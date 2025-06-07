<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Event;
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
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $organizer = User::factory()->organizer()->create([
            'name' => 'Organizer User',
            'email' => 'organizer@example.com',
        ]);

        $events = Event::factory(4)->create([
            'organizer_id' => $organizer->id,
        ]);

        $attendees = User::factory(16)->attendee()->create();

        $events->each(function ($event, $index) use ($attendees) {
            $eventAttendees = $attendees->slice($index * 4, 4);
            $event->attendingUsers()->attach($eventAttendees->pluck('id'));
        });
    }
}
