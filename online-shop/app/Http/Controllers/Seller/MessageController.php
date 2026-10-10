<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('messages.index');
    }
}
