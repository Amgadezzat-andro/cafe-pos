<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Order #</p>
                        <p class="text-2xl font-bold">{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Date</p>
                        <p class="text-lg font-semibold">{{ $order->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Cashier</p>
                        <p class="text-lg font-semibold">{{ $order->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p>
                            @if ($order->status === 'completed')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Completed
                                </span>
                            @elseif ($order->status === 'pending')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Cancelled
                                </span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 pt-6 border-t">
                    <div>
                        <p class="text-sm text-gray-600">Payment Method</p>
                        <p class="text-lg font-semibold capitalize">{{ $order->payment_method }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Items Count</p>
                        <p class="text-lg font-semibold">{{ $order->items->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Order Items</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Product Name</th>
                                <th class="px-4 py-3 text-right">Price</th>
                                <th class="px-4 py-3 text-right">Quantity</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-4 py-3">{{ $item->product_name }}</td>
                                    <td class="px-4 py-3 text-right font-semibold">${{ number_format($item->price, 2) }}</td>
                                    <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-green-600">${{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">Order Summary</h3>

                <div class="space-y-3 max-w-md ml-auto">
                    <div class="flex justify-between">
                        <span class="text-gray-700">Subtotal:</span>
                        <span class="font-semibold">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700">Tax (5%):</span>
                        <span class="font-semibold">${{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="border-t-2 pt-3 flex justify-between text-lg">
                        <span class="font-bold">Total Amount:</span>
                        <span class="font-bold text-green-600">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                        Back to Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
