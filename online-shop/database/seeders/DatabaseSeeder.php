<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);

        // Ensure every customer has a cart and wishlist.
        User::where('role', User::ROLE_CUSTOMER)->get()->each(function (User $user) {
            if (! $user->cart) {
                $user->cart()->create();
            }
            if (! $user->wishlist) {
                $user->wishlist()->create();
            }
        });
    }
}
