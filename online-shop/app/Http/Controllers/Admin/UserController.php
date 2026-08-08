<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewSellerRegisteredNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && in_array($request->role, ['admin', 'seller', 'customer'])) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'blocked'])) {
            $query->where('status', $request->status);
        }

        $users = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['orders', 'products']);

        return view('admin.users.show', compact('user'));
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'You cannot block an administrator account.');
        }

        $user->update(['status' => $user->isBlocked() ? User::STATUS_ACTIVE : User::STATUS_BLOCKED]);

        $action = $user->isBlocked() ? 'blocked' : 'unblocked';
        AuditService::log(ucfirst($action), 'Users', "User {$user->email} was {$action}.");

        return back()->with('success', "User {$user->email} has been {$action}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'You cannot delete an administrator account.');
        }

        if ($user->isSeller() && $user->products()->exists()) {
            return back()->with('error', 'This seller has products. Delete or reassign their products first.');
        }

        $email = $user->email;
        $user->delete();

        AuditService::log('Deleted', 'Users', "User {$email} was deleted.");

        return back()->with('success', "User {$email} has been deleted.");
    }
}
