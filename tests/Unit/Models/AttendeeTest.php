<?php

namespace Tests\Unit\Models;

use App\Enums\RsvpStatus;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendee;
use Carbon\Carbon;
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

    public function test_check_in_not_open_before_day_before_event()
    {
        $event = Event::factory()->create([
            'date' => Carbon::now()->addDays(2)->toDateString(),
        ]);

        Carbon::setTestNow(Carbon::now());
        $this->assertTrue($event->IsOpenForCheckIn());
    }

    public function test_check_in_open_on_day_before_event()
    {
        $event = Event::factory()->create([
            'date' => Carbon::now()->addDay()->toDateString(),
        ]);

        Carbon::setTestNow(Carbon::now());
        $this->assertFalse($event->IsOpenForCheckIn());
    }

    public function test_check_in_open_on_event_day()
    {
        $event = Event::factory()->create([
            'date' => Carbon::now()->toDateString(),
        ]);

        Carbon::setTestNow(Carbon::now());
        $this->assertFalse($event->IsOpenForCheckIn());
    }

    public function test_check_in_not_open_after_event_day()
    {
        $event = Event::factory()->create([
            'date' => Carbon::now()->subDay()->toDateString(),
        ]);

        Carbon::setTestNow(Carbon::now()->addDay());
        $this->assertTrue($event->IsOpenForCheckIn());
    }
}