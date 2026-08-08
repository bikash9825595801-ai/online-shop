<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupportMessageRequest;
use App\Models\SupportMessage;
use App\Notifications\NewComplaintNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(): View
    {
        return view('support.create');
    }

    public function store(StoreSupportMessageRequest $request): RedirectResponse
    {
        $message = SupportMessage::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => SupportMessage::STATUS_OPEN,
        ]);

        NewComplaintNotification::send($message);
        AuditService::log('Complaint submitted', 'Support', "Complaint submitted: {$message->subject}");

        return redirect()->route('support.mine')->with('success', 'Your message has been submitted. Support will respond shortly.');
    }

    public function mine(): View
    {
        $messages = SupportMessage::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('support.mine', compact('messages'));
    }
}
