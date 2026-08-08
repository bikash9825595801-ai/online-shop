<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['order.customer']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'paid', 'failed', 'refunded'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('search')) {
            $query->whereHas('order', fn ($q) => $q->where('order_number', 'like', '%' . trim($request->search) . '%'))
                ->orWhere('transaction_id', 'like', '%' . trim($request->search) . '%');
        }

        $payments = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $totalCollected = Payment::where('status', Payment::STATUS_PAID)->sum('amount');
        $totalPending = Payment::where('status', Payment::STATUS_PENDING)->sum('amount');

        return view('admin.payments.index', compact('payments', 'totalCollected', 'totalPending'));
    }
}
