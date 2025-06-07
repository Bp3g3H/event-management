<?php

namespace App\Policies;

use App\Models\Attendee;
use App\Models\User;

class AttendeePolicy
{
    public function update(User $user, Attendee $attendee): bool
    {
        return $this->isOwner($user, $attendee);
    }

    public function delete(User $user, Attendee $attendee): bool
    {
        return $this->isOwner($user, $attendee);
    }

    private function isOwner(User $user, Attendee $attendee): bool
    {
        return $attendee->user_id === $user->id;
    }
}
