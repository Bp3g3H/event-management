<?php

namespace Tests\Unit;

use App\Enums\RsvpStatus;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_organized_events()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $user->id]);

        $this->assertTrue($user->organizedEvents->contains($event));
    }

    public function test_user_can_have_attending_events()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $user->attendingEvents()->attach($event->id);

        $this->assertTrue($user->attendingEvents->contains($event));
    }

    public function test_user_can_have_attendee()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $attendee = Attendee::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $this->assertTrue($user->attendees->contains($attendee));
    }
}