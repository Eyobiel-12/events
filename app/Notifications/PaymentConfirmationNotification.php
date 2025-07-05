<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

final class PaymentConfirmationNotification extends Notification implements ShouldQueue
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
        // Genereer PDF ticket
        $pdf = Pdf::loadView('tickets.pdf', ['ticket' => $this->ticket]);
        $pdfContent = $pdf->output();
        
        $filename = "ticket-{$this->ticket->qr_code}.pdf";

        return (new MailMessage)
            ->subject("Betaling Bevestigd - {$this->ticket->ticketType->event->title}")
            ->greeting("Hallo {$this->ticket->attendee_name}!")
            ->line("Je betaling is succesvol verwerkt.")
            ->line("**Event Details:**")
            ->line("📅 **Event:** {$this->ticket->ticketType->event->title}")
            ->line("🕒 **Datum & Tijd:** {$this->ticket->ticketType->event->start_date->format('d M Y H:i')}")
            ->line("📍 **Locatie:** {$this->ticket->ticketType->event->location}, {$this->ticket->ticketType->event->city}")
            ->line("🎫 **Ticket Type:** {$this->ticket->ticketType->name}")
            ->line("💰 **Betaald Bedrag:** €{$this->ticket->amount_paid}")
            ->line("🆔 **Ticket ID:** {$this->ticket->qr_code}")
            ->line("")
            ->line("**Belangrijke Informatie:**")
            ->line("• Bewaar je ticket goed - je hebt deze nodig voor toegang")
            ->line("• Toon de QR code bij de ingang van het event")
            ->line("• Je kunt je ticket ook downloaden via je account")
            ->line("")
            ->line("**Event Details:**")
            ->line($this->ticket->ticketType->event->description)
            ->line("")
            ->line("Heb je vragen? Neem contact op met de organisatie.")
            ->action('Bekijk Mijn Tickets', route('tickets.my-tickets'))
            ->attachData($pdfContent, $filename, [
                'mime' => 'application/pdf',
            ])
            ->salutation("Veel plezier bij het event!");
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'event_title' => $this->ticket->ticketType->event->title,
            'attendee_name' => $this->ticket->attendee_name,
            'amount_paid' => $this->ticket->amount_paid,
            'qr_code' => $this->ticket->qr_code,
        ];
    }
}
