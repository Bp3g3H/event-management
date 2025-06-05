<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::Admin->value
        ]);

        User::factory()->create([
            'name' => 'Organizer User',
            'email' => 'organizer@example.com',
            'role' => UserRole::Organizer->value
        ]);

        User::factory()->create([
            'name' => 'Attendee User',
            'email' => 'attendee@example.com',
            'role' => UserRole::Attendee->value
        ]);
    }
}
