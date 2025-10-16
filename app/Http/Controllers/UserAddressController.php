<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->orderByDesc('created_at')->get();

        return response()->json($addresses);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $address = $request->user()->addresses()->create($data);

        if ($address->is_default) {
            $this->markOthersAsNonDefault($address);
        }

        return response()->json($address, 201);
    }

    public function update(Request $request, UserAddress $userAddress)
    {
        $this->authorizeOwnership($request, $userAddress);

        $data = $this->validatePayload($request, $userAddress->id);

        $userAddress->update($data);

        if ($userAddress->is_default) {
            $this->markOthersAsNonDefault($userAddress);
        }

        return response()->json($userAddress);
    }

    public function destroy(Request $request, UserAddress $userAddress)
    {
        $this->authorizeOwnership($request, $userAddress);

        $userAddress->delete();

        return response()->json(['status' => 'deleted']);
    }

    public function setDefault(Request $request, UserAddress $userAddress)
    {
        $this->authorizeOwnership($request, $userAddress);

        $userAddress->update(['is_default' => true]);
        $this->markOthersAsNonDefault($userAddress);

        return response()->json($userAddress->fresh());
    }

    protected function authorizeOwnership(Request $request, UserAddress $address): void
    {
        if ($address->user_id !== $request->user()->id) {
            abort(403);
        }
    }

    protected function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'label' => ['nullable', 'string', 'max:100'],
            'recipient_name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'line1' => ['required', 'string', 'max:255'],
            'line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:120'],
            'is_default' => ['sometimes', 'boolean'],
        ], [], [
            'line1' => 'address line',
        ]);
    }

    protected function markOthersAsNonDefault(UserAddress $address): void
    {
        if (! $address->is_default) {
            return;
        }

        UserAddress::query()
            ->where('user_id', $address->user_id)
            ->where('id', '!=', $address->id)
            ->update(['is_default' => false]);
    }
}
