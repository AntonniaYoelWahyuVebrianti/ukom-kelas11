<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $cartItems = $user->cartItems()->with('product')->get();
        $addresses = $user->addresses()->orderByDesc('is_default')->orderByDesc('created_at')->get();

        return response()->json([
            'cart' => $this->formatCart($cartItems),
            'addresses' => $addresses,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Your cart is empty.',
            ], 422);
        }

        $data = $request->validate([
            'address_id' => ['nullable', 'exists:user_addresses,id'],
            'address' => ['nullable', 'array'],
            'address.label' => ['nullable', 'string', 'max:100'],
            'address.recipient_name' => ['required_without:address_id', 'string', 'max:150'],
            'address.phone' => ['nullable', 'string', 'max:30'],
            'address.line1' => ['required_without:address_id', 'string', 'max:255'],
            'address.line2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['required_without:address_id', 'string', 'max:120'],
            'address.state' => ['nullable', 'string', 'max:120'],
            'address.postal_code' => ['nullable', 'string', 'max:20'],
            'address.country' => ['nullable', 'string', 'max:120'],
        ]);

        $address = null;

        if (! empty($data['address_id'])) {
            $address = UserAddress::where('user_id', $user->id)
                ->where('id', $data['address_id'])
                ->firstOrFail();
        } else {
            $addressPayload = $data['address'];
            $addressPayload['user_id'] = $user->id;
            $addressPayload['is_default'] = $user->addresses()->doesntExist();
            $addressPayload['country'] = $addressPayload['country'] ?? 'Indonesia';
            $address = UserAddress::create($addressPayload);
        }

        $cartSummary = $this->formatCart($cartItems);

        $order = DB::transaction(function () use ($user, $address, $cartItems, $cartSummary) {
            $order = Order::create([
                'user_id' => $user->id,
                'user_address_id' => $address->id,
                'status' => 'pending',
                'subtotal' => $cartSummary['subtotal'],
                'shipping_fee' => 0,
                'total' => $cartSummary['subtotal'],
            ]);

            foreach ($cartItems as $item) {
                $product = $item->product;
                if (! $product) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'quantity' => $item->quantity,
                    'total' => $product->price * $item->quantity,
                ]);

                if ($product->stock >= $item->quantity) {
                    $product->decrement('stock', $item->quantity);
                }
            }

            CartItem::where('user_id', $user->id)->delete();

            return $order;
        });

        $order->load(['items.product', 'address']);

        return response()->json([
            'message' => 'Order created. Proceed to payment when ready.',
            'order' => $order,
        ], 201);
    }

    protected function formatCart($items): array
    {
        $subtotal = 0;

        $data = $items->map(function (CartItem $item) use (&$subtotal) {
            $product = $item->product;
            $line = $product ? $product->price * $item->quantity : 0;
            $subtotal += $line;

            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'line_total' => $line,
                'product' => $product ? [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image_url' => $product->image_url,
                ] : null,
            ];
        })->values();

        return [
            'items' => $data,
            'subtotal' => $subtotal,
        ];
    }
}
