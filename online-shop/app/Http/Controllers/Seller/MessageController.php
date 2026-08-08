<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(): View
    {
        $messages = UserNotification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('seller.messages.index', compact('messages'));
    }
}
