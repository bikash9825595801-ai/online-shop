<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Notifications\ProductAwaitingApprovalNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::where('seller_id', Auth::id())->with(['category']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . trim($request->search) . '%');
        }

        if ($request->filled('approval') && in_array($request->approval, ['pending', 'approved', 'rejected'])) {
            $query->where('approval_status', $request->approval);
        }

        $products = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        return view('seller.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::where('status', Category::STATUS_ACTIVE)->orderBy('name')->get();

        return view('seller.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $product = Product::create([
            'seller_id' => Auth::id(),
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(6),
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'sku' => $request->sku,
            'brand' => $request->brand,
            'image' => $request->hasFile('image')
                ? $request->file('image')->store('products', 'public')
                : null,
            'status' => Product::STATUS_ACTIVE,
            'approval_status' => Product::APPROVAL_PENDING,
        ]);

        ProductAwaitingApprovalNotification::send($product);
        AuditService::log('Product created', 'Products', "Product \"{$product->name}\" created by seller.");

        return redirect()->route('seller.products.index')
            ->with('success', 'Product created. It is now pending admin approval.');
    }

    public function edit(Product $product): View
    {
        abort_unless($product->seller_id === Auth::id(), 403);

        $categories = Category::where('status', Category::STATUS_ACTIVE)->orderBy('name')->get();

        return view('seller.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        abort_unless($product->seller_id === Auth::id(), 403);

        $data = $request->only([
            'name', 'category_id', 'description', 'price', 'discount_price',
            'stock', 'sku', 'brand',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && ! str_starts_with($product->image, 'http') && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        if ($request->boolean('remove_image') && $product->image && ! str_starts_with($product->image, 'http')) {
            \Storage::disk('public')->delete($product->image);
            $data['image'] = null;
        }

        // Edits require re-approval so the storefront is never showing outdated stock/price
        $data['approval_status'] = Product::APPROVAL_PENDING;
        $data['status'] = Product::STATUS_ACTIVE;

        $product->update($data);

        ProductAwaitingApprovalNotification::send($product);
        AuditService::log('Product updated', 'Products', "Product \"{$product->name}\" updated by seller (pending re-approval).");

        return redirect()->route('seller.products.index')->with('success', 'Product updated and submitted for re-approval.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        abort_unless($product->seller_id === Auth::id(), 403);

        if ($product->image && ! str_starts_with($product->image, 'http') && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }

        $name = $product->name;
        $product->delete();

        AuditService::log('Product deleted', 'Products', "Product \"{$name}\" deleted by seller.");

        return back()->with('success', 'Product deleted successfully.');
    }
}
