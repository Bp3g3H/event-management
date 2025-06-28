<?php

namespace App\Observers;

use App\Mail\AttendeeCreated;
use App\Models\Attendee;
use Illuminate\Support\Facades\Mail;

class AttendeeObserver
{
    public function created(Attendee $attendee)
    {
        $attendee->load('user', 'event');
        Mail::to($attendee->user->email)
            ->send(new AttendeeCreated($attendee, $attendee->event));
    }

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
