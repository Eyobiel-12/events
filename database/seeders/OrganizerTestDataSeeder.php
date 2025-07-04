<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Booth;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\Organisation;
use App\Models\Ticket;
use App\Models\TicketScan;
use App\Models\TicketType;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class OrganizerTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate gerelateerde tabellen
        DB::statement('PRAGMA foreign_keys = OFF;');
        Booth::truncate();
        Feedback::truncate();
        TicketScan::truncate();
        Ticket::truncate();
        TicketType::truncate();
        Event::truncate();
        Vendor::truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        // Get the first organisation
        $organisation = Organisation::first();
        if (!$organisation) {
            return;
        }

        // Create vendors
        $vendors = [
            [
                'name' => 'TechCorp Solutions',
                'email' => 'info@techcorp.com',
                'phone' => '+31 20 123 4567',
                'description' => 'Leading technology solutions provider',
                'website' => 'https://techcorp.com',
                'status' => 'active',
            ],
            [
                'name' => 'Green Energy Co',
                'email' => 'contact@greenenergy.com',
                'phone' => '+31 20 234 5678',
                'description' => 'Sustainable energy solutions',
                'website' => 'https://greenenergy.com',
                'status' => 'active',
            ],
            [
                'name' => 'Creative Design Studio',
                'email' => 'hello@creativestudio.com',
                'phone' => '+31 20 345 6789',
                'description' => 'Creative design and branding services',
                'website' => 'https://creativestudio.com',
                'status' => 'pending',
            ],
        ];

        foreach ($vendors as $vendorData) {
            Vendor::create(array_merge($vendorData, ['organisation_id' => $organisation->id]));
        }

        // Create events
        $events = [
            [
                'title' => 'Tech Innovation Summit 2024',
                'slug' => 'tech-innovation-summit-2024',
                'description' => 'Join us for the biggest tech innovation event of the year',
                'location' => 'Amsterdam Convention Center',
                'address' => 'Damrak 1',
                'city' => 'Amsterdam',
                'country' => 'Netherlands',
                'postal_code' => '1012 LG',
                'start_date' => now()->addMonths(2),
                'end_date' => now()->addMonths(2)->addDays(2),
                'max_attendees' => 500,
                'status' => 'published',
                'is_featured' => true,
            ],
            [
                'title' => 'Sustainable Business Conference',
                'slug' => 'sustainable-business-conference',
                'description' => 'Learn about sustainable business practices',
                'location' => 'Rotterdam Business Center',
                'address' => 'Coolsingel 105',
                'city' => 'Rotterdam',
                'country' => 'Netherlands',
                'postal_code' => '3012 AL',
                'start_date' => now()->addMonths(3),
                'end_date' => now()->addMonths(3)->addDay(),
                'max_attendees' => 300,
                'status' => 'published',
                'is_featured' => false,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create(array_merge($eventData, ['organisation_id' => $organisation->id]));
        }

        // Create ticket types for each event
        $events = Event::where('organisation_id', $organisation->id)->get();
        
        foreach ($events as $event) {
            $ticketTypes = [
                [
                    'name' => 'Early Bird',
                    'description' => 'Limited early bird tickets at discounted price',
                    'price' => 49.99,
                    'quantity' => 100,
                    'sold_quantity' => 25,
                    'sale_start_date' => now()->subMonth(),
                    'sale_end_date' => now()->addMonth(),
                    'is_active' => true,
                    'benefits' => 'Early access, Networking lunch, Swag bag',
                ],
                [
                    'name' => 'Regular',
                    'description' => 'Standard conference ticket',
                    'price' => 79.99,
                    'quantity' => 200,
                    'sold_quantity' => 45,
                    'sale_start_date' => now()->subMonth(),
                    'sale_end_date' => $event->start_date,
                    'is_active' => true,
                    'benefits' => 'Full access, Networking lunch, Swag bag',
                ],
                [
                    'name' => 'VIP',
                    'description' => 'Premium VIP experience',
                    'price' => 149.99,
                    'quantity' => 50,
                    'sold_quantity' => 15,
                    'sale_start_date' => now()->subMonth(),
                    'sale_end_date' => $event->start_date,
                    'is_active' => true,
                    'benefits' => 'VIP seating, Exclusive dinner, Meet speakers, Premium swag',
                ],
            ];

            foreach ($ticketTypes as $ticketTypeData) {
                TicketType::create(array_merge($ticketTypeData, ['event_id' => $event->id]));
            }
        }

        // Create booths for events
        $vendors = Vendor::where('organisation_id', $organisation->id)->get();
        
        foreach ($events as $event) {
            $boothSizes = ['small', 'medium', 'large'];
            $boothStatuses = ['available', 'reserved', 'occupied'];
            
            for ($i = 1; $i <= 10; $i++) {
                Booth::create([
                    'event_id' => $event->id,
                    'vendor_id' => $vendors->random()->id,
                    'name' => "Booth {$i}",
                    'number' => "B{$event->id}-{$i}",
                    'description' => "Exhibition booth number {$i}",
                    'size' => $boothSizes[array_rand($boothSizes)],
                    'price' => rand(500, 2000),
                    'status' => $boothStatuses[array_rand($boothStatuses)],
                    'amenities' => ['Electricity', 'WiFi', 'Table', 'Chairs'],
                ]);
            }
        }

        // Create tickets
        $ticketTypes = TicketType::all();
        
        foreach ($ticketTypes as $ticketType) {
            $soldCount = $ticketType->sold_quantity;
            
            for ($i = 0; $i < $soldCount; $i++) {
                Ticket::create([
                    'ticket_type_id' => $ticketType->id,
                    'attendee_name' => fake()->name(),
                    'attendee_email' => fake()->email(),
                    'attendee_phone' => fake()->phoneNumber(),
                    'status' => 'sold',
                    'qr_code' => 'TICKET-' . strtoupper(uniqid()),
                    'amount_paid' => $ticketType->price,
                    'payment_method' => 'credit_card',
                    'payment_id' => 'PAY-' . strtoupper(uniqid()),
                    'paid_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        // Create ticket scans
        $soldTickets = Ticket::where('status', 'sold')->get();
        $users = User::all();
        
        foreach ($soldTickets->take(10) as $ticket) {
            TicketScan::create([
                'ticket_id' => $ticket->id,
                'event_id' => $ticket->ticketType->event_id,
                'scanned_by' => $users->random()->id,
                'scanned_at' => now()->subDays(rand(1, 7)),
                'location' => 'Main Entrance',
                'status' => 'valid',
            ]);
        }

        // Create feedback
        $soldTickets = Ticket::where('status', 'sold')->get();
        
        foreach ($soldTickets->take(5) as $ticket) {
            Feedback::create([
                'event_id' => $ticket->ticketType->event_id,
                'ticket_id' => $ticket->id,
                'attendee_name' => $ticket->attendee_name,
                'attendee_email' => $ticket->attendee_email,
                'overall_rating' => rand(3, 5),
                'organization_rating' => rand(3, 5),
                'venue_rating' => rand(3, 5),
                'content_rating' => rand(3, 5),
                'comments' => fake()->paragraph(),
                'would_recommend' => rand(0, 1),
                'would_attend_again' => rand(0, 1),
            ]);
        }
    }
}
