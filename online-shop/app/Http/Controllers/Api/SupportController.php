<?php

namespace App\Http\Controllers\Api;

use App\Models\SupportMessage;
use App\Notifications\NewComplaintNotification;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $messages = SupportMessage::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (SupportMessage $m) => $this->messagePayload($m));

        return api_response(true, 'Support messages fetched.', ['messages' => $messages]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $message = SupportMessage::create([
            'user_id' => $request->user()->id,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => SupportMessage::STATUS_OPEN,
        ]);

        NewComplaintNotification::send($message);
        AuditService::log('Complaint submitted', 'Support', "Complaint submitted via API: {$message->subject}");

        return api_response(true, 'Your message has been submitted. Support will respond shortly.', [
            'message' => $this->messagePayload($message),
        ], 201);
    }

    public function show(Request $request, SupportMessage $message): JsonResponse
    {
        abort_unless($message->user_id === $request->user()->id, 403, 'This message does not belong to you.');

        return api_response(true, 'Support message fetched.', [
            'message' => $this->messagePayload($message),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function messagePayload(SupportMessage $message): array
    {
        return [
            'id' => $message->id,
            'subject' => $message->subject,
            'message' => $message->message,
            'status' => $message->status,
            'admin_response' => $message->admin_response,
            'responded_at' => $message->responded_at?->toIso8601String(),
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }
}
