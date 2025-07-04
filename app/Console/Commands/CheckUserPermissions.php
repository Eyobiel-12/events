<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

final class CheckUserPermissions extends Command
{
    protected $signature = 'users:check-permissions {email?}';
    protected $description = 'Controleer permissies van gebruikers';

    public function handle(): int
    {
        $email = $this->argument('email');

        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("Gebruiker met email '{$email}' niet gevonden.");
                return 1;
            }
            $this->displayUserPermissions($user);
        } else {
            $users = User::with('roles', 'permissions')->get();
            foreach ($users as $user) {
                $this->displayUserPermissions($user);
                $this->line('');
            }
        }

        return 0;
    }

    private function displayUserPermissions(User $user): void
    {
        $this->info("=== Gebruiker: {$user->name} ({$user->email}) ===");
        
        // Rollen
        $roles = $user->roles->pluck('name')->toArray();
        $this->line("Rollen: " . implode(', ', $roles));
        
        // Permissies
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        if (empty($permissions)) {
            $this->warn("Geen permissies toegekend");
        } else {
            $this->line("Permissies (" . count($permissions) . "):");
            foreach ($permissions as $permission) {
                $this->line("  - {$permission}");
            }
        }
        
        // Dashboard toegang
        $this->line("Dashboard toegang:");
        $this->line("  - Admin dashboard: " . ($user->canAccessAdminDashboard() ? 'Ja' : 'Nee'));
        $this->line("  - Organizer dashboard: " . ($user->canAccessOrganizerDashboard() ? 'Ja' : 'Nee'));
        
        // Organisaties
        $organisations = $user->organisations;
        if ($organisations->isNotEmpty()) {
            $this->line("Organisaties:");
            foreach ($organisations as $org) {
                $this->line("  - {$org->name} (rol: {$org->pivot->role})");
            }
        }
    }
} 