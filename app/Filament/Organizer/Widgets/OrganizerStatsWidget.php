<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Widgets;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\Vendor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class OrganizerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $organisationId = $user->organisations()->first()?->id;

        if (!$organisationId) {
            return [
                Stat::make('Events', '0')
                    ->description('No organization found')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }

        $totalEvents = Event::where('organisation_id', $organisationId)->count();
        $activeEvents = Event::where('organisation_id', $organisationId)
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->count();
        
        $totalTickets = Ticket::whereHas('ticketType.event', function ($query) use ($organisationId) {
            $query->where('organisation_id', $organisationId);
        })->count();
        
        $soldTickets = Ticket::whereHas('ticketType.event', function ($query) use ($organisationId) {
            $query->where('organisation_id', $organisationId);
        })->where('status', 'sold')->count();
        
        $totalRevenue = Ticket::whereHas('ticketType.event', function ($query) use ($organisationId) {
            $query->where('organisation_id', $organisationId);
        })->where('status', 'sold')
        ->join('ticket_types', 'tickets.ticket_type_id', '=', 'ticket_types.id')
        ->sum('ticket_types.price');
        
        $totalVendors = Vendor::where('organisation_id', $organisationId)->count();
        $activeVendors = Vendor::where('organisation_id', $organisationId)
            ->where('status', 'active')
            ->count();

        return [
            Stat::make('Total Events', $totalEvents)
                ->description('All events created')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            
            Stat::make('Active Events', $activeEvents)
                ->description('Upcoming published events')
                ->descriptionIcon('heroicon-m-play')
                ->color('success'),
            
            Stat::make('Total Tickets', $totalTickets)
                ->description('All tickets created')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
            
            Stat::make('Sold Tickets', $soldTickets)
                ->description('Tickets sold')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Total Revenue', '€' . number_format($totalRevenue, 2))
                ->description('Revenue from sold tickets')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('warning'),
            
            Stat::make('Total Vendors', $totalVendors)
                ->description('All vendors registered')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            
            Stat::make('Active Vendors', $activeVendors)
                ->description('Active vendor accounts')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }
}
