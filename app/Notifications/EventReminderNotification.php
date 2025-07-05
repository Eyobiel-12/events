<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Event $event,
        private string $attendeeName,
        private string $attendeeEmail
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Event Herinnering - {$this->event->title}")
            ->greeting("Hallo {$this->attendeeName}!")
            ->line("Dit is een herinnering voor het event van morgen.")
            ->line("**Event Details:**")
            ->line("📅 **Event:** {$this->event->title}")
            ->line("🕒 **Datum & Tijd:** {$this->event->start_date->format('d M Y H:i')}")
            ->line("📍 **Locatie:** {$this->event->location}, {$this->event->city}")
            ->line("")
            ->line("**Belangrijke Informatie:**")
            ->line("• Neem je ticket mee (digitaal of geprint)")
            ->line("• Kom op tijd - check-in start 30 minuten voor het event")
            ->line("• Toon je QR code bij de ingang")
            ->line("• Parkeren is beschikbaar op locatie")
            ->line("")
            ->line("**Event Beschrijving:**")
            ->line($this->event->description)
            ->line("")
            ->line("**Contact:**")
            ->line("Heb je vragen? Neem contact op met de organisatie.")
            ->action('Bekijk Event Details', route('events.show', $this->event))
            ->salutation("Tot morgen!");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'attendee_name' => $this->attendeeName,
            'attendee_email' => $this->attendeeEmail,
            'event_date' => $this->event->start_date->format('Y-m-d H:i:s'),
        ];
    }
}
