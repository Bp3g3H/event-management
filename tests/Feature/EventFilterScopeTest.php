<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventFilterScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_title()
    {
        Event::factory()->create(['title' => 'Laravel Conference']);
        Event::factory()->create(['title' => 'Symfony Meetup']);

        $results = Event::filter(['title' => 'Laravel'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Laravel Conference', $results->first()->title);
    }

    public function test_filter_by_description()
    {
        Event::factory()->create(['description' => 'Annual PHP event']);
        Event::factory()->create(['description' => 'JavaScript event']);

        $results = Event::filter(['description' => 'PHP'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Annual PHP event', $results->first()->description);
    }

    public function test_filter_by_date()
    {
        Event::factory()->create(['date' => '2025-06-10']);
        Event::factory()->create(['date' => '2025-07-01']);

        $results = Event::filter(['date' => '2025-06-10'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('2025-06-10', $results->first()->date);
    }

    public function test_filter_by_location()
    {
        Event::factory()->create(['location' => 'New York']);
        Event::factory()->create(['location' => 'San Francisco']);

        $results = Event::filter(['location' => 'New York'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('New York', $results->first()->location);
    }

    public function test_filter_by_organizer_name()
    {
        $organizer = User::factory()->create(['name' => 'Alice']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        Event::factory()->create();

        $results = Event::filter(['organizer' => 'Alice'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals($organizer->id, $results->first()->organizer_id);
    }

    public function test_filter_by_organizer_id()
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        Event::factory()->create();

        $results = Event::filter(['organizer_id' => $organizer->id])->get();

        $this->assertCount(1, $results);
        $this->assertEquals($organizer->id, $results->first()->organizer_id);
    }

    public function test_filter_combined()
    {
        $organizer = User::factory()->create(['name' => 'Bob']);
        Event::factory()->create([
            'title' => 'Summer Fest',
            'description' => 'Fun event',
            'date' => '2025-08-01',
            'location' => 'Beach',
            'organizer_id' => $organizer->id,
        ]);
        Event::factory()->create();

        $filters = [
            'title' => 'Summer',
            'description' => 'Fun',
            'date' => '2025-08-01',
            'location' => 'Beach',
            'organizer' => 'Bob',
            'organizer_id' => $organizer->id,
        ];

        $results = Event::filter($filters)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Summer Fest', $results->first()->title);
    }
}