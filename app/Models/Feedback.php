<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $fillable = [
        'event_id',
        'ticket_id',
        'attendee_name',
        'attendee_email',
        'overall_rating',
        'organization_rating',
        'venue_rating',
        'content_rating',
        'comments',
        'would_recommend',
        'would_attend_again',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'organization_rating' => 'integer',
        'venue_rating' => 'integer',
        'content_rating' => 'integer',
        'would_recommend' => 'boolean',
        'would_attend_again' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
