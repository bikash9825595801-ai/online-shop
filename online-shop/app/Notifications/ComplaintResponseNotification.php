<?php

namespace App\Notifications;

use App\Models\SupportMessage;
use App\Services\NotificationService;

class ComplaintResponseNotification
{
    /**
     * Notify the customer that their complaint was answered.
     */
    public static function send(SupportMessage $message): void
    {
        NotificationService::send(
            $message->user_id,
            'complaint',
            'Your complaint was responded to',
            "Your complaint \"{$message->subject}\" was answered by support.",
            route('support.mine'),
        );
    }
}
