<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'description' => 'Mobile phones, laptops, accessories and gadgets.'],
            ['name' => 'Fashion', 'description' => 'Men\'s and women\'s clothing, shoes and accessories.'],
            ['name' => 'Home & Living', 'description' => 'Furniture, kitchenware and home decor.'],
            ['name' => 'Beauty & Health', 'description' => 'Skincare, cosmetics and wellness products.'],
            ['name' => 'Sports & Outdoors', 'description' => 'Fitness equipment, sports gear and outdoor items.'],
            ['name' => 'Toys & Kids', 'description' => 'Toys, baby gear and kids essentials.'],
            ['name' => 'Books & Stationery', 'description' => 'Books, notebooks and office stationery.'],
            ['name' => 'Grocery', 'description' => 'Daily essentials, snacks and beverages.'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                array_merge($category, ['status' => Category::STATUS_ACTIVE])
            );
        }
    }
}
