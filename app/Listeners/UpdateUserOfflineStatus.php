<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class UpdateUserOfflineStatus
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $event->user->update([
                'is_online' => false,
                'last_activity_at' => null,
            ]);
        }
    }
}