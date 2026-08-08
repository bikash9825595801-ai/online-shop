<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $price = fake()->randomFloat(2, 10, 500);

        return [
            'seller_id' => User::factory()->seller(),
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'price' => $price,
            'discount_price' => fake()->boolean(40) ? fake()->randomFloat(2, 5, $price - 1) : null,
            'stock' => fake()->numberBetween(0, 100),
            'sku' => 'SKU-' . strtoupper(Str::random(8)),
            'brand' => fake()->randomElement(['Nike', 'Adidas', 'Samsung', 'Apple', 'Sony', 'LG', 'HP', 'Dell', 'Puma', 'Lenovo']),
            'image' => null,
            'status' => Product::STATUS_ACTIVE,
            'approval_status' => Product::APPROVAL_APPROVED,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['approval_status' => Product::APPROVAL_PENDING]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['approval_status' => Product::APPROVAL_REJECTED]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => Product::STATUS_INACTIVE]);
    }

    public function lowStock(int $threshold = 10): static
    {
        return $this->state(fn () => ['stock' => fake()->numberBetween(0, $threshold)]);
    }
}
