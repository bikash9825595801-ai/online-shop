<?php

namespace App\Notifications;

use App\Models\SupportMessage;
use App\Services\NotificationService;

class NewComplaintNotification
{
    /**
     * Notify all admins that a new complaint was submitted.
     */
    public static function send(SupportMessage $message): void
    {
        NotificationService::sendToAdmins(
            'complaint',
            'New complaint received',
            "{$message->user->name} submitted a complaint: {$message->subject}",
            route('admin.complaints.show', $message->id),
            $message->user_id,
        );
    }
}
