<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Notification;

class UserObserver
{
    public function created(User $user)
    {
        Notification::createUnique([
            'user_id' => null, // system-wide
            'type' => 'user_created',
            'title' => 'User created',
            'message' => "User {$user->name} was created.",
            'data' => ['user_id' => $user->id],
        ]);
    }

    public function updated(User $user)
    {
        Notification::createUnique([
            'user_id' => null,
            'type' => 'user_updated',
            'title' => 'User updated',
            'message' => "User {$user->name} was updated.",
            'data' => ['user_id' => $user->id],
        ]);
    }

    public function deleted(User $user)
    {
        Notification::createUnique([
            'user_id' => null,
            'type' => 'user_deleted',
            'title' => 'User deleted',
            'message' => "User {$user->name} was deleted.",
            'data' => ['user_id' => $user->id],
        ]);
    }
}
