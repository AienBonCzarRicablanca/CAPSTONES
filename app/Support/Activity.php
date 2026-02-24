<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\User;

class Activity
{
    /**
     * @param  User|null  $user
     */
    public static function log(?User $user, string $action, array $meta = []): void
    {
        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'meta' => $meta,
        ]);
    }
}
