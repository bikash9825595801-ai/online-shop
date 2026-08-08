<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Notifications\ProductApprovalNotification;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'seller']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('brand', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('approval') && in_array($request->approval, ['pending', 'approved', 'rejected'])) {
            $query->where('approval_status', $request->approval);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status') && in_array($request->status, ['active', 'inactive'])) {
            $query->where('status', $request->status);
        }

        $products = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'seller', 'reviews.customer']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->only([
            'name', 'category_id', 'description', 'price', 'discount_price',
            'stock', 'sku', 'brand', 'status',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && ! str_starts_with($product->image, 'http') && \Storage::disk('public')->exists($product->image)) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        AuditService::log('Product updated', 'Products', "Product \"{$product->name}\" updated by admin.");

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function approve(Product $product): RedirectResponse
    {
        $oldStatus = $product->approval_status;
        $product->update(['approval_status' => Product::APPROVAL_APPROVED]);

        ProductApprovalNotification::send($product->fresh());
        AuditService::log('Product approved', 'Products', "Product \"{$product->name}\" approved.");

        return back()->with('success', "Product \"{$product->name}\" approved and now visible on the storefront.");
    }

    public function reject(Request $request, Product $product): RedirectResponse
    {
        $request->validate(['reason' => ['nullable', 'string', 'max:1000']]);

        $product->update(['approval_status' => Product::APPROVAL_REJECTED]);

        ProductApprovalNotification::send($product->fresh());
        AuditService::log('Product rejected', 'Products', "Product \"{$product->name}\" rejected.");

        return back()->with('success', "Product \"{$product->name}\" rejected.");
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'status' => $product->status === Product::STATUS_ACTIVE ? Product::STATUS_INACTIVE : Product::STATUS_ACTIVE,
        ]);

        AuditService::log('Product status changed', 'Products', "Product \"{$product->name}\" set to {$product->status}.");

        return back()->with('success', 'Product status updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $name = $product->name;

        if ($product->image && ! str_starts_with($product->image, 'http') && \Storage::disk('public')->exists($product->image)) {
            \Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        AuditService::log('Product deleted', 'Products', "Product \"{$name}\" deleted.");

        return back()->with('success', 'Product deleted successfully.');
    }
}
