<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->firstOrCreate([
            'email' => 'admin@habesha.events',
        ], [
            'name' => 'Admin',
            'password' => bcrypt('admin1234'),
        ]);

        $admin->assignRole('admin');

        $organizer = User::query()->firstOrCreate([
            'email' => 'organizer@habesha.events',
        ], [
            'name' => 'Organizer',
            'password' => bcrypt('organizer1234'),
        ]);

        $organizer->assignRole('organizer');
    }
}
