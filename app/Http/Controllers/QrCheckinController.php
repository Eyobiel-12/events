<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketScan;
use App\Models\Event;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class QrCheckinController extends Controller
{
    public function __construct(
        private QrCodeService $qrCodeService
    ) {}

    public function show(Event $event): View
    {
        // Controleer of gebruiker toegang heeft tot dit event
        $user = auth()->user();
        $organisationId = $user->organisations()->first()?->id;
        
        if ($event->organisation_id !== $organisationId && !$user->hasRole('admin')) {
            abort(403);
        }

        return view('qr-checkin.show', compact('event'));
    }

    public function scan(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'qr_data' => 'required|string',
        ]);

        try {
            // Valideer QR code data
            $qrData = $this->qrCodeService->validateQrCode($request->qr_data);
            
            if (!$qrData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ongeldige QR code',
                    'type' => 'invalid'
                ]);
            }

            // Zoek ticket op basis van QR code
            $ticket = Ticket::where('qr_code', $qrData['qr_code'])
                ->whereHas('ticketType.event', function ($query) use ($event) {
                    $query->where('id', $event->id);
                })
                ->with(['ticketType.event', 'user'])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket niet gevonden voor dit event',
                    'type' => 'not_found'
                ]);
            }

            // Controleer ticket status
            if ($ticket->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket is niet betaald',
                    'type' => 'not_paid'
                ]);
            }

            // Controleer of ticket al is gebruikt
            if ($ticket->checked_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket is al gebruikt',
                    'type' => 'already_used',
                    'ticket' => [
                        'attendee_name' => $ticket->attendee_name,
                        'checked_in_at' => $ticket->checked_in_at->format('d M Y H:i'),
                        'checked_in_by' => $ticket->checked_in_by,
                    ]
                ]);
            }

            // Check-in ticket
            DB::beginTransaction();
            try {
                $ticket->update([
                    'checked_in_at' => now(),
                    'checked_in_by' => auth()->user()->name,
                ]);

                // Maak scan record aan
                TicketScan::create([
                    'ticket_id' => $ticket->id,
                    'event_id' => $event->id,
                    'scanned_by' => auth()->id(),
                    'scanned_at' => now(),
                    'location' => $request->input('location', 'Main Entrance'),
                    'status' => 'valid',
                    'notes' => 'QR code scan',
                ]);

                DB::commit();

                Log::info('Ticket checked in', [
                    'ticket_id' => $ticket->id,
                    'event_id' => $event->id,
                    'scanned_by' => auth()->user()->name,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket succesvol gecheckt-in',
                    'type' => 'success',
                    'ticket' => [
                        'attendee_name' => $ticket->attendee_name,
                        'attendee_email' => $ticket->attendee_email,
                        'ticket_type' => $ticket->ticketType->name,
                        'checked_in_at' => now()->format('d M Y H:i'),
                        'checked_in_by' => auth()->user()->name,
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Check-in failed', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Er is een fout opgetreden bij het check-in',
                    'type' => 'error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('QR scan error', [
                'error' => $e->getMessage(),
                'qr_data' => $request->qr_data
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden',
                'type' => 'error'
            ]);
        }
    }

    public function manualCheckin(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'ticket_id' => 'required|string',
        ]);

        try {
            // Zoek ticket op basis van ID of QR code
            $ticket = Ticket::where('id', $request->ticket_id)
                ->orWhere('qr_code', $request->ticket_id)
                ->whereHas('ticketType.event', function ($query) use ($event) {
                    $query->where('id', $event->id);
                })
                ->with(['ticketType.event', 'user'])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket niet gevonden',
                    'type' => 'not_found'
                ]);
            }

            // Controleer ticket status
            if ($ticket->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket is niet betaald',
                    'type' => 'not_paid'
                ]);
            }

            // Controleer of ticket al is gebruikt
            if ($ticket->checked_in_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket is al gebruikt',
                    'type' => 'already_used',
                    'ticket' => [
                        'attendee_name' => $ticket->attendee_name,
                        'checked_in_at' => $ticket->checked_in_at->format('d M Y H:i'),
                        'checked_in_by' => $ticket->checked_in_by,
                    ]
                ]);
            }

            // Check-in ticket
            DB::beginTransaction();
            try {
                $ticket->update([
                    'checked_in_at' => now(),
                    'checked_in_by' => auth()->user()->name,
                ]);

                // Maak scan record aan
                TicketScan::create([
                    'ticket_id' => $ticket->id,
                    'event_id' => $event->id,
                    'scanned_by' => auth()->id(),
                    'scanned_at' => now(),
                    'location' => $request->input('location', 'Manual Entry'),
                    'status' => 'valid',
                    'notes' => 'Manual check-in',
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Ticket succesvol gecheckt-in',
                    'type' => 'success',
                    'ticket' => [
                        'attendee_name' => $ticket->attendee_name,
                        'attendee_email' => $ticket->attendee_email,
                        'ticket_type' => $ticket->ticketType->name,
                        'checked_in_at' => now()->format('d M Y H:i'),
                        'checked_in_by' => auth()->user()->name,
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Er is een fout opgetreden bij het check-in',
                    'type' => 'error'
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden',
                'type' => 'error'
            ]);
        }
    }

    public function verify(string $qrCode): JsonResponse
    {
        try {
            $ticket = Ticket::where('qr_code', $qrCode)
                ->with(['ticketType.event'])
                ->first();

            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket niet gevonden',
                ]);
            }

            return response()->json([
                'success' => true,
                'ticket' => [
                    'id' => $ticket->id,
                    'attendee_name' => $ticket->attendee_name,
                    'attendee_email' => $ticket->attendee_email,
                    'event_title' => $ticket->ticketType->event->title,
                    'ticket_type' => $ticket->ticketType->name,
                    'status' => $ticket->status,
                    'checked_in_at' => $ticket->checked_in_at?->format('d M Y H:i'),
                    'checked_in_by' => $ticket->checked_in_by,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Er is een fout opgetreden',
            ]);
        }
    }
} 