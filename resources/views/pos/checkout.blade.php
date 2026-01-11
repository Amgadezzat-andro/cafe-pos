<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Order Summary -->
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-2xl font-bold mb-6">Order Summary</h3>

                        <div class="space-y-4">
                            @foreach ($cart as $item)
                                <div class="flex justify-between items-center pb-4 border-b">
                                    <div class="flex-1">
                                        <p class="font-semibold">{{ $item['name'] }}</p>
                                        <p class="text-sm text-gray-600">{{ $item['category'] }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">{{ $item['quantity'] }} × ${{ number_format($item['price'], 2) }}</p>
                                        <p class="font-bold">${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-xl font-bold mb-4">Payment Method</h3>

                        <form action="{{ route('pos.complete-checkout') }}" method="POST">
                            @csrf

                            <div class="space-y-3 mb-6">
                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50" onclick="document.getElementById('payment_cash').checked=true">
                                    <input type="radio" id="payment_cash" name="payment_method" value="cash" checked class="w-4 h-4 text-blue-600" required>
                                    <span class="ml-3 font-semibold">Cash</span>
                                </label>

                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50" onclick="document.getElementById('payment_card').checked=true">
                                    <input type="radio" id="payment_card" name="payment_method" value="card" class="w-4 h-4 text-blue-600" required>
                                    <span class="ml-3 font-semibold">Card</span>
                                </label>

                                <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-blue-50" onclick="document.getElementById('payment_check').checked=true">
                                    <input type="radio" id="payment_check" name="payment_method" value="check" class="w-4 h-4 text-blue-600" required>
                                    <span class="ml-3 font-semibold">Check</span>
                                </label>
                            </div>

                            <div class="flex gap-3">
                                <a href="{{ route('pos.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-4 rounded text-center">
                                    Back to Cart
                                </a>
                                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded">
                                    Complete Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="md:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-6">
                        <h3 class="text-xl font-bold mb-6">Total</h3>

                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax (5%):</span>
                                <span class="font-semibold">${{ number_format($tax, 2) }}</span>
                            </div>

                            <div class="border-t pt-4 flex justify-between text-lg">
                                <span class="font-bold">Grand Total:</span>
                                <span class="font-bold text-green-600">${{ number_format($grandTotal, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded p-4">
                            <p class="text-sm text-blue-800">
                                <strong>Items:</strong> {{ count($cart) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
