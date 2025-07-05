<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\DashboardStatsUpdated;

final class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        
        // Basis statistieken
        $stats = $this->getBasicStats($user);
        
        // Grafiek data
        $chartData = $this->getChartData($user);
        
        // Recente activiteiten
        $recentActivities = $this->getRecentActivities($user);
        
        // Dispatch real-time update event
        if ($user->organisations()->first()) {
            event(new DashboardStatsUpdated($stats, $user->organisations()->first()->id));
        }
        
        return view('dashboard.index', compact('stats', 'chartData', 'recentActivities'));
    }

    private function getBasicStats($user): array
    {
        $organisation = $user->organisations()->first();
        
        if (!$organisation) {
            return [
                'total_events' => 0,
                'total_tickets' => 0,
                'total_revenue' => 0,
                'total_feedback' => 0,
                'upcoming_events' => 0,
                'completed_events' => 0,
            ];
        }

        $events = Event::where('organisation_id', $organisation->id);
        
        return [
            'total_events' => $events->count(),
            'total_tickets' => Ticket::whereHas('ticketType.event', function ($query) use ($organisation) {
                $query->where('organisation_id', $organisation->id);
            })->count(),
            'total_revenue' => Transaction::whereHas('ticket.event.organisation', function ($query) use ($organisation) {
                $query->where('id', $organisation->id);
            })->where('status', 'completed')->sum('amount'),
            'total_feedback' => Feedback::whereHas('event.organisation', function ($query) use ($organisation) {
                $query->where('id', $organisation->id);
            })->count(),
            'upcoming_events' => $events->where('start_date', '>', now())->count(),
            'completed_events' => $events->where('end_date', '<', now())->count(),
        ];
    }

    private function getChartData($user): array
    {
        $organisation = $user->organisations()->first();
        
        if (!$organisation) {
            return [
                'monthly_revenue' => [],
                'ticket_sales' => [],
                'event_ratings' => [],
            ];
        }

        // Maandelijkse omzet (laatste 12 maanden)
        $monthlyRevenue = Transaction::whereHas('ticket.event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })
        ->where('status', 'completed')
        ->where('created_at', '>=', now()->subMonths(12))
        ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->month => $item->total];
        });

        // Ticket verkopen per event
        $ticketSales = Event::where('organisation_id', $organisation->id)
        ->withCount(['tickets as total_tickets'])
        ->orderBy('start_date', 'desc')
        ->limit(10)
        ->get()
        ->mapWithKeys(function ($event) {
            return [$event->title => $event->total_tickets];
        });

        // Event ratings
        $eventRatings = Event::where('organisation_id', $organisation->id)
        ->withAvg('feedback as average_rating')
        ->whereHas('feedback')
        ->orderBy('start_date', 'desc')
        ->limit(10)
        ->get()
        ->mapWithKeys(function ($event) {
            return [$event->title => round($event->average_rating, 1)];
        });

        return [
            'monthly_revenue' => $monthlyRevenue,
            'ticket_sales' => $ticketSales,
            'event_ratings' => $eventRatings,
        ];
    }

    private function getRecentActivities($user): array
    {
        $organisation = $user->organisations()->first();
        
        if (!$organisation) {
            return [];
        }

        $activities = collect();

        // Recente events
        $recentEvents = Event::where('organisation_id', $organisation->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentEvents as $event) {
            $activities->push([
                'type' => 'event_created',
                'title' => "Event aangemaakt: {$event->title}",
                'date' => $event->created_at,
                'icon' => 'calendar',
                'color' => 'blue',
            ]);
        }

        // Recente ticket verkopen
        $recentTickets = Ticket::whereHas('ticketType.event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        foreach ($recentTickets as $ticket) {
            $activities->push([
                'type' => 'ticket_sold',
                'title' => "Ticket verkocht voor {$ticket->ticketType->event->title}",
                'date' => $ticket->created_at,
                'icon' => 'ticket',
                'color' => 'green',
            ]);
        }

        // Recente feedback
        $recentFeedback = Feedback::whereHas('event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

        foreach ($recentFeedback as $feedback) {
            $activities->push([
                'type' => 'feedback_received',
                'title' => "Feedback ontvangen voor {$feedback->event->title}",
                'date' => $feedback->created_at,
                'icon' => 'star',
                'color' => 'yellow',
            ]);
        }

        return $activities->sortByDesc('date')->take(10)->values()->toArray();
    }

    public function export(Request $request): Response
    {
        $user = auth()->user();
        $organisation = $user->organisations()->first();
        
        if (!$organisation) {
            abort(404);
        }

        $format = $request->get('format', 'csv');
        $type = $request->get('type', 'events');

        switch ($type) {
            case 'events':
                $data = $this->exportEvents($organisation);
                break;
            case 'tickets':
                $data = $this->exportTickets($organisation);
                break;
            case 'revenue':
                $data = $this->exportRevenue($organisation);
                break;
            case 'feedback':
                $data = $this->exportFeedback($organisation);
                break;
            default:
                abort(400);
        }

        if ($format === 'pdf') {
            return $this->exportToPdf($data, $type);
        }

        return $this->exportToCsv($data, $type);
    }

    private function exportEvents($organisation): array
    {
        return Event::where('organisation_id', $organisation->id)
            ->with(['organisation'])
            ->get()
            ->map(function ($event) {
                return [
                    'ID' => $event->id,
                    'Titel' => $event->title,
                    'Beschrijving' => $event->description,
                    'Locatie' => $event->location,
                    'Stad' => $event->city,
                    'Start Datum' => $event->start_date->format('d-m-Y H:i'),
                    'Eind Datum' => $event->end_date->format('d-m-Y H:i'),
                    'Status' => $event->status,
                    'Aangemaakt' => $event->created_at->format('d-m-Y H:i'),
                ];
            })
            ->toArray();
    }

    private function exportTickets($organisation): array
    {
        return Ticket::whereHas('ticketType.event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })
        ->with(['ticketType.event', 'user'])
        ->get()
        ->map(function ($ticket) {
            return [
                'ID' => $ticket->id,
                'Event' => $ticket->ticketType->event->title,
                'Ticket Type' => $ticket->ticketType->name,
                'Attendee' => $ticket->attendee_name,
                'Email' => $ticket->attendee_email,
                'Status' => $ticket->status,
                'Prijs' => '€' . number_format($ticket->amount_paid, 2),
                'Aangemaakt' => $ticket->created_at->format('d-m-Y H:i'),
            ];
        })
        ->toArray();
    }

    private function exportRevenue($organisation): array
    {
        return Transaction::whereHas('ticket.event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })
        ->with(['ticket.ticketType.event'])
        ->get()
        ->map(function ($transaction) {
            return [
                'ID' => $transaction->id,
                'Event' => $transaction->ticket->event->title,
                'Ticket' => $transaction->ticket->ticketType->name,
                'Attendee' => $transaction->ticket->attendee_name,
                'Bedrag' => '€' . number_format($transaction->amount, 2),
                'Status' => $transaction->status,
                'Betaald Op' => $transaction->paid_at ? $transaction->paid_at->format('d-m-Y H:i') : '-',
                'Aangemaakt' => $transaction->created_at->format('d-m-Y H:i'),
            ];
        })
        ->toArray();
    }

    private function exportFeedback($organisation): array
    {
        return Feedback::whereHas('event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })
        ->with(['event'])
        ->get()
        ->map(function ($feedback) {
            return [
                'ID' => $feedback->id,
                'Event' => $feedback->event->title,
                'Attendee' => $feedback->attendee_name,
                'Email' => $feedback->attendee_email,
                'Rating' => $feedback->rating . '/5',
                'Comment' => $feedback->comment,
                'Status' => $feedback->status,
                'Aangemaakt' => $feedback->created_at->format('d-m-Y H:i'),
            ];
        })
        ->toArray();
    }

    private function exportToCsv(array $data, string $type): Response
    {
        if (empty($data)) {
            return response('Geen data beschikbaar', 404);
        }

        $headers = array_keys($data[0]);
        $filename = "{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';

        $output = fopen('php://temp', 'r+');
        
        // UTF-8 BOM voor Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // Headers
        fputcsv($output, $headers);
        
        // Data
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    private function exportToPdf(array $data, string $type): Response
    {
        if (empty($data)) {
            return response('Geen data beschikbaar', 404);
        }

        $headers = array_keys($data[0]);
        $filename = "{$type}_" . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = Pdf::loadView('dashboard.export-pdf', [
            'data' => $data,
            'headers' => $headers,
            'type' => $type,
        ]);

        return $pdf->download($filename);
    }
}
