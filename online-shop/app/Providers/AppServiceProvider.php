<?php

namespace App\Providers;

use App\Models\SupportMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Shared data for the storefront navbar and dashboard header.
        View::composer(['partials.storefront-nav', 'partials.dashboard-header'], function ($view) {
            $user = Auth::user();

            $cartCount = 0;
            $cartSubtotal = 0;
            $wishlistCount = 0;
            $unreadCount = 0;
            $notifications = collect();

            if ($user) {
                $cart = $user->cart()->with('items.product')->first();
                $cartCount = $cart?->itemCount() ?? 0;
                $cartSubtotal = $cart?->subtotal() ?? 0;
                $wishlistCount = $user->wishlist?->items()->count() ?? 0;
                $unreadCount = $user->notifications()->where('is_read', false)->count();
                $notifications = $user->notifications()->latest()->limit(8)->get();
            }

            $view->with(compact('cartCount', 'cartSubtotal', 'wishlistCount', 'unreadCount', 'notifications'));
        });

        // Open complaints count for the admin sidebar badge.
        View::composer('partials.admin-sidebar', function ($view) {
            $view->with('openComplaints', SupportMessage::whereIn('status', [SupportMessage::STATUS_OPEN, SupportMessage::STATUS_IN_PROGRESS])->count());
        });
    }
}
