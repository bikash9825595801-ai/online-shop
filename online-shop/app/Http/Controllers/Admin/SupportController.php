<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportMessage;
use App\Notifications\ComplaintResponseNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportMessage::with(['user']);

        if ($request->filled('status') && in_array($request->status, SupportMessage::STATUSES)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . trim($request->search) . '%')
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%' . trim($request->search) . '%'));
            });
        }

        $messages = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.complaints.index', compact('messages'));
    }

    public function show(SupportMessage $message): View
    {
        $message->load(['user', 'responder']);

        return view('admin.complaints.show', compact('message'));
    }

    public function respond(Request $request, SupportMessage $message): RedirectResponse
    {
        $request->validate([
            'admin_response' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'in:' . implode(',', SupportMessage::STATUSES)],
        ]);

        $message->update([
            'admin_response' => $request->admin_response,
            'status' => $request->status,
            'responded_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        ComplaintResponseNotification::send($message);
        AuditService::log('Complaint responded', 'Support', "Complaint \"{$message->subject}\" responded.");

        return back()->with('success', 'Response sent to the customer.');
    }
}
