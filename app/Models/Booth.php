<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Booth extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'vendor_id',
        'name',
        'number',
        'description',
        'size',
        'price',
        'status',
        'amenities',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'amenities' => 'array',
        'size' => 'string',
        'status' => 'string',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
