<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Point of Sale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ $message }}
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ $message }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Products Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-2xl font-bold mb-6">Products</h3>

                        @if ($products->count())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($products as $product)
                                    <div class="border rounded-lg p-4 hover:shadow-lg transition">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h4 class="font-bold text-lg">{{ $product->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $product->category->name }}</p>
                                            </div>
                                        </div>

                                        <p class="text-2xl font-bold text-green-600 mb-4">${{ number_format($product->price, 2) }}</p>

                                        <form action="{{ route('cart.add') }}" method="POST" class="flex gap-2">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="number" name="quantity" value="1" min="1" class="w-16 px-2 py-2 border border-gray-300 rounded" placeholder="Qty">
                                            <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                                                Add to Cart
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No active products available.</p>
                        @endif
                    </div>
                </div>

                <!-- Cart Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                        <h3 class="text-2xl font-bold mb-4">Shopping Cart</h3>

                        @if (count($cart) > 0)
                            <div class="mb-6 max-h-96 overflow-y-auto">
                                @foreach ($cart as $productId => $item)
                                    <div class="mb-4 pb-4 border-b">
                                        <div class="flex justify-between mb-2">
                                            <div class="flex-1">
                                                <p class="font-semibold">{{ $item['name'] }}</p>
                                                <p class="text-xs text-gray-600">{{ $item['category'] }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('cart.remove', $productId) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 ml-2">×</button>
                                            </form>
                                        </div>

                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-sm font-semibold">${{ number_format($item['price'], 2) }}</span>
                                            <form method="POST" action="{{ route('cart.update', $productId) }}" class="flex gap-1 items-center">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-12 px-2 py-1 border border-gray-300 rounded text-sm">
                                                <button type="submit" class="text-xs bg-gray-200 hover:bg-gray-300 px-2 py-1 rounded">Update</button>
                                            </form>
                                        </div>

                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Qty: {{ $item['quantity'] }}</span>
                                            <span class="font-bold">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Cart Summary -->
                            <div class="border-t pt-4 space-y-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Subtotal:</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>

                                <div class="flex justify-between text-lg font-bold text-green-600">
                                    <span>Total:</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>

                                <div class="pt-4 space-y-2">
                                    <a href="{{ route('pos.checkout') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded text-center">
                                        Checkout
                                    </a>
                                    <form method="POST" action="{{ route('cart.clear') }}" style="display: inline-block; width: 100%;">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Clear cart?')">
                                            Clear Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500 mb-4">Cart is empty</p>
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                <p class="text-gray-400 text-sm">Add products to get started</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
