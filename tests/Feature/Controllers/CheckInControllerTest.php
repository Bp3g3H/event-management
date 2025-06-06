<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CheckInControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    public function test_cannot_check_in_to_past_event()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create(['date' => Carbon::now()->subDays(2)->toDateString()]);
        Attendee::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'check_in' => false]);

        $response = $this->postJson("/api/events/{$event->id}/check-in");
        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Check-in is only allowed one day before and on the day of the event.']);
    }

    public function test_cannot_check_in_to_future_event()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create(['date' => Carbon::now()->addDays(2)->toDateString()]);
        Attendee::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'check_in' => false]);

        $response = $this->postJson("/api/events/{$event->id}/check-in");
        $response->assertStatus(400)
            ->assertJsonFragment(['message' => 'Check-in is only allowed one day before and on the day of the event.']);
    }

    public function test_cannot_check_in_if_already_checked_in()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create(['date' => Carbon::now()->toDateString()]);
        Attendee::factory()->checkedIn()->create(['user_id' => $user->id, 'event_id' => $event->id]);

        $response = $this->postJson("/api/events/{$event->id}/check-in");
        $response->assertStatus(409)
            ->assertJsonFragment(['message' => 'You have already checked in for this event.']);
    }

    public function test_successful_check_in_on_event_day()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create(['date' => Carbon::now()->toDateString()]);
        $attendee = Attendee::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'check_in' => false]);

        $response = $this->postJson("/api/events/{$event->id}/check-in");
        $response->assertOk()
            ->assertJsonFragment(['message' => "You have Check-in successful for event: {$event->title}"]);

        $this->assertDatabaseHas('attendees', [
            'id' => $attendee->id,
            'check_in' => true,
        ]);
    }

    public function test_successful_check_in_one_day_before_event()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create(['date' => Carbon::now()->addDay()->toDateString()]);
        $attendee = Attendee::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'check_in' => false]);

        $response = $this->postJson("/api/events/{$event->id}/check-in");
        $response->assertOk()
            ->assertJsonFragment(['message' => "You have Check-in successful for event: {$event->title}"]);

        $this->assertDatabaseHas('attendees', [
            'id' => $attendee->id,
            'check_in' => true,
        ]);
    }

    public function test_check_in_returns_404_if_user_is_not_attendee()
    {
        $this->authenticate();
        $event = Event::factory()->create(['date' => now()->toDateString()]);

        $response = $this->postJson("/api/events/{$event->id}/check-in");
        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'You are not registered as an attendee for this event.']);
    }
}