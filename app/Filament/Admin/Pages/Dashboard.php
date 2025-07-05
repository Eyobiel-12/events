<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Models\Organisation;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Feedback;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Request;

final class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.admin.pages.dashboard';

    public $stats = [];
    public $chartData = [];
    public $topEvents = [];
    public $trends = [];
    public $scope = 'platform'; // 'platform' of 'organisator'

    public function mount(): void
    {
        $this->scope = request()->get('scope', 'platform');
        $orgId = null;
        if ($this->scope === 'organisator' && auth()->user()->hasRole('organizer')) {
            $orgId = auth()->user()->organisations()->first()?->id;
        }

        $this->stats = $this->getStats($orgId);
        $this->trends = $this->getTrends($orgId);
        $this->chartData = [
            'monthly_revenue' => $this->getMonthlyRevenue($orgId),
        ];
        $this->topEvents = $this->getTopEvents($orgId);
    }

    private function getStats($orgId = null): array
    {
        return [
            'organisations' => $orgId ? 1 : Organisation::count(),
            'events' => $orgId ? Event::where('organisation_id', $orgId)->count() : Event::count(),
            'tickets' => $orgId ? Ticket::whereHas('ticketType.event', fn($q) => $q->where('organisation_id', $orgId))->count() : Ticket::count(),
            'revenue' => $orgId
                ? Transaction::whereHas('ticket.event', fn($q) => $q->where('organisation_id', $orgId))->where('status', 'completed')->sum('amount') ?? 0
                : Transaction::where('status', 'completed')->sum('amount') ?? 0,
            'users' => $orgId ? User::whereHas('organisations', fn($q) => $q->where('organisation_id', $orgId))->count() : User::count(),
            'feedback_score' => $orgId
                ? Feedback::whereHas('event', fn($q) => $q->where('organisation_id', $orgId))->avg('rating') ?? 0
                : Feedback::avg('rating') ?? 0,
        ];
    }

    private function getTrends($orgId = null): array
    {
        // Vergelijk huidige maand met vorige maand
        $now = now();
        $thisMonth = $now->format('Y-m');
        $lastMonth = $now->copy()->subMonth()->format('Y-m');
        $monthly = $this->getMonthlyRevenue($orgId);
        $current = $monthly[$thisMonth] ?? 0;
        $previous = $monthly[$lastMonth] ?? 0;
        $trend = $previous == 0 ? null : round((($current - $previous) / ($previous ?: 1)) * 100, 1);
        return [
            'revenue' => $trend,
            // Je kunt hier ook trends voor andere KPI's toevoegen
        ];
    }

    private function getMonthlyRevenue($orgId = null): array
    {
        $query = Transaction::where('status', 'completed');
        if ($orgId) {
            $query->whereHas('ticket.event', fn($q) => $q->where('organisation_id', $orgId));
        }
        $data = $query
            ->where('created_at', '>=', now()->subMonths(12))
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $result = [];
        foreach ($data as $row) {
            $result[$row->month] = $row->total;
        }
        return $result;
    }

    private function getTopEvents($orgId = null)
    {
        $query = Event::withCount('tickets');
        if ($orgId) {
            $query->where('organisation_id', $orgId);
        }
        return $query->orderByDesc('tickets_count')->limit(3)->get(['id', 'title']);
    }

    protected function getViewData(): array
    {
        return [
            'stats' => $this->stats,
            'chartData' => $this->chartData,
            'topEvents' => $this->topEvents,
            'trends' => $this->trends,
            'scope' => $this->scope,
        ];
    }
} 