<?php

namespace Tests\Unit;

use App\Enums\RsvpStatus;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeeTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendee_belongs_to_user()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $attendee = Attendee::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $this->assertEquals($user->id, $attendee->user->id);
    }

    public function test_attendee_belongs_to_event()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();
        $attendee = Attendee::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $this->assertEquals($event->id, $attendee->event->id);
    }
}