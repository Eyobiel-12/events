<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\PaymentConfirmationNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mollie\Laravel\Facades\Mollie;

final class WebhookController extends Controller
{
    public function mollie(Request $request): Response
    {
        try {
            $paymentId = $request->input('id');
            $payment = Mollie::api()->payments->get($paymentId);

            // Zoek ticket op basis van payment_id
            $ticket = Ticket::where('payment_id', $paymentId)->first();
            
            if (!$ticket) {
                Log::error('Webhook: Ticket niet gevonden voor payment_id', ['payment_id' => $paymentId]);
                return response('Ticket niet gevonden', 404);
            }

            // Zoek transaction
            $transaction = Transaction::where('payment_id', $paymentId)->first();

            DB::beginTransaction();
            try {
                if ($payment->isPaid()) {
                    // Betaling succesvol
                    $ticket->update(['status' => 'paid']);
                    
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'completed',
                            'paid_at' => now(),
                        ]);
                    }

                    // Stuur betalingsbevestigingsemail
                    $user = User::find($ticket->user_id);
                    if ($user) {
                        $user->notify(new PaymentConfirmationNotification($ticket));
                    }

                    Log::info('Webhook: Betaling succesvol', [
                        'ticket_id' => $ticket->id,
                        'payment_id' => $paymentId,
                        'amount' => $payment->amount->value,
                        'email_sent' => $user ? 'yes' : 'no'
                    ]);

                } elseif ($payment->isFailed()) {
                    // Betaling mislukt
                    $ticket->update(['status' => 'failed']);
                    
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'failed',
                            'failed_at' => now(),
                        ]);
                    }

                    // Maak quota vrij
                    $ticket->ticketType->decrement('sold_quantity', 1);

                    Log::info('Webhook: Betaling mislukt', [
                        'ticket_id' => $ticket->id,
                        'payment_id' => $paymentId
                    ]);

                } elseif ($payment->isExpired()) {
                    // Betaling verlopen
                    $ticket->update(['status' => 'expired']);
                    
                    if ($transaction) {
                        $transaction->update([
                            'status' => 'expired',
                            'expired_at' => now(),
                        ]);
                    }

                    // Maak quota vrij
                    $ticket->ticketType->decrement('sold_quantity', 1);

                    Log::info('Webhook: Betaling verlopen', [
                        'ticket_id' => $ticket->id,
                        'payment_id' => $paymentId
                    ]);
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Webhook: Database fout', [
                    'ticket_id' => $ticket->id ?? null,
                    'payment_id' => $paymentId,
                    'error' => $e->getMessage()
                ]);
                return response('Database fout', 500);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Webhook: Algemene fout', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response('Fout', 500);
        }
    }
} 