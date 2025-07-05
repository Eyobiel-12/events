<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Ticket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

final class QrCodeService
{
    public function generateTicketQrCode(Ticket $ticket): string
    {
        $qrData = [
            'ticket_id' => $ticket->id,
            'qr_code' => $ticket->qr_code,
            'event_id' => $ticket->ticketType->event->id,
            'attendee_name' => $ticket->attendee_name,
            'timestamp' => now()->timestamp,
        ];

        $qrString = json_encode($qrData);
        
        return QrCode::format('png')
            ->size(300)
            ->margin(10)
            ->generate($qrString);
    }

    public function generateQrCodeSvg(Ticket $ticket): string
    {
        $qrData = [
            'ticket_id' => $ticket->id,
            'qr_code' => $ticket->qr_code,
            'event_id' => $ticket->ticketType->event->id,
            'attendee_name' => $ticket->attendee_name,
            'timestamp' => now()->timestamp,
        ];

        $qrString = json_encode($qrData);
        
        return QrCode::format('svg')
            ->size(300)
            ->margin(10)
            ->generate($qrString);
    }

    public function validateQrCode(string $qrCode): ?array
    {
        try {
            $data = json_decode($qrCode, true);
            
            if (!$data || !isset($data['ticket_id'], $data['qr_code'])) {
                return null;
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function generateQrCodeUrl(Ticket $ticket): string
    {
        return route('tickets.verify', ['qr_code' => $ticket->qr_code]);
    }
} 