<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'title',
        'slug',
        'description',
        'location',
        'address',
        'city',
        'country',
        'postal_code',
        'image',
        'start_date',
        'end_date',
        'max_attendees',
        'status',
        'is_featured',
        'settings',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_featured' => 'boolean',
        'settings' => 'array',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function ticketTypes(): HasMany
    {
        return $this->hasMany(TicketType::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasManyThrough(Ticket::class, TicketType::class);
    }

    public function booths(): HasMany
    {
        return $this->hasMany(Booth::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->feedback()->approved()->avg('rating') ?? 0.0;
    }

    public function getTotalFeedbackCountAttribute(): int
    {
        return $this->feedback()->approved()->count();
    }

    public function getRatingDistributionAttribute(): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->feedback()->approved()->withRating($i)->count();
        }
        return $distribution;
    }

    public function ticketScans(): HasMany
    {
        return $this->hasMany(TicketScan::class);
    }
}
