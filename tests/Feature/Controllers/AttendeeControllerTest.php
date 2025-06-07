<?php

namespace Tests\Feature\Controllers;

use App\Enums\RsvpStatus;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AttendeeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        return $user;
    }

    public function test_auth_is_required_for_all_attendee_routes()
    {
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create();

        $this->getJson('/api/attendees')->assertUnauthorized();
        $this->getJson("/api/attendees/{$attendee->id}")->assertUnauthorized();
        $this->postJson('/api/attendees', [])->assertUnauthorized();
        $this->patchJson("/api/attendees/{$attendee->id}", [])->assertUnauthorized();
        $this->deleteJson("/api/attendees/{$attendee->id}")->assertUnauthorized();
    }

    public function test_index_returns_attendees_for_authenticated_user()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $response = $this->getJson('/api/attendees');
        $response->assertOk()->assertJsonFragment(['id' => $attendee->id]);
    }

    public function test_show_returns_attendee()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $response = $this->getJson("/api/attendees/{$attendee->id}");
        $response->assertOk()->assertJsonFragment(['id' => $attendee->id]);
    }

    public function test_store_creates_attendee()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();

        $data = [
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Accepted->value,
        ];

        $response = $this->postJson('/api/attendees', $data);
        $response->assertCreated()->assertJsonFragment(['event_id' => $event->id]);
        $this->assertDatabaseHas('attendees', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Accepted->value,
        ]);
    }

    public function test_store_returns_409_if_duplicate()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $data = [
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ];

        $response = $this->postJson('/api/attendees', $data);
        $response->assertStatus(Response::HTTP_CONFLICT)
            ->assertJsonFragment(['message' => 'You have already registered as an attendee for this event.']);
    }

    public function test_store_validation()
    {
        $this->authenticate();
        $response = $this->postJson('/api/attendees', []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['event_id']);
    }

    public function test_update_attendee()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $data = ['rsvp_status' => RsvpStatus::Accepted->value];
        $response = $this->patchJson("/api/attendees/{$attendee->id}", $data);
        $response->assertOk()->assertJsonFragment(['rsvp_status' => RsvpStatus::Accepted->value]);
        $this->assertDatabaseHas('attendees', [
            'id' => $attendee->id,
            'rsvp_status' => RsvpStatus::Accepted->value,
        ]);
    }

    public function test_update_validation()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $response = $this->patchJson("/api/attendees/{$attendee->id}", []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['rsvp_status']);
    }

    public function test_destroy_attendee()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $response = $this->deleteJson("/api/attendees/{$attendee->id}");
        $response->assertOk()->assertJsonFragment(['message' => 'Attendee deleted successfully']);
        $this->assertDatabaseMissing('attendees', ['id' => $attendee->id]);
    }

    public function test_user_can_update_own_attendee()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $data = ['rsvp_status' => RsvpStatus::Accepted->value];
        $response = $this->patchJson("/api/attendees/{$attendee->id}", $data);
        $response->assertOk()->assertJsonFragment(['rsvp_status' => RsvpStatus::Accepted->value]);
    }

    public function test_user_cannot_update_other_users_attendee()
    {
        $this->authenticate();
        $otherUser = User::factory()->create();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $otherUser->id,
            'event_id' => $event->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $data = ['rsvp_status' => RsvpStatus::Accepted->value];
        $response = $this->patchJson("/api/attendees/{$attendee->id}", $data);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_user_can_delete_own_attendee()
    {
        $user = $this->authenticate();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        $response = $this->deleteJson("/api/attendees/{$attendee->id}");
        $response->assertOk()->assertJsonFragment(['message' => 'Attendee deleted successfully']);
        $this->assertDatabaseMissing('attendees', ['id' => $attendee->id]);
    }

    public function test_user_cannot_delete_other_users_attendee()
    {
        $this->authenticate();
        $otherUser = User::factory()->create();
        $event = Event::factory()->create();
        $attendee = Attendee::factory()->create([
            'user_id' => $otherUser->id,
            'event_id' => $event->id,
        ]);
        $response = $this->deleteJson("/api/attendees/{$attendee->id}");
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
