<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /** Catalog prices below are in USD; convert to Nepali Rupees (NPR) on insert. */
    private const USD_TO_NPR = 135;

    public function run(): void
    {
        $sellers = User::where('role', User::ROLE_SELLER)->get();
        $categories = Category::all();

        if ($sellers->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Skipping product seeding: sellers or categories missing.');

            return;
        }

        $productData = [
            ['Wireless Bluetooth Earbuds', 'Electronics', 39.99, 24.99, 85, 'Samsung', 'SoundCore wireless earbuds with noise cancellation and 24h battery life.'],
            ['Samsung Galaxy Smartphone', 'Electronics', 499.99, 449.99, 30, 'Samsung', 'Latest Samsung Galaxy smartphone with stunning AMOLED display and 128GB storage.'],
            ['Apple iPhone 15 Pro', 'Electronics', 1099.99, 999.99, 20, 'Apple', 'Apple iPhone 15 Pro with titanium design and A17 Pro chip.'],
            ['Sony Noise Cancelling Headphones', 'Electronics', 199.99, 159.99, 45, 'Sony', 'Premium over-ear headphones with industry-leading active noise cancellation.'],
            ['HP Laptop 15.6 inch', 'Electronics', 649.99, 599.99, 18, 'HP', 'HP Pavilion laptop with Ryzen 5, 8GB RAM and 512GB SSD.'],
            ['Dell UltraSharp Monitor 27"', 'Electronics', 329.99, 289.99, 12, 'Dell', '27 inch QHD IPS monitor with USB-C hub.'],
            ['Mechanical Gaming Keyboard', 'Electronics', 79.99, 59.99, 60, 'Lenovo', 'RGB mechanical keyboard with hot-swappable switches.'],
            ['Smartwatch Fitness Tracker', 'Electronics', 49.99, 39.99, 120, 'Samsung', 'Fitness smartwatch with heart rate, GPS and sleep tracking.'],
            ['Nike Running Shoes', 'Fashion', 89.99, 69.99, 75, 'Nike', 'Lightweight running shoes with responsive cushioning.'],
            ['Adidas Classic Sneakers', 'Fashion', 74.99, 54.99, 65, 'Adidas', 'Classic three-stripe sneakers in premium leather.'],
            ['Puma Sports T-Shirt', 'Fashion', 29.99, 19.99, 140, 'Puma', 'Breathable cotton sports t-shirt for men.'],
            ['Denim Jacket Men', 'Fashion', 59.99, 44.99, 40, 'Levi\'s', 'Classic blue denim jacket, durable and stylish.'],
            ['Women\'s Summer Dress', 'Fashion', 45.99, 34.99, 55, 'Zara', 'Floral summer dress with flowy fit.'],
            ['Leather Wallet', 'Fashion', 25.99, 18.99, 200, 'Herschel', 'Genuine leather bifold wallet with RFID protection.'],
            ['Cotton Trousers', 'Fashion', 34.99, 27.99, 90, 'H&M', 'Comfortable cotton chinos for everyday wear.'],
            ['Backpack Travel 40L', 'Fashion', 55.99, 42.99, 48, 'Herschel', 'Water-resistant travel backpack with laptop sleeve.'],
            ['Cozy Cotton Blanket', 'Home & Living', 39.99, 29.99, 33, 'IKEA', 'Soft woven throw blanket, 150x200 cm.'],
            ['Non-stick Cookware Set', 'Home & Living', 89.99, 69.99, 22, 'Tefal', '10 piece non-stick cookware set for the modern kitchen.'],
            ['LED Table Lamp', 'Home & Living', 24.99, 17.99, 110, 'Philips', 'Dimmable LED table lamp with touch control.'],
            ['Memory Foam Pillow', 'Home & Living', 34.99, 26.99, 70, 'Casper', 'Cooling memory foam pillow for restful sleep.'],
            ['Ceramic Dinner Set 12pc', 'Home & Living', 64.99, 49.99, 15, 'Corelle', '12 piece dinner set with elegant design.'],
            ['Vitamin C Serum', 'Beauty & Health', 19.99, 14.99, 130, 'The Ordinary', 'Brightening vitamin C serum with hyaluronic acid.'],
            ['Moisturizing Cream', 'Beauty & Health', 24.99, 18.99, 95, 'Nivea', '24h hydration face and body cream.'],
            ['Electric Toothbrush', 'Beauty & Health', 44.99, 34.99, 60, 'Oral-B', 'Sonic electric toothbrush with 3 brushing modes.'],
            ['Dumbbell Set 20kg', 'Sports & Outdoors', 79.99, 59.99, 25, 'Bowflex', 'Adjustable dumbbell set for home workouts.'],
            ['Yoga Mat Pro', 'Sports & Outdoors', 29.99, 22.99, 85, 'Lululemon', 'Non-slip extra thick yoga mat.'],
            ['Camping Tent 4-Person', 'Sports & Outdoors', 119.99, 94.99, 10, 'Coleman', 'Weatherproof camping tent with easy setup.'],
            ['Soccer Ball Size 5', 'Sports & Outdoors', 24.99, 19.99, 100, 'Nike', 'FIFA quality soccer ball for all surfaces.'],
            ['Lego Building Blocks 1000pc', 'Toys & Kids', 54.99, 44.99, 38, 'LEGO', 'Creative building block set, 1000 pieces.'],
            ['Remote Control Car', 'Toys & Kids', 34.99, 27.99, 50, 'Hot Wheels', 'High speed RC car with 30 min playtime.'],
            ['The Great Gatsby (Novel)', 'Books & Stationery', 14.99, 10.99, 75, 'Penguin', 'Classic novel by F. Scott Fitzgerald.'],
            ['Gel Pen Set 12pc', 'Books & Stationery', 9.99, 6.99, 250, 'Pilot', 'Smooth writing gel pens in assorted colors.'],
            ['Premium Notebook A5', 'Books & Stationery', 12.99, 9.99, 180, 'Moleskine', 'Hard cover A5 ruled notebook with ribbon bookmark.'],
            ['Green Tea 100 Bags', 'Grocery', 15.99, 11.99, 90, 'Lipton', 'Refreshing green tea, 100 bags per pack.'],
            ['Extra Virgin Olive Oil 1L', 'Grocery', 18.99, 14.99, 65, 'Bertolli', 'Cold pressed extra virgin olive oil.'],
            ['Instant Coffee Jar', 'Grocery', 22.99, 17.99, 80, 'Nescafe', 'Rich and aromatic instant coffee, 200g.'],
        ];

        foreach ($productData as $index => [$name, $categoryName, $price, $discount, $stock, $brand, $description]) {
            $category = $categories->firstWhere('name', $categoryName) ?? $categories->random();

            $approval = $index % 13 === 0 ? Product::APPROVAL_PENDING : Product::APPROVAL_APPROVED;

            Product::create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $category->id,
                'name' => $name,
                'slug' => Str::slug($name.'-'.Str::random(4)),
                'description' => $description,
                'price' => round($price * self::USD_TO_NPR),
                'discount_price' => round($discount * self::USD_TO_NPR),
                'stock' => $stock,
                'sku' => 'SKU-'.strtoupper(Str::random(8)),
                'brand' => $brand,
                'image' => config('product-images.'.$name) ?? 'https://picsum.photos/seed/product'.$index.'/600/400',
                'status' => Product::STATUS_ACTIVE,
                'approval_status' => $approval,
            ]);
        }

        // A few low-stock and out-of-stock approved products so the inventory pages look alive.
        Product::visible()->orderBy('id')->limit(4)->get()->each(function (Product $product, $key) {
            if ($key === 0) {
                $product->update(['stock' => 2]);
            } elseif ($key === 1) {
                $product->update(['stock' => 0]);
            } elseif ($key === 2) {
                $product->update(['stock' => 5]);
            } else {
                $product->update(['stock' => 8]);
            }
        });
    }
}
