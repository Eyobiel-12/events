<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ticket_type_id',
        'uuid',
        'status',
        'amount_paid',
        'payment_method',
        'payment_id',
        'paid_at',
        'checked_in_at',
        'checked_in_by',
        'metadata',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (empty($ticket->uuid)) {
                $ticket->uuid = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id', 'id')
            ->join('ticket_types', 'tickets.ticket_type_id', '=', 'ticket_types.id')
            ->join('events', 'ticket_types.event_id', '=', 'events.id');
    }
}
