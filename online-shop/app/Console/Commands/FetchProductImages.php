<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Client\ConnectionException;

class FetchProductImages extends Command
{
    protected $signature = 'products:fetch-images';

    protected $description = 'Download mapped Unsplash images into storage/app/public/products and store local paths on products';

    public function handle(): int
    {
        $mapped = config('product-images');

        if (empty($mapped)) {
            $this->warn('No product image mapping found in config/product-images.php');

            return self::SUCCESS;
        }

        $downloaded = 0;
        $reused = 0;
        $updated = 0;
        $failed = [];

        foreach ($mapped as $name => $url) {
            $filename = Str::slug($name) . '.jpg';
            $path = 'products/' . $filename;

            // Download once, reuse the file on later runs.
            if (Storage::disk('public')->exists($path)) {
                $reused++;
            } else {
                try {
                    $response = Http::timeout(30)->get($url);

                    if (! $response->successful()) {
                        $failed[] = "{$name} (HTTP {$response->status()})";

                        continue;
                    }

                    Storage::disk('public')->put($path, $response->body());
                    $downloaded++;
                } catch (ConnectionException $e) {
                    $failed[] = "{$name} (network error)";

                    continue;
                }
            }

            $count = Product::where('name', $name)->update(['image' => $path]);
            $updated += $count;
        }

        $this->info("Downloaded new images: {$downloaded}");
        $this->info("Reused existing files: {$reused}");
        $this->info("Products updated: {$updated}");

        if (! empty($failed)) {
            $this->warn('Failed (' . count($failed) . '):');
            foreach ($failed as $entry) {
                $this->line("  - {$entry}");
            }
        }

        return self::SUCCESS;
    }
}
