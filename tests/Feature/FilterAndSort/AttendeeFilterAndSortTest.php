<?php

namespace Tests\Feature\FilterAndSort;

use App\Enums\RsvpStatus;
use App\Models\Attendee;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeeFilterAndSortTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $otherUser;

    private Event $event;

    private Event $otherEvent;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users and events for testing
        $this->user = User::factory()->create(['name' => 'Alice']);
        $this->otherUser = User::factory()->create(['name' => 'Bob']);
        $this->event = Event::factory()->create(['title' => 'LaravelConf', 'organizer_id' => $this->user->id]);
        $this->otherEvent = Event::factory()->create(['title' => 'SymfonyLive', 'organizer_id' => $this->otherUser->id]);
    }

    public function test_filter_by_event_id()
    {
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id]);
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id]);

        $results = Attendee::filterAndSort(['event_id' => $this->event->id], $this->user->id)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($this->event->id, $results->first()->event_id);
    }

    public function test_filter_by_event_title()
    {
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id]);
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id]);

        $results = Attendee::filterAndSort(['event_title' => 'Laravel'], $this->user->id)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($this->event->id, $results->first()->event_id);
    }

    public function test_filter_by_organizer_name()
    {
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id]);
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id]);

        $results = Attendee::filterAndSort(['organizer' => 'Alice'], $this->user->id)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($this->event->id, $results->first()->event_id);
    }

    public function test_filter_by_organizer_id()
    {
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id]);
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id]);

        $results = Attendee::filterAndSort(['organizer_id' => $this->user->id], $this->user->id)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($this->event->id, $results->first()->event_id);
    }

    public function test_filter_by_rsvp_status()
    {
        Attendee::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'rsvp_status' => RsvpStatus::Accepted->value,
        ]);
        Attendee::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $this->otherEvent->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $results = Attendee::filterAndSort(['rsvp_status' => RsvpStatus::Accepted->value], $this->user->id)->get();
        $this->assertCount(1, $results);
        $this->assertEquals(RsvpStatus::Accepted->value, $results->first()->rsvp_status);
    }

    public function test_sort_by_event_title()
    {
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id]);
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id]);

        $results = Attendee::filterAndSort(['sort_by' => 'event_title', 'sort_order' => 'asc'], $this->user->id)->get();
        $this->assertEquals(
            [$this->event->id, $this->otherEvent->id],
            $results->pluck('event_id')->toArray()
        );
    }

    public function test_sort_by_organizer_name()
    {
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id]);
        Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id]);

        $results = Attendee::filterAndSort(['sort_by' => 'organizer_name', 'sort_order' => 'asc'], $this->user->id)->get();
        $this->assertEquals(
            [$this->event->id, $this->otherEvent->id],
            $results->pluck('event_id')->toArray()
        );
    }

    public function test_sort_by_rsvp_status()
    {
        $a1 = Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id, 'rsvp_status' => RsvpStatus::Accepted->value]);
        $a2 = Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id, 'rsvp_status' => RsvpStatus::Pending->value]);

        $results = Attendee::filterAndSort(['sort_by' => 'rsvp_status', 'sort_order' => 'asc'], $this->user->id)->get();
        $this->assertEquals(
            [$a1->id, $a2->id],
            $results->pluck('id')->toArray()
        );
    }

    public function test_sort_by_created_at()
    {
        $a1 = Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->event->id, 'created_at' => now()->subDay()]);
        $a2 = Attendee::factory()->create(['user_id' => $this->user->id, 'event_id' => $this->otherEvent->id, 'created_at' => now()]);

        $results = Attendee::filterAndSort(['sort_by' => 'created_at', 'sort_order' => 'asc'], $this->user->id)->get();
        $this->assertEquals(
            [$a1->id, $a2->id],
            $results->pluck('id')->toArray()
        );
    }

    public function test_combined_filters()
    {
        $a1 = Attendee::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $this->event->id,
            'rsvp_status' => RsvpStatus::Accepted->value,
        ]);
        Attendee::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $this->otherEvent->id,
            'rsvp_status' => RsvpStatus::Pending->value,
        ]);

        $filters = [
            'event_id' => $this->event->id,
            'event_title' => 'Laravel',
            'organizer' => 'Alice',
            'organizer_id' => $this->user->id,
            'rsvp_status' => RsvpStatus::Accepted->value,
        ];

        $results = Attendee::filterAndSort($filters, $this->user->id)->get();
        $this->assertCount(1, $results);
        $this->assertEquals($a1->id, $results->first()->id);
    }
}
