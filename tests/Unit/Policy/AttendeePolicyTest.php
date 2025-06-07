<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use App\Models\Attendee;
use App\Policies\AttendeePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_and_delete_attendee()
    {
        $user = User::factory()->create();
        $attendee = Attendee::factory()->create(['user_id' => $user->id]);
        $policy = new AttendeePolicy();
   
        $this->assertTrue($policy->update($user, $attendee));
        $this->assertTrue($policy->delete($user, $attendee));
    }

    public function test_non_owner_cannot_update_or_delete_attendee()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $attendee = Attendee::factory()->create(['user_id' => $otherUser->id]);
        $policy = new AttendeePolicy();

        $this->assertFalse($policy->update($user, $attendee));
        $this->assertFalse($policy->delete($user, $attendee));
    }
}