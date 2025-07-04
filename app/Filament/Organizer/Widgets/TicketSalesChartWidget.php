<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Widgets;

use App\Models\Ticket;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

final class TicketSalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Ticket Sales (Last 30 Days)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = auth()->user();
        $organisationId = $user->organisations()->first()?->id;

        if (!$organisationId) {
            return [
                'datasets' => [
                    [
                        'label' => 'Tickets Sold',
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $days = collect();
        $sales = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days->push($date->format('M d'));
            
            $count = Ticket::whereHas('ticketType.event', function ($query) use ($organisationId) {
                $query->where('organisation_id', $organisationId);
            })
            ->where('status', 'sold')
            ->whereDate('created_at', $date)
            ->count();
            
            $sales->push($count);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Sold',
                    'data' => $sales->toArray(),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
} 