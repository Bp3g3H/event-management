<?php

namespace Tests\Unit;

use App\Models\Attendee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendeeObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_in_sets_check_in_timestamp()
    {
        $attendee = Attendee::factory()->create([
            'check_in' => false,
            'check_in_timestamp' => null,
        ]);

        $attendee->check_in = true;
        $attendee->save();

        $attendee->refresh();
        $this->assertNotNull($attendee->check_in_timestamp);
    }

    public function test_check_in_does_not_set_timestamp_if_already_true()
    {
        $attendee = Attendee::factory()->create([
            'check_in' => true,
            'check_in_timestamp' => now(),
        ]);

        $originalTimestamp = $attendee->check_in_timestamp;
        $attendee->check_in = true;
        $attendee->save();

        $attendee->refresh();
        $this->assertEquals($originalTimestamp, $attendee->check_in_timestamp);
    }
}