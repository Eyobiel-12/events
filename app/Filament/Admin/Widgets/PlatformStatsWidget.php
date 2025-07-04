<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Organisation;
use App\Models\Event;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $orgCount = Organisation::count();
        $eventCount = Event::count();
        $ticketCount = Ticket::count();
        $revenue = Ticket::where('status', 'paid')->sum('amount_paid');

        return [
            Stat::make('Organisaties', $orgCount),
            Stat::make('Events', $eventCount),
            Stat::make('Tickets verkocht', $ticketCount),
            Stat::make('Totale omzet', '€ ' . number_format($revenue, 2, ',', '.')),
        ];
    }
}
