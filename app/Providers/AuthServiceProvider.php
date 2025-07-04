<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Event;
use App\Models\Organisation;
use App\Models\Ticket;
use App\Models\Vendor;
use App\Policies\EventPolicy;
use App\Policies\OrganisationPolicy;
use App\Policies\TicketPolicy;
use App\Policies\VendorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
        Ticket::class => TicketPolicy::class,
        Vendor::class => VendorPolicy::class,
        Organisation::class => OrganisationPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
} 