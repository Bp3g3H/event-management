<?php

namespace Tests\Unit\Policies;

use App\Models\Event;
use App\Models\User;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected EventPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new EventPolicy;
    }

    public function test_admin_can_store_update_and_destroy()
    {
        $admin = User::factory()->admin()->create();
        $organizer = User::factory()->organizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $this->assertTrue($this->policy->store($admin));
        $this->assertTrue($this->policy->update($admin, $event));
        $this->assertTrue($this->policy->destroy($admin, $event));
    }

    public function test_organizer_can_store_update_and_destroy_own_event()
    {
        $organizer = User::factory()->organizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $this->assertTrue($this->policy->store($organizer));
        $this->assertTrue($this->policy->update($organizer, $event));
        $this->assertTrue($this->policy->destroy($organizer, $event));
    }

    public function test_organizer_cannot_update_or_destroy_others_event()
    {
        $organizer = User::factory()->organizer()->create();
        $otherOrganizer = User::factory()->organizer()->create();
        $event = Event::factory()->create(['organizer_id' => $otherOrganizer->id]);

        $this->assertFalse($this->policy->update($organizer, $event));
        $this->assertFalse($this->policy->destroy($organizer, $event));
    }

    public function test_attendee_cannot_store_update_or_destroy()
    {
        $attendee = User::factory()->attendee()->create();
        $organizer = User::factory()->organizer()->create();
        $event = Event::factory()->create(['organizer_id' => $organizer->id]);

        $this->assertFalse($this->policy->store($attendee));
        $this->assertFalse($this->policy->update($attendee, $event));
        $this->assertFalse($this->policy->destroy($attendee, $event));
    }
}
