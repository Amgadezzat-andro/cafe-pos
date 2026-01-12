<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('purchases.edit', $purchase) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('purchases.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <!-- Purchase Date -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
                        <p class="mt-2 text-lg font-semibold text-gray-900">{{ $purchase->purchase_date->format('Y-m-d') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reference ID</label>
                        <p class="mt-2 text-lg font-semibold text-gray-900">#{{ str_pad($purchase->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>

                <!-- Supplier & Product -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supplier</label>
                        <p class="mt-2 text-lg font-semibold text-gray-900">{{ $purchase->supplier->name }}</p>
                        <p class="text-sm text-gray-600">
                            @if ($purchase->supplier->email)
                                <a href="mailto:{{ $purchase->supplier->email }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $purchase->supplier->email }}
                                </a>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Product</label>
                        <p class="mt-2 text-lg font-semibold text-gray-900">{{ $purchase->product->name }}</p>
                        <p class="text-sm text-gray-600">SKU: {{ $purchase->product->sku ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Quantity & Price -->
                <div class="grid grid-cols-3 gap-6 bg-gray-50 p-4 rounded">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <p class="mt-2 text-2xl font-bold text-blue-600">{{ $purchase->quantity }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit Price</label>
                        <p class="mt-2 text-2xl font-bold text-gray-900">${{ number_format($purchase->purchase_price, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Cost</label>
                        <p class="mt-2 text-2xl font-bold text-green-600">${{ number_format($purchase->getTotalCost(), 2) }}</p>
                    </div>
                </div>

                <!-- Notes -->
                @if ($purchase->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <p class="mt-2 text-gray-900">{{ $purchase->notes }}</p>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="border-t pt-4 text-xs text-gray-500">
                    <p>Created: {{ $purchase->created_at->format('Y-m-d H:i') }}</p>
                    <p>Updated: {{ $purchase->updated_at->format('Y-m-d H:i') }}</p>
                </div>

                <!-- Delete Button -->
                <div class="border-t pt-4">
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Are you sure? This will decrease inventory by {{ $purchase->quantity }}.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded">
                            Delete Purchase
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
