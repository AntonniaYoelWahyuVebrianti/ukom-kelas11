<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 10));

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null,
        ]);

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url'],
        ]);

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null,
        ]);

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->products()->update([
            'category_id' => null,
        ]);

        $category->delete();

        return response()->json(status: 204);
    }
}
