<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('categoryModel:id,name')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 12));

        return response()->json($products);
    }

    public function store(Request $request)
    {
    $data = $this->validatedData($request);

        $product = Product::create($data);

        return response()->json($product->load('categoryModel:id,name'), 201);
    }

    public function update(Request $request, Product $product)
    {
    $data = $this->validatedData($request, $product);

        $product->update($data);

        return response()->json($product->fresh('categoryModel:id,name'));
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(status: 204);
    }

    protected function validatedData(Request $request, ?Product $product = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('products', 'slug')->ignore($product?->id)],
            'sku' => ['nullable', 'string', 'max:120', Rule::unique('products', 'sku')->ignore($product?->id)],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'image' => ['nullable', 'image', 'max:2048'],
            'stock' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $validator->sometimes('image_url', 'required_without:image', function () use ($product, $request) {
            if ($request->hasFile('image')) {
                return false;
            }

            return ! $product?->image_url;
        });

        $validator->sometimes('image', 'required_without:image_url', function () use ($product, $request) {
            if ($request->filled('image_url')) {
                return false;
            }

            return ! $product?->image_url;
        });

        $data = $validator->validate();

        if (isset($data['image'])) {
            unset($data['image']);
        }

    $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
    $data['sku'] = isset($data['sku']) && $data['sku'] !== '' ? $data['sku'] : null;
    $data['category_id'] = isset($data['category_id']) && $data['category_id'] !== '' ? (int) $data['category_id'] : null;
    $data['description'] = isset($data['description']) && $data['description'] !== '' ? $data['description'] : null;
    $data['stock'] = (int) $data['stock'];
    $data['price'] = (int) $data['price'];

        if ($request->hasFile('image')) {
            if ($product && $product->image_url) {
                $previousPath = parse_url($product->image_url, PHP_URL_PATH);

                if ($previousPath && str_starts_with($previousPath, '/storage/')) {
                    $relativePath = ltrim(substr($previousPath, strlen('/storage/')), '/');

                    if ($relativePath && Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                }
            }

            $storedPath = $request->file('image')->store('products', 'public');
            $data['image_url'] = Storage::disk('public')->url($storedPath);
        }

        if (empty($data['image_url'] ?? null)) {
            $data['image_url'] = null;
        }

        $categoryName = null;
        if (! empty($data['category_id'])) {
            $categoryName = Category::find($data['category_id'])?->name;
        }

        $data['category'] = $categoryName;

        return $data;
    }
}
