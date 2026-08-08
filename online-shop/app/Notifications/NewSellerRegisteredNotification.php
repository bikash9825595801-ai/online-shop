<?php

namespace App\Notifications;

use App\Models\User;
use App\Services\NotificationService;

class NewSellerRegisteredNotification
{
    /**
     * Notify all admins that a new seller was registered/created.
     */
    public static function send(User $seller): void
    {
        NotificationService::sendToAdmins(
            'user',
            'New seller registered',
            "A new seller \"{$seller->name}\" ({$seller->email}) has joined the platform.",
            route('admin.sellers.show', $seller->id),
            $seller->id,
        );
    }
}
