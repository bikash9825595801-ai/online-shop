<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', User::ROLE_CUSTOMER)
            ->withCount(['orders' => function ($q) {
                $q->where('order_status', '!=', Order::ORDER_CANCELLED);
            }])
            ->withSum(['orders' => function ($q) {
                $q->where('order_status', '!=', Order::ORDER_CANCELLED);
            }], 'total_amount');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'blocked'])) {
            $query->where('status', $request->status);
        }

        $customers = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer): View
    {
        abort_unless($customer->isCustomer(), 404);

        $customer->load(['orders.items.product', 'reviews.product']);

        $totalSpent = Order::where('customer_id', $customer->id)
            ->where('order_status', '!=', Order::ORDER_CANCELLED)
            ->sum('total_amount');

        return view('admin.customers.show', compact('customer', 'totalSpent'));
    }
}
