<?php

namespace App\Http\Controllers\Api;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);

        return api_response(true, 'Notifications fetched.', [
            'notifications' => collect($notifications->items())->map(fn (UserNotification $n) => $this->notificationPayload($n)),
            'unread_count' => $request->user()->notifications()->where('is_read', false)->count(),
            'pagination' => $this->paginationPayload($notifications),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->notifications()->where('is_read', false)->count();

        return api_response(true, 'Unread count fetched.', ['unread_count' => $count]);
    }

    public function markAsRead(Request $request, UserNotification $notification): JsonResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403, 'This notification does not belong to you.');

        $notification->markAsRead();

        return api_response(true, 'Notification marked as read.', [
            'notification' => $this->notificationPayload($notification),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        return api_response(true, 'All notifications marked as read.');
    }

    /**
     * @return array<string, mixed>
     */
    private function notificationPayload(UserNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
            'link' => $notification->link,
            'is_read' => $notification->is_read,
            'created_at' => $notification->created_at?->toIso8601String(),
        ];
    }
}
