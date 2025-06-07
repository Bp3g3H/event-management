<?php

namespace Tests\Unit\Models;

use App\Enums\RsvpStatus;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_has_organizer()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $user->id]);

        $this->assertEquals($user->id, $event->organizer->id);
    }

    public function test_event_has_attendees()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();
        $attendee = Attendee::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $this->assertTrue($event->attendees->contains($attendee));
    }

    public function test_event_has_attending_users()
    {
        $event = Event::factory()->create();
        $user = User::factory()->create();
        $event->attendingUsers()->attach($user->id, ['rsvp_status' => RsvpStatus::Pending->value]);

        $this->assertTrue($event->attendingUsers->contains($user));
    }
}