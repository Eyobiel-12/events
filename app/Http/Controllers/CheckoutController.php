<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Mollie\Laravel\Facades\Mollie;

final class CheckoutController extends Controller
{
    public function show(Ticket $ticket): View
    {
        // Controleer of gebruiker eigenaar is van het ticket
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        // Controleer of ticket nog pending is
        if ($ticket->status !== 'pending') {
            return redirect()->route('tickets.my-tickets')
                ->withErrors(['error' => 'Dit ticket is al betaald of geannuleerd.']);
        }

        return view('checkout.show', compact('ticket'));
    }

    public function process(Request $request, Ticket $ticket): RedirectResponse
    {
        // Controleer of gebruiker eigenaar is van het ticket
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        // Controleer of ticket nog pending is
        if ($ticket->status !== 'pending') {
            return redirect()->route('tickets.my-tickets')
                ->withErrors(['error' => 'Dit ticket is al betaald of geannuleerd.']);
        }

        $request->validate([
            'payment_method' => 'required|in:ideal,creditcard,paypal',
        ]);

        try {
            // Maak Mollie betaling aan
            $payment = Mollie::api()->payments->create([
                'amount' => [
                    'currency' => 'EUR',
                    'value' => number_format($ticket->amount_paid, 2, '.', ''),
                ],
                'description' => "Ticket voor {$ticket->ticketType->event->title}",
                'redirectUrl' => route('checkout.success', $ticket),
                'webhookUrl' => route('webhook.mollie'),
                'metadata' => [
                    'ticket_id' => $ticket->id,
                    'user_id' => auth()->id(),
                ],
            ]);

            // Sla transaction op
            Transaction::create([
                'ticket_id' => $ticket->id,
                'payment_id' => $payment->id,
                'amount' => $ticket->amount_paid,
                'currency' => 'EUR',
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'metadata' => [
                    'mollie_payment_id' => $payment->id,
                ],
            ]);

            // Update ticket met payment_id
            $ticket->update([
                'payment_id' => $payment->id,
            ]);

            // Redirect naar Mollie betaalpagina
            return redirect($payment->getCheckoutUrl());

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Er is een fout opgetreden bij het aanmaken van de betaling.']);
        }
    }

    public function success(Ticket $ticket): View
    {
        // Controleer of gebruiker eigenaar is van het ticket
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('ticket'));
    }

    public function cancel(Ticket $ticket): RedirectResponse
    {
        // Controleer of gebruiker eigenaar is van het ticket
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        // Annuleer ticket en maak quota vrij
        DB::beginTransaction();
        try {
            $ticket->ticketType->decrement('sold_quantity', 1);
            $ticket->delete();

            DB::commit();

            return redirect()->route('events.show', $ticket->ticketType->event)
                ->with('success', 'Betaling geannuleerd. Je ticket is verwijderd.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Er is een fout opgetreden bij het annuleren.']);
        }
    }
} 