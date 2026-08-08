<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can manage the product.
     * Sellers can only manage their own products; admins can manage all.
     */
    public function manage(User $user, Product $product): bool
    {
        return $user->isAdmin() || ($user->isSeller() && $product->seller_id === $user->id);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->manage($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->manage($user, $product);
    }
}
