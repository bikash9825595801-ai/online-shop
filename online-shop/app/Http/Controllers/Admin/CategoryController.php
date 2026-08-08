<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'description' => $request->description,
            'image' => $request->hasFile('image')
                ? $request->file('image')->store('categories', 'public')
                : null,
            'status' => Category::STATUS_ACTIVE,
        ]);

        AuditService::log('Category created', 'Categories', "Category \"{$category->name}\" created.");

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            if ($category->image && \Storage::disk('public')->exists($category->image)) {
                \Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        AuditService::log('Category updated', 'Categories', "Category \"{$category->name}\" updated.");

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function toggleStatus(Category $category): RedirectResponse
    {
        $category->update([
            'status' => $category->isActive() ? Category::STATUS_INACTIVE : Category::STATUS_ACTIVE,
        ]);

        AuditService::log('Category status changed', 'Categories', "Category \"{$category->name}\" set to {$category->status}.");

        return back()->with('success', "Category \"{$category->name}\" is now {$category->status}.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete a category that contains products.');
        }

        $name = $category->name;
        $category->delete();

        AuditService::log('Category deleted', 'Categories', "Category \"{$name}\" deleted.");

        return back()->with('success', 'Category deleted successfully.');
    }
}
