<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Authenticate as a given user or create a new one if none provided.
     */
    protected function authenticate(?User $user = null): User
    {
        $user = $user ?: User::factory()->create();
        $this->actingAs($user, 'sanctum');
        return $user;
    }

    public function test_index_returns_paginated_events_with_filters()
    {
        $this->authenticate();

        Event::factory()->count(3)->create(['title' => 'LaravelConf']);
        Event::factory()->count(2)->create(['title' => 'SymfonyLive']);

        $response = $this->getJson('/api/events?title=LaravelConf&per_page=2');

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'LaravelConf']);
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_store_fails_with_invalid_data()
    {
        $admin = User::factory()->admin()->create();
        $this->authenticate($admin);

        $response = $this->postJson('/api/events', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'description', 'date', 'location', 'organizer_id']);
    }

    public function test_show_returns_event()
    {
        $admin = User::factory()->admin()->create();
        $this->authenticate($admin);

        $event = Event::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $event->id]);
    }

    public function test_update_modifies_event()
    {
        $admin = User::factory()->admin()->create();
        $this->authenticate($admin);

        $event = Event::factory()->create(['organizer_id' => $admin->id]);

        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'date' => '2025-07-01',
            'location' => 'Updated Location',
            'organizer_id' => $admin->id,
        ];

        $response = $this->putJson("/api/events/{$event->id}", $data);

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_destroy_deletes_event()
    {
        $user = $this->authenticate();

        $event = Event::factory()->create(['organizer_id' => $user->id]);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertOk();
        $response->assertJson(['message' => 'Event deleted successfully']);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_organizer_can_store_update_and_delete_own_event()
    {
        $organizer = User::factory()->organizer()->create();
        $this->authenticate($organizer);

        $data = [
            'title' => 'Organizer Event',
            'description' => 'Organizer Description',
            'date' => '2025-06-10',
            'location' => 'Organizer Location',
            'organizer_id' => $organizer->id,
        ];
        $response = $this->postJson('/api/events', $data);
        $response->assertCreated();
        $eventId = $response->json('data.id') ?? $response->json('id');

        $event = Event::find($eventId);
        $updateData = [
            'title' => 'Updated Organizer Event',
            'description' => 'Updated Description',
            'date' => '2025-07-01',
            'location' => 'Updated Location',
            'organizer_id' => $organizer->id,
        ];
        $response = $this->putJson("/api/events/{$event->id}", $updateData);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated Organizer Event']);

        $response = $this->deleteJson("/api/events/{$event->id}");
        $response->assertOk();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_admin_can_store_update_and_delete_any_event()
    {
        $admin = User::factory()->admin()->create();
        $organizer = User::factory()->organizer()->create();
        $this->authenticate($admin);

        $data = [
            'title' => 'Admin Event',
            'description' => 'Admin Description',
            'date' => '2025-06-10',
            'location' => 'Admin Location',
            'organizer_id' => $organizer->id,
        ];
        $response = $this->postJson('/api/events', $data);
        $response->assertCreated();
        $eventId = $response->json('data.id') ?? $response->json('id');

        $event = Event::find($eventId);
        $updateData = [
            'title' => 'Updated Admin Event',
            'description' => 'Updated Description',
            'date' => '2025-07-01',
            'location' => 'Updated Location',
            'organizer_id' => $organizer->id,
        ];
        $response = $this->putJson("/api/events/{$event->id}", $updateData);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated Admin Event']);

        $response = $this->deleteJson("/api/events/{$event->id}");
        $response->assertOk();
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_attendee_cannot_store_update_or_delete_event()
    {
        $attendee = User::factory()->attendee()->create();
        $organizer = User::factory()->organizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        $this->authenticate($attendee);

        $data = [
            'title' => 'Attendee Event',
            'description' => 'Attendee Description',
            'date' => '2025-06-10',
            'location' => 'Attendee Location',
            'organizer_id' => $attendee->id,
        ];
        $this->postJson('/api/events', $data)->assertForbidden();

        $updateData = [
            'title' => 'Should Not Update',
            'description' => 'Should Not Update',
            'date' => '2025-07-01',
            'location' => 'Should Not Update',
            'organizer_id' => $attendee->id,
        ];
        $this->putJson("/api/events/{$event->id}", $updateData)->assertForbidden();

        $this->deleteJson("/api/events/{$event->id}")->assertForbidden();
    }

    public function test_organizer_cannot_update_or_delete_others_event()
    {
        $organizer = User::factory()->organizer()->create();
        $otherOrganizer = User::factory()->organizer()->create();
        $event = Event::factory()->create(['organizer_id' => $otherOrganizer->id]);
        $this->authenticate($organizer);

        $updateData = [
            'title' => 'Should Not Update',
            'description' => 'Should Not Update',
            'date' => '2025-07-01',
            'location' => 'Should Not Update',
            'organizer_id' => $organizer->id,
        ];
        $this->putJson("/api/events/{$event->id}", $updateData)->assertForbidden();

        $this->deleteJson("/api/events/{$event->id}")->assertForbidden();
    }
}