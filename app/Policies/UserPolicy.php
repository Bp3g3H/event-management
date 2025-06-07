<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $this->isOwner($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $this->isOwner($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() || $this->isOwner($user, $model);
    }

    private function isOwner(User $user, User $model): bool
    {
        return $model->id === $user->id;
    }
}
