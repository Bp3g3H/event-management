<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function store(User $user): bool
    {
        return $user->isAdmin() || $user->isOrganizer();
    }

    public function update(User $user, Event $event): bool
    {
       return $user->isAdmin() || $this->isEventOrganizer($user, $event);
    }

    public function destroy(User $user, Event $event): bool
    {
        return $user->isAdmin() || $this->isEventOrganizer($user, $event);
    }

    private function isEventOrganizer(User $user, Event $event)
    {
        return $user->id === $event->organizer_id;
    }
}
