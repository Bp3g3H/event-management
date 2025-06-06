<?php

namespace App\Observers;

use App\Models\Attendee;

class AttendeeObserver
{
    public function updating(Attendee $attendee)
    {
        if (
            $attendee->isDirty('check_in') &&
            $attendee->check_in === true &&
            $attendee->getOriginal('check_in') === false
        ) {
            $attendee->check_in_timestamp = now();
        }
    }
}
