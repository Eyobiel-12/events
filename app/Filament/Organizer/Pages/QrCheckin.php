<?php

declare(strict_types=1);

namespace App\Filament\Organizer\Pages;

use App\Models\Event;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

final class QrCheckin extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    protected static string $view = 'filament.organizer.pages.qr-checkin';

    protected static ?string $title = 'QR Check-in';

    protected static ?string $navigationGroup = 'Event Management';

    protected static ?int $navigationSort = 5;

    public ?Event $selectedEvent = null;

    public function mount(): void
    {
        $user = Auth::user();
        $organisationId = $user->organisations()->first()?->id;
        
        // Laad het eerste event van de organisatie als default
        $this->selectedEvent = Event::where('organisation_id', $organisationId)
            ->where('status', 'published')
            ->orderBy('start_date')
            ->first();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('select_event')
                ->label('Select Event')
                ->form([
                    \Filament\Forms\Components\Select::make('event_id')
                        ->label('Event')
                        ->options(function () {
                            $user = Auth::user();
                            $organisationId = $user->organisations()->first()?->id;
                            
                            return Event::where('organisation_id', $organisationId)
                                ->where('status', 'published')
                                ->pluck('title', 'id');
                        })
                        ->required()
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data): void {
                    $this->selectedEvent = Event::find($data['event_id']);
                })
                ->modalHeading('Select Event for QR Check-in')
                ->modalDescription('Choose which event to perform QR check-ins for.')
                ->modalSubmitActionLabel('Select Event'),
        ];
    }

    public function getEvents(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();
        $organisationId = $user->organisations()->first()?->id;
        
        return Event::where('organisation_id', $organisationId)
            ->where('status', 'published')
            ->orderBy('start_date')
            ->get();
    }

    public function getCheckinStats(): array
    {
        if (!$this->selectedEvent) {
            return [
                'total_tickets' => 0,
                'checked_in' => 0,
                'not_checked_in' => 0,
                'percentage' => 0,
            ];
        }

        $totalTickets = $this->selectedEvent->tickets()->where('status', 'paid')->count();
        $checkedIn = $this->selectedEvent->tickets()->where('status', 'paid')->whereNotNull('checked_in_at')->count();
        $notCheckedIn = $totalTickets - $checkedIn;
        $percentage = $totalTickets > 0 ? round(($checkedIn / $totalTickets) * 100, 1) : 0;

        return [
            'total_tickets' => $totalTickets,
            'checked_in' => $checkedIn,
            'not_checked_in' => $notCheckedIn,
            'percentage' => $percentage,
        ];
    }
} 