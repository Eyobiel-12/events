<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'name',
        'email',
        'phone',
        'description',
        'website',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function booths(): HasMany
    {
        return $this->hasMany(Booth::class);
    }
}
