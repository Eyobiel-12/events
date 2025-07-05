<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Feedback;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

final class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $organisation = $user->organisations()->first();

        if (!$organisation) {
            return [
                Stat::make('Totaal Events', '0')
                    ->description('Aantal events')
                    ->descriptionIcon('heroicon-m-calendar')
                    ->color('blue'),
                Stat::make('Totaal Tickets', '0')
                    ->description('Verkochte tickets')
                    ->descriptionIcon('heroicon-m-ticket')
                    ->color('green'),
                Stat::make('Totaal Omzet', '€0.00')
                    ->description('Totale omzet')
                    ->descriptionIcon('heroicon-m-currency-euro')
                    ->color('yellow'),
                Stat::make('Feedback', '0')
                    ->description('Ontvangen feedback')
                    ->descriptionIcon('heroicon-m-star')
                    ->color('purple'),
            ];
        }

        $totalEvents = Event::where('organisation_id', $organisation->id)->count();
        $totalTickets = Ticket::whereHas('ticketType.event', function ($query) use ($organisation) {
            $query->where('organisation_id', $organisation->id);
        })->count();
        $totalRevenue = Transaction::whereHas('ticket.event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })->where('status', 'completed')->sum('amount');
        $totalFeedback = Feedback::whereHas('event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })->count();

        return [
            Stat::make('Totaal Events', number_format($totalEvents))
                ->description('Aantal events')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('blue'),
            Stat::make('Totaal Tickets', number_format($totalTickets))
                ->description('Verkochte tickets')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('green'),
            Stat::make('Totaal Omzet', '€' . number_format($totalRevenue, 2))
                ->description('Totale omzet')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('yellow'),
            Stat::make('Feedback', number_format($totalFeedback))
                ->description('Ontvangen feedback')
                ->descriptionIcon('heroicon-m-star')
                ->color('purple'),
        ];
    }
} 