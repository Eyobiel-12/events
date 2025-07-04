<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'quota',
        'sold_count',
        'available_from',
        'available_until',
        'is_active',
        'benefits',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'is_active' => 'boolean',
        'benefits' => 'array',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function getAvailableTicketsAttribute(): int
    {
        if ($this->quota === null) {
            return PHP_INT_MAX;
        }
        
        return $this->quota - $this->sold_count;
    }
}
