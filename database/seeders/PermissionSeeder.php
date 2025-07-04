<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Admin permissies - volledige toegang tot alles
        $adminPermissions = [
            // Organisatie management
            'view_any_organisation',
            'view_organisation',
            'create_organisation',
            'update_organisation',
            'delete_organisation',
            'delete_any_organisation',
            
            // Gebruiker management
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'delete_any_user',
            
            // Event management (alle organisaties)
            'view_any_event',
            'view_event',
            'create_event',
            'update_event',
            'delete_event',
            'delete_any_event',
            
            // Ticket management (alle organisaties)
            'view_any_ticket',
            'view_ticket',
            'create_ticket',
            'update_ticket',
            'delete_ticket',
            'delete_any_ticket',
            
            // Ticket type management (alle organisaties)
            'view_any_ticket_type',
            'view_ticket_type',
            'create_ticket_type',
            'update_ticket_type',
            'delete_ticket_type',
            'delete_any_ticket_type',
            
            // Vendor management (alle organisaties)
            'view_any_vendor',
            'view_vendor',
            'create_vendor',
            'update_vendor',
            'delete_vendor',
            'delete_any_vendor',
            
            // Booth management (alle organisaties)
            'view_any_booth',
            'view_booth',
            'create_booth',
            'update_booth',
            'delete_booth',
            'delete_any_booth',
            
            // Feedback management (alle organisaties)
            'view_any_feedback',
            'view_feedback',
            'create_feedback',
            'update_feedback',
            'delete_feedback',
            'delete_any_feedback',
            
            // Ticket scan management (alle organisaties)
            'view_any_ticket_scan',
            'view_ticket_scan',
            'create_ticket_scan',
            'update_ticket_scan',
            'delete_ticket_scan',
            'delete_any_ticket_scan',
            
            // Systeem permissies
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
            'delete_role',
            'delete_any_role',
            
            'view_any_permission',
            'view_permission',
            'create_permission',
            'update_permission',
            'delete_permission',
            'delete_any_permission',
            
            // Dashboard toegang
            'access_admin_dashboard',
            'access_organizer_dashboard',
            
            // Export permissies
            'export_events',
            'export_tickets',
            'export_feedback',
            'export_reports',
        ];

        // Organizer permissies - alleen eigen organisatie
        $organizerPermissions = [
            // Event management (eigen organisatie)
            'view_own_events',
            'view_own_event',
            'create_own_event',
            'update_own_event',
            'delete_own_event',
            
            // Ticket management (eigen organisatie)
            'view_own_tickets',
            'view_own_ticket',
            'create_own_ticket',
            'update_own_ticket',
            'delete_own_ticket',
            
            // Ticket type management (eigen organisatie)
            'view_own_ticket_types',
            'view_own_ticket_type',
            'create_own_ticket_type',
            'update_own_ticket_type',
            'delete_own_ticket_type',
            
            // Vendor management (eigen organisatie)
            'view_own_vendors',
            'view_own_vendor',
            'create_own_vendor',
            'update_own_vendor',
            'delete_own_vendor',
            
            // Booth management (eigen organisatie)
            'view_own_booths',
            'view_own_booth',
            'create_own_booth',
            'update_own_booth',
            'delete_own_booth',
            
            // Feedback management (eigen organisatie)
            'view_own_feedback',
            'view_own_feedback_item',
            'create_own_feedback',
            'update_own_feedback',
            'delete_own_feedback',
            
            // Ticket scan management (eigen organisatie)
            'view_own_ticket_scans',
            'view_own_ticket_scan',
            'create_own_ticket_scan',
            'update_own_ticket_scan',
            'delete_own_ticket_scan',
            
            // Dashboard toegang
            'access_organizer_dashboard',
            
            // Export permissies (eigen data)
            'export_own_events',
            'export_own_tickets',
            'export_own_feedback',
            'export_own_reports',
        ];

        // Maak alle permissies aan
        $allPermissions = array_merge($adminPermissions, $organizerPermissions);
        
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Haal rollen op
        $adminRole = Role::where('name', 'admin')->first();
        $organizerRole = Role::where('name', 'organizer')->first();

        if ($adminRole) {
            // Geef admin alle permissies
            $adminRole->syncPermissions($allPermissions);
        }

        if ($organizerRole) {
            // Geef organizer alleen organizer permissies
            $organizerRole->syncPermissions($organizerPermissions);
        }
    }
} 