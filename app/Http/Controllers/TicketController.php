<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\User;
use App\Notifications\TicketDownloadNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class TicketController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'quantity' => 'required|integer|min:1|max:10',
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'attendee_phone' => 'nullable|string|max:255',
        ]);

        $ticketType = TicketType::findOrFail($request->ticket_type_id);
        
        // Controleer of tickettype bij dit event hoort
        if ($ticketType->event_id !== $event->id) {
            return back()->withErrors(['ticket_type_id' => 'Ongeldig ticket type.']);
        }

        // Controleer beschikbaarheid
        if ($ticketType->quantity <= $ticketType->sold_quantity) {
            return back()->withErrors(['quantity' => 'Dit ticket type is uitverkocht.']);
        }

        if (($ticketType->quantity - $ticketType->sold_quantity) < $request->quantity) {
            return back()->withErrors(['quantity' => 'Niet genoeg tickets beschikbaar.']);
        }

        // Maak ticket aan in database transaction
        DB::beginTransaction();
        try {
            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'ticket_type_id' => $ticketType->id,
                'attendee_name' => $request->attendee_name,
                'attendee_email' => $request->attendee_email,
                'attendee_phone' => $request->attendee_phone,
                'status' => 'pending',
                'amount_paid' => $ticketType->price * $request->quantity,
                'qr_code' => 'TICKET-' . strtoupper(uniqid()),
            ]);

            // Update sold_quantity
            $ticketType->increment('sold_quantity', $request->quantity);

            DB::commit();

            return redirect()->route('checkout.show', $ticket);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Er is een fout opgetreden. Probeer het opnieuw.']);
        }
    }

    public function myTickets(): View
    {
        $tickets = auth()->user()->tickets()
            ->with(['ticketType.event.organisation'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.my-tickets', compact('tickets'));
    }

    public function download(Ticket $ticket): \Symfony\Component\HttpFoundation\Response
    {
        // Controleer of gebruiker eigenaar is van het ticket
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        // Genereer PDF ticket
        $pdf = \PDF::loadView('tickets.pdf', compact('ticket'));
        
        // Stuur download notificatie email
        $user = User::find($ticket->user_id);
        if ($user) {
            $user->notify(new TicketDownloadNotification($ticket));
        }
        
        return $pdf->download("ticket-{$ticket->qr_code}.pdf");
    }
} 