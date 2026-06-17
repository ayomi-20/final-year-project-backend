<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    // Notify a specific user
    public static function notifyUser(int $userId, string $type, string $title, string $body, ?int $relatedId = null, ?string $relatedType = null): void
    {
        AppNotification::create([
            'user_id'      => $userId,
            'is_admin'     => false,
            'type'         => $type,
            'title'        => $title,
            'body'         => $body,
            'related_id'   => $relatedId,
            'related_type' => $relatedType,
        ]);
    }

    // Notify all admins
    public static function notifyAdmins(string $type, string $title, string $body, ?int $relatedId = null, ?string $relatedType = null): void
    {
        AppNotification::create([
            'user_id'      => null,
            'is_admin'     => true,
            'type'         => $type,
            'title'        => $title,
            'body'         => $body,
            'related_id'   => $relatedId,
            'related_type' => $relatedType,
        ]);
    }
}