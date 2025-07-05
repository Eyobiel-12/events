<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders {--dry-run : Test run without sending emails}';

    protected $description = 'Send event reminders to attendees 24 hours before events';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No emails will be sent');
        }

        $tomorrow = now()->addDay()->startOfDay();
        
        // Zoek events die morgen plaatsvinden
        $events = Event::where('status', 'published')
            ->whereDate('start_date', $tomorrow)
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events scheduled for tomorrow.');
            return self::SUCCESS;
        }

        $totalReminders = 0;
        $sentReminders = 0;

        foreach ($events as $event) {
            $this->info("Processing event: {$event->title}");

            // Zoek alle betaalde tickets voor dit event
            $tickets = Ticket::whereHas('ticketType', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->where('status', 'paid')
            ->whereNull('checked_in_at') // Alleen tickets die nog niet zijn gecheckt-in
            ->get();

            $totalReminders += $tickets->count();

            foreach ($tickets as $ticket) {
                $user = User::find($ticket->user_id);
                
                if (!$user) {
                    $this->warn("User not found for ticket {$ticket->id}");
                    continue;
                }

                if (!$isDryRun) {
                    try {
                        $user->notify(new EventReminderNotification(
                            $event,
                            $ticket->attendee_name,
                            $ticket->attendee_email
                        ));
                        
                        $sentReminders++;
                        $this->line("✓ Reminder sent to {$ticket->attendee_name} ({$ticket->attendee_email})");
                        
                        Log::info('Event reminder sent', [
                            'event_id' => $event->id,
                            'event_title' => $event->title,
                            'ticket_id' => $ticket->id,
                            'attendee_name' => $ticket->attendee_name,
                            'attendee_email' => $ticket->attendee_email,
                        ]);
                        
                    } catch (\Exception $e) {
                        $this->error("Failed to send reminder to {$ticket->attendee_name}: {$e->getMessage()}");
                        
                        Log::error('Event reminder failed', [
                            'event_id' => $event->id,
                            'ticket_id' => $ticket->id,
                            'attendee_email' => $ticket->attendee_email,
                            'error' => $e->getMessage(),
                        ]);
                    }
                } else {
                    $this->line("DRY RUN: Would send reminder to {$ticket->attendee_name} ({$ticket->attendee_email})");
                    $sentReminders++;
                }
            }
        }

        $this->info("Processed {$totalReminders} total reminders");
        $this->info("Sent {$sentReminders} reminders successfully");

        return self::SUCCESS;
    }
}
