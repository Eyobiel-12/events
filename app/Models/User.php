<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

final class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organisations(): BelongsToMany
    {
        return $this->belongsToMany(Organisation::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organisation_id')
            ->join('organisation_user', 'events.organisation_id', '=', 'organisation_user.organisation_id')
            ->where('organisation_user.user_id', $this->id)
            ->where('organisation_user.role', 'admin');
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /**
     * Controleer of gebruiker toegang heeft tot een specifieke organisatie
     */
    public function hasAccessToOrganisation(Organisation $organisation): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->organisations()
            ->where('organisation_id', $organisation->id)
            ->exists();
    }

    /**
     * Haal de primaire organisatie van de gebruiker op
     */
    public function getPrimaryOrganisation(): ?Organisation
    {
        return $this->organisations()->first();
    }

    /**
     * Controleer of gebruiker een specifieke permissie heeft voor een organisatie
     */
    public function canForOrganisation(string $permission, Organisation $organisation): bool
    {
        if ($this->hasRole('admin')) {
            return $this->can($permission);
        }

        if ($this->hasRole('organizer')) {
            return $this->can($permission) && $this->hasAccessToOrganisation($organisation);
        }

        return false;
    }

    /**
     * Controleer of gebruiker toegang heeft tot admin dashboard
     */
    public function canAccessAdminDashboard(): bool
    {
        return $this->hasRole('admin') && $this->can('access_admin_dashboard');
    }

    /**
     * Controleer of gebruiker toegang heeft tot organizer dashboard
     */
    public function canAccessOrganizerDashboard(): bool
    {
        return $this->hasRole('organizer') && $this->can('access_organizer_dashboard');
    }
}
