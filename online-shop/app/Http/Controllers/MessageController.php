<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.active']);
    }

    public function index(): View
    {
        $conversations = Conversation::forUser(auth()->id())
            ->with(['customer:id,name,email,profile_image', 'seller:id,name,email,profile_image', 'lastMessage'])
            ->orderByDesc('last_message_at')
            ->get();

        return view('messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation): View
    {
        abort_unless($conversation->isParticipant(auth()->id()), 403);

        $conversation->load(['customer:id,name,email,profile_image', 'seller:id,name,email,profile_image', 'order:id,order_number']);

        $messages = $conversation->messages()
            ->with('sender:id,name,profile_image')
            ->orderBy('created_at')
            ->get();

        // Mark the other party's messages as read.
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('messages.show', compact('conversation', 'messages'));
    }

    public function start(User $seller): RedirectResponse
    {
        abort_if($seller->isSeller() === false, 404);
        abort_if($seller->id === auth()->id(), 403);

        $conversation = Conversation::firstOrCreate([
            'customer_id' => auth()->id(),
            'seller_id' => $seller->id,
        ]);

        return redirect()->route('messages.show', $conversation->id);
    }

    public function send(Request $request, Conversation $conversation): RedirectResponse
    {
        abort_unless($conversation->isParticipant(auth()->id()), 403);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $conversation->messages()->create([
            'sender_id' => auth()->id(),
            'message' => trim($data['message']),
        ]);

        $conversation->update(['last_message_at' => now()]);

        $other = $conversation->otherUser(auth()->user());

        NotificationService::send(
            $other,
            'message',
            'New message from '.auth()->user()->name,
            Str::limit(trim($data['message']), 120),
            route('messages.show', $conversation->id),
            auth()->id(),
        );

        return back();
    }
}
