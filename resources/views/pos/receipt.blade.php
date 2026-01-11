<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Receipt') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <!-- Header -->
                <div class="text-center mb-8 pb-8 border-b-2">
                    <h1 class="text-3xl font-bold mb-2">Cafe POS</h1>
                    <p class="text-gray-600">Order Receipt</p>
                </div>

                <!-- Order Info -->
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div>
                        <p class="text-sm text-gray-600">Order #</p>
                        <p class="text-lg font-bold">{{ $order->id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Order Date</p>
                        <p class="text-lg font-bold">{{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Cashier</p>
                        <p class="text-lg font-bold">{{ $order->user->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="text-lg font-bold capitalize">{{ $order->payment_method }}</p>
                    </div>
                </div>

                <!-- Items -->
                <div class="mb-8">
                    <h3 class="font-bold text-lg mb-4">Order Items</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 border-b-2">
                                <tr>
                                    <th class="text-left px-4 py-2">Product</th>
                                    <th class="text-right px-4 py-2">Price</th>
                                    <th class="text-right px-4 py-2">Qty</th>
                                    <th class="text-right px-4 py-2">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <p class="font-semibold">{{ $item->product_name }}</p>
                                        </td>
                                        <td class="text-right px-4 py-3">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-right px-4 py-3">{{ $item->quantity }}</td>
                                        <td class="text-right px-4 py-3 font-bold">${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-gray-50 rounded p-6 mb-8 border-2">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-700">Subtotal:</span>
                            <span class="font-semibold">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-700">Tax (5%):</span>
                            <span class="font-semibold">${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="border-t-2 pt-3 flex justify-between text-xl">
                            <span class="font-bold">Total Amount:</span>
                            <span class="font-bold text-green-600">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center border-t-2 pt-6">
                    <p class="text-gray-600 text-sm mb-4">Thank you for your purchase!</p>
                    <div class="space-x-3">
                        <a href="{{ route('pos.index') }}" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                            New Order
                        </a>
                        <button onclick="window.print()" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                            Print Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
