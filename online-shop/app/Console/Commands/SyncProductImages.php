<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncProductImages extends Command
{
    protected $signature = 'products:sync-images';

    protected $description = 'Update product images to real Unsplash photos matched by product name';

    public function handle(): int
    {
        $mapped = config('product-images');

        if (empty($mapped)) {
            $this->warn('No product image mapping found in config/product-images.php');

            return self::SUCCESS;
        }

        $products = Product::all();
        $updated = 0;
        $matched = 0;
        $unmatched = [];

        foreach ($products as $product) {
            if (! isset($mapped[$product->name])) {
                $unmatched[] = $product->name;

                continue;
            }

            $matched++;

            if ($product->image === $mapped[$product->name]) {
                continue;
            }

            $product->update(['image' => $mapped[$product->name]]);
            $updated++;
        }

        $this->info("Products processed: {$products->count()}");
        $this->info("Matched by name: {$matched}");
        $this->info("Images updated: {$updated}");

        $unmatched = array_values(array_unique($unmatched));

        if (! empty($unmatched)) {
            $this->warn('Products without a mapped image (' . count($unmatched) . '):');
            foreach ($unmatched as $name) {
                $this->line("  - {$name}");
            }
        }

        return self::SUCCESS;
    }
}
