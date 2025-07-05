<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

final class RevenueChart extends ChartWidget
{
    protected static ?string $heading = 'Maandelijkse Omzet';

    protected function getData(): array
    {
        $user = Auth::user();
        $organisation = $user->organisations()->first();

        if (!$organisation) {
            return [
                'datasets' => [
                    [
                        'label' => 'Omzet (€)',
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $monthlyRevenue = Transaction::whereHas('ticket.event.organisation', function ($query) use ($organisation) {
            $query->where('id', $organisation->id);
        })
        ->where('status', 'completed')
        ->where('created_at', '>=', now()->subMonths(12))
        ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $labels = $monthlyRevenue->pluck('month')->toArray();
        $data = $monthlyRevenue->pluck('total')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Omzet (€)',
                    'data' => $data,
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
} 