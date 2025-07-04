<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TicketScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'event_id',
        'scanned_by',
        'scanned_at',
        'location',
        'notes',
        'status',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
        'status' => 'string',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
