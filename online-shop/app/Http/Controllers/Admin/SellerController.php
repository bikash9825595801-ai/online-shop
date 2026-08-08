<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewSellerRegisteredNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', User::ROLE_SELLER)
            ->withCount(['products' => function ($q) {
                $q->where('approval_status', Product::APPROVAL_APPROVED);
            }]);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $sellers = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.sellers.index', compact('sellers'));
    }

    public function show(User $seller): View
    {
        abort_unless($seller->isSeller(), 404);

        $seller->load(['products.category', 'products.reviews']);

        $revenue = $seller->sellerOrders()->sum('subtotal');
        $totalSales = $seller->sellerOrders()->sum('quantity');
        $ordersCount = $seller->sellerOrders()->distinct('order_id')->count('order_id');

        return view('admin.sellers.show', compact('seller', 'revenue', 'totalSales', 'ordersCount'));
    }

    public function create(): View
    {
        return view('admin.sellers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'address' => ['nullable', 'string', 'max:1000'],
        ]);

        $seller = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'address' => $data['address'] ?? null,
            'role' => User::ROLE_SELLER,
            'status' => User::STATUS_ACTIVE,
        ]);

        NewSellerRegisteredNotification::send($seller);
        AuditService::log('Seller created', 'Sellers', "New seller account created: {$seller->email}");

        return redirect()->route('admin.sellers.index')->with('success', "Seller {$seller->name} created successfully.");
    }

    public function edit(User $seller): View
    {
        abort_unless($seller->isSeller(), 404);

        return view('admin.sellers.edit', compact('seller'));
    }

    public function update(Request $request, User $seller): RedirectResponse
    {
        abort_unless($seller->isSeller(), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($seller->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $seller->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'password' => $request->filled('password') ? Hash::make($request->password) : $seller->password,
        ]);

        AuditService::log('Seller updated', 'Sellers', "Seller {$seller->email} updated.");

        return back()->with('success', 'Seller updated successfully.');
    }

    public function toggleBlock(User $seller): RedirectResponse
    {
        abort_unless($seller->isSeller(), 404);

        $seller->update(['status' => $seller->isBlocked() ? User::STATUS_ACTIVE : User::STATUS_BLOCKED]);

        $action = $seller->isBlocked() ? 'blocked' : 'unblocked';
        AuditService::log(ucfirst($action), 'Sellers', "Seller {$seller->email} was {$action}.");

        return back()->with('success', "Seller {$seller->email} has been {$action}.");
    }
}
