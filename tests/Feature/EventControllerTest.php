<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
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

    public function test_store_creates_event_with_valid_data()
    {
        $user = $this->authenticate();

        $data = [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'date' => '2025-06-10',
            'location' => 'Test Location',
            'organizer_id' => $user->id,
        ];

        $response = $this->postJson('/api/events', $data);

        $response->assertCreated();
        $response->assertJsonFragment(['title' => 'Test Event']);
    }

    public function test_store_fails_with_invalid_data()
    {
        $this->authenticate();

        $response = $this->postJson('/api/events', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'description', 'date', 'location', 'organizer_id']);
    }

    public function test_show_returns_event()
    {
        $this->authenticate();

        $event = Event::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertOk();
        $response->assertJsonFragment(['id' => $event->id]);
    }

    public function test_update_modifies_event()
    {
        $user = $this->authenticate();

        $event = Event::factory()->create(['organizer_id' => $user->id]);

        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'date' => '2025-07-01',
            'location' => 'Updated Location',
            'organizer_id' => $user->id,
        ];

        $response = $this->putJson("/api/events/{$event->id}", $data);

        $response->assertOk();
        $response->assertJsonFragment(['title' => 'Updated Title']);
    }

    public function test_destroy_deletes_event()
    {
        $this->authenticate();

        $event = Event::factory()->create();

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertOk();
        $response->assertJson(['message' => 'Event deleted successfully']);
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }
}