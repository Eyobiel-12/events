<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TicketDownloadNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Ticket $ticket
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Ticket Download - {$this->ticket->ticketType->event->title}")
            ->greeting("Hallo {$this->ticket->attendee_name}!")
            ->line("Je ticket is succesvol gedownload.")
            ->line("**Event Details:**")
            ->line("📅 **Event:** {$this->ticket->ticketType->event->title}")
            ->line("🕒 **Datum & Tijd:** {$this->ticket->ticketType->event->start_date->format('d M Y H:i')}")
            ->line("📍 **Locatie:** {$this->ticket->ticketType->event->location}, {$this->ticket->ticketType->event->city}")
            ->line("🎫 **Ticket Type:** {$this->ticket->ticketType->name}")
            ->line("🆔 **Ticket ID:** {$this->ticket->qr_code}")
            ->line("")
            ->line("**Herinnering:**")
            ->line("• Bewaar je ticket goed")
            ->line("• Toon de QR code bij de ingang")
            ->line("• Je kunt je ticket opnieuw downloaden via je account")
            ->line("")
            ->action('Bekijk Mijn Tickets', route('tickets.my-tickets'))
            ->salutation("Veel plezier bij het event!");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'event_title' => $this->ticket->ticketType->event->title,
            'attendee_name' => $this->ticket->attendee_name,
            'qr_code' => $this->ticket->qr_code,
        ];
    }
}
