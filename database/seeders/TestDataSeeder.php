<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organisation;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Test organisatie
        $organisation = Organisation::query()->firstOrCreate([
            'name' => 'Habesha Events Amsterdam',
            'slug' => 'habesha-events-amsterdam',
        ], [
            'description' => 'De grootste Habesha community organisatie in Amsterdam',
            'email' => 'info@habesha-events.nl',
            'phone' => '+31 6 12345678',
            'website' => 'https://habesha-events.nl',
            'is_active' => true,
        ]);

        // Test event
        $event = Event::query()->firstOrCreate([
            'title' => 'Habesha Cultural Festival 2024',
            'slug' => 'habesha-cultural-festival-2024',
        ], [
            'organisation_id' => $organisation->id,
            'description' => 'Een geweldig festival met traditionele muziek, dans en eten',
            'location' => 'Amsterdam Arena',
            'address' => 'ArenA Boulevard 1',
            'city' => 'Amsterdam',
            'country' => 'Netherlands',
            'postal_code' => '1101 AX',
            'start_date' => now()->addMonths(2),
            'end_date' => now()->addMonths(2)->addHours(8),
            'max_attendees' => 1000,
            'status' => 'published',
            'is_featured' => true,
        ]);

        // Test ticket types
        TicketType::query()->firstOrCreate([
            'name' => 'Early Bird',
            'event_id' => $event->id,
        ], [
            'description' => 'Vroegboek korting ticket',
            'price' => 25.00,
            'quota' => 200,
            'available_from' => now(),
            'available_until' => now()->addMonth(),
            'is_active' => true,
        ]);

        TicketType::query()->firstOrCreate([
            'name' => 'Regular',
            'event_id' => $event->id,
        ], [
            'description' => 'Standaard ticket',
            'price' => 35.00,
            'quota' => 500,
            'available_from' => now()->addMonth(),
            'available_until' => now()->addMonths(2)->subDay(),
            'is_active' => true,
        ]);

        TicketType::query()->firstOrCreate([
            'name' => 'VIP',
            'event_id' => $event->id,
        ], [
            'description' => 'VIP ticket met exclusieve voordelen',
            'price' => 75.00,
            'quota' => 100,
            'available_from' => now(),
            'available_until' => now()->addMonths(2)->subDay(),
            'is_active' => true,
            'benefits' => ['Exclusieve seating', 'Meet & Greet', 'Free drinks'],
        ]);

        // Koppel admin gebruiker aan organisatie
        $admin = User::where('email', 'admin@habesha.events')->first();
        if ($admin) {
            $admin->organisations()->syncWithoutDetaching([
                $organisation->id => ['role' => 'admin']
            ]);
        }

        // Koppel organizer gebruiker aan organisatie
        $organizer = User::where('email', 'organizer@habesha.events')->first();
        if ($organizer) {
            $organizer->organisations()->syncWithoutDetaching([
                $organisation->id => ['role' => 'admin']
            ]);
        }
    }
}
