<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserNotification;

class NotificationService
{
    /**
     * Create an in-app notification for a user.
     */
    public static function send(
        User|int $user,
        string $type,
        string $title,
        ?string $message = null,
        ?string $link = null,
        ?int $actorId = null,
    ): UserNotification {
        $userId = $user instanceof User ? $user->id : $user;

        return UserNotification::create([
            'user_id' => $userId,
            'actor_id' => $actorId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    /**
     * Notify every user with a given role.
     */
    public static function sendToRole(string $role, string $type, string $title, ?string $message = null, ?string $link = null, ?int $actorId = null): void
    {
        User::where('role', $role)->where('status', User::STATUS_ACTIVE)->get()
            ->each(fn (User $user) => self::send($user, $type, $title, $message, $link, $actorId));
    }

    /**
     * Notify all admin users.
     */
    public static function sendToAdmins(string $type, string $title, ?string $message = null, ?string $link = null, ?int $actorId = null): void
    {
        self::sendToRole(User::ROLE_ADMIN, $type, $title, $message, $link, $actorId);
    }
}
