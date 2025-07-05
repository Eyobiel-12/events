<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Feedback;
use App\Models\Organisation;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class PlatformStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Totaal Organisaties', number_format(Organisation::count()))
                ->description('Aantal tenants op het platform')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('blue'),

            Stat::make('Totaal Events', number_format(Event::count()))
                ->description('Aantal events platform-breed')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('green'),

            Stat::make('Totaal Tickets', number_format(Ticket::count()))
                ->description('Verkochte tickets platform-breed')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('yellow'),

            Stat::make('Platform Omzet', '€' . number_format(Transaction::where('status', 'completed')->sum('amount') ?? 0, 2))
                ->description('Totale omzet van alle transacties')
                ->descriptionIcon('heroicon-m-currency-euro')
                ->color('purple'),

            Stat::make('Actieve Gebruikers', number_format(User::count()))
                ->description('Aantal geregistreerde gebruikers')
                ->descriptionIcon('heroicon-m-users')
                ->color('indigo'),

            Stat::make('Feedback Score', number_format(Feedback::avg('rating') ?? 0, 1) . '/5')
                ->description('Gemiddelde feedback score')
                ->descriptionIcon('heroicon-m-star')
                ->color('orange'),
        ];
    }
} 