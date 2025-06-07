<?php

namespace Tests\Feature\FilterAndSort;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventFilterAndSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_title()
    {
        Event::factory()->create(['title' => 'Laravel Conference']);
        Event::factory()->create(['title' => 'Symfony Meetup']);

        $results = Event::filterAndSort(['title' => 'Laravel'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Laravel Conference', $results->first()->title);
    }

    public function test_filter_by_description()
    {
        Event::factory()->create(['description' => 'Annual PHP event']);
        Event::factory()->create(['description' => 'JavaScript event']);

        $results = Event::filterAndSort(['description' => 'PHP'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Annual PHP event', $results->first()->description);
    }

    public function test_filter_by_date()
    {
        Event::factory()->create(['date' => '2025-06-10']);
        Event::factory()->create(['date' => '2025-07-01']);

        $results = Event::filterAndSort(['date' => '2025-06-10'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('2025-06-10', $results->first()->date->toDateString());
    }

    public function test_filter_by_location()
    {
        Event::factory()->create(['location' => 'New York']);
        Event::factory()->create(['location' => 'San Francisco']);

        $results = Event::filterAndSort(['location' => 'New York'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('New York', $results->first()->location);
    }

    public function test_filter_by_organizer_name()
    {
        $organizer = User::factory()->create(['name' => 'Alice']);
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        Event::factory()->create();

        $results = Event::filterAndSort(['organizer' => 'Alice'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals($organizer->id, $results->first()->organizer_id);
    }

    public function test_filter_by_organizer_id()
    {
        $organizer = User::factory()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);
        Event::factory()->create();

        $results = Event::filterAndSort(['organizer_id' => $organizer->id])->get();

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

        $results = Event::filterAndSort($filters)->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Summer Fest', $results->first()->title);
    }

    public function test_sort_by_title_asc()
    {
        Event::factory()->create(['title' => 'Alpha']);
        Event::factory()->create(['title' => 'Beta']);
        Event::factory()->create(['title' => 'Gamma']);

        $results = Event::filterAndSort(['sort_by' => 'title', 'sort_order' => 'asc'])->pluck('title')->toArray();

        $this->assertEquals(['Alpha', 'Beta', 'Gamma'], $results);
    }

    public function test_sort_by_title_desc()
    {
        Event::factory()->create(['title' => 'Alpha']);
        Event::factory()->create(['title' => 'Beta']);
        Event::factory()->create(['title' => 'Gamma']);

        $results = Event::filterAndSort(['sort_by' => 'title', 'sort_order' => 'desc'])->pluck('title')->toArray();

        $this->assertEquals(['Gamma', 'Beta', 'Alpha'], $results);
    }

    public function test_sort_by_date_asc()
    {
        Event::factory()->create(['title' => 'First', 'date' => '2025-01-01']);
        Event::factory()->create(['title' => 'Second', 'date' => '2025-02-01']);
        Event::factory()->create(['title' => 'Third', 'date' => '2025-03-01']);

        $results = Event::filterAndSort(['sort_by' => 'date', 'sort_order' => 'asc'])->pluck('title')->toArray();

        $this->assertEquals(['First', 'Second', 'Third'], $results);
    }

    public function test_sort_by_date_desc()
    {
        Event::factory()->create(['title' => 'First', 'date' => '2025-01-01']);
        Event::factory()->create(['title' => 'Second', 'date' => '2025-02-01']);
        Event::factory()->create(['title' => 'Third', 'date' => '2025-03-01']);

        $results = Event::filterAndSort(['sort_by' => 'date', 'sort_order' => 'desc'])->pluck('title')->toArray();

        $this->assertEquals(['Third', 'Second', 'First'], $results);
    }

    public function test_sort_by_location_asc()
    {
        Event::factory()->create(['title' => 'A', 'location' => 'Paris']);
        Event::factory()->create(['title' => 'B', 'location' => 'London']);
        Event::factory()->create(['title' => 'C', 'location' => 'Berlin']);

        $results = Event::filterAndSort(['sort_by' => 'location', 'sort_order' => 'asc'])->pluck('location')->toArray();

        $this->assertEquals(['Berlin', 'London', 'Paris'], $results);
    }

    public function test_sort_by_location_desc()
    {
        Event::factory()->create(['title' => 'A', 'location' => 'Paris']);
        Event::factory()->create(['title' => 'B', 'location' => 'London']);
        Event::factory()->create(['title' => 'C', 'location' => 'Berlin']);

        $results = Event::filterAndSort(['sort_by' => 'location', 'sort_order' => 'desc'])->pluck('location')->toArray();

        $this->assertEquals(['Paris', 'London', 'Berlin'], $results);
    }

    public function test_default_sort_is_created_at_desc()
    {
        $first = Event::factory()->create(['title' => 'First']);
        sleep(1);
        $second = Event::factory()->create(['title' => 'Second']);
        sleep(1);
        $third = Event::factory()->create(['title' => 'Third']);

        $results = Event::filterAndSort([])->pluck('title')->toArray();

        $this->assertEquals(['Third', 'Second', 'First'], $results);
    }
}