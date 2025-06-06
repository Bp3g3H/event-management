<?php

namespace App\Policies;

use App\Models\Attendee;
use App\Models\User;

class AttendeePolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Attendee $attendee): bool
    {
        return $attendee->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendee $attendee): bool
    {
        return $attendee->user_id === $user->id;
    }
}
