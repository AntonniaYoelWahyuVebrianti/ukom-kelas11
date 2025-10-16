<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private ?string $newSessionToken = null;

    public function index(Request $request): JsonResponse
    {
        [$ownerId, $sessionId] = $this->resolveOwner($request);

        $items = CartItem::query()
            ->with('product')
            ->when($ownerId, fn ($query) => $query->where('user_id', $ownerId))
            ->when(! $ownerId, fn ($query) => $query->where('session_id', $sessionId))
            ->orderByDesc('created_at')
            ->get();

        return $this->respond($this->formatCart($items));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        [$ownerId, $sessionId] = $this->resolveOwner($request);

        $product = Product::findOrFail($data['product_id']);

        if ($product->stock < 1) {
            return $this->respond([
                'message' => 'Product is out of stock.',
            ], 422);
        }

        $quantity = min($data['quantity'] ?? 1, $product->stock);

        $item = CartItem::query()
            ->when($ownerId, fn ($query) => $query->where('user_id', $ownerId))
            ->when(! $ownerId, fn ($query) => $query->where('session_id', $sessionId))
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->quantity = min($item->quantity + $quantity, $product->stock);
            $item->save();
        } else {
            $item = CartItem::create([
                'product_id' => $product->id,
                'user_id' => $ownerId,
                'session_id' => $ownerId ? null : $sessionId,
                'quantity' => $quantity,
            ]);
        }

        $items = CartItem::query()
            ->with('product')
            ->when($ownerId, fn ($query) => $query->where('user_id', $ownerId))
            ->when(! $ownerId, fn ($query) => $query->where('session_id', $sessionId))
            ->get();

        return $this->respond($this->formatCart($items), 201);
    }

    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        [$ownerId, $sessionId] = $this->resolveOwner($request);

        if (($ownerId && $cartItem->user_id !== $ownerId) || (! $ownerId && $cartItem->session_id !== $sessionId)) {
            abort(404);
        }

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = $cartItem->product;
        $newQuantity = $data['quantity'];

        if ($product && $newQuantity > $product->stock) {
            $newQuantity = $product->stock;
        }

        $cartItem->update([
            'quantity' => max($newQuantity, 1),
        ]);

        $items = CartItem::query()
            ->with('product')
            ->when($ownerId, fn ($query) => $query->where('user_id', $ownerId))
            ->when(! $ownerId, fn ($query) => $query->where('session_id', $sessionId))
            ->get();

        return $this->respond($this->formatCart($items));
    }

    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        [$ownerId, $sessionId] = $this->resolveOwner($request);

        if (($ownerId && $cartItem->user_id !== $ownerId) || (! $ownerId && $cartItem->session_id !== $sessionId)) {
            abort(404);
        }

        $cartItem->delete();

        $items = CartItem::query()
            ->with('product')
            ->when($ownerId, fn ($query) => $query->where('user_id', $ownerId))
            ->when(! $ownerId, fn ($query) => $query->where('session_id', $sessionId))
            ->get();

        return $this->respond($this->formatCart($items));
    }

    public function claim(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['nullable', 'uuid'],
        ]);

        $user = $request->user();
        $token = $request->input('token') ?? $request->cookie('cart_token');

        if (! $user || ! $token) {
            return response()->json(['status' => 'ignored']);
        }

        $guestItems = CartItem::query()
            ->where('session_id', $token)
            ->get();

        foreach ($guestItems as $guestItem) {
            $existing = CartItem::query()
                ->where('user_id', $user->id)
                ->where('product_id', $guestItem->product_id)
                ->first();

            if ($existing) {
                $product = $guestItem->product;
                $stock = $product?->stock ?? PHP_INT_MAX;
                $existing->quantity = min($existing->quantity + $guestItem->quantity, $stock);
                $existing->save();
                $guestItem->delete();
            } else {
                $guestItem->update([
                    'session_id' => null,
                    'user_id' => $user->id,
                ]);
            }
        }

        $items = CartItem::query()->with('product')->where('user_id', $user->id)->get();

        return response()->json($this->formatCart($items));
    }

    private function resolveOwner(Request $request): array
    {
        if (Auth::check()) {
            return [Auth::id(), null];
        }

        $token = $request->cookie('cart_token');
        if (! $token) {
            $token = (string) Str::uuid();
            $this->newSessionToken = $token;
        }

        return [null, $token];
    }

    private function respond(array $payload, int $status = 200): JsonResponse
    {
        $response = response()->json($payload, $status);

        if ($this->newSessionToken) {
            $response->cookie('cart_token', $this->newSessionToken, 60 * 24 * 30);
        }

        return $response;
    }

    private function formatCart($items): array
    {
        $subtotal = 0;

        $data = $items->map(function (CartItem $item) use (&$subtotal) {
            $product = $item->product;
            $lineTotal = $product ? $product->price * $item->quantity : 0;
            $subtotal += $lineTotal;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'line_total' => $lineTotal,
                'product' => $product ? [
                    'name' => $product->name,
                    'price' => $product->price,
                    'image_url' => $product->image_url,
                    'slug' => $product->slug,
                    'stock' => $product->stock,
                ] : null,
            ];
        })->values();

        return [
            'items' => $data,
            'subtotal' => $subtotal,
        ];
    }
}
