<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">All Status</option>
                            <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                            <option value="cancelled" @selected(request('status') === 'cancelled')>Cancelled</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                        <a href="{{ route('cashier.orders') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Messages -->
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

            <!-- Orders List -->
            @if ($orders->count())
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <div class="bg-white rounded-lg shadow p-6">
                            <!-- Order Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold">Order #{{ $order->id }}</h3>
                                    <p class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-3xl font-bold text-green-600">${{ number_format($order->total, 2) }}</p>
                                    @if ($order->status === 'completed')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @elseif ($order->status === 'cancelled')
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Cancelled
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Order Details -->
                            <div class="grid grid-cols-3 gap-4 mb-4 pb-4 border-b">
                                <div>
                                    <p class="text-sm text-gray-600">Payment Method</p>
                                    <p class="font-semibold capitalize">{{ $order->payment_method }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Items</p>
                                    <p class="font-semibold">{{ $order->items->count() }} items</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Subtotal</p>
                                    <p class="font-semibold">${{ number_format($order->subtotal, 2) }}</p>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="mb-4">
                                <h4 class="font-semibold mb-2">Items:</h4>
                                <div class="space-y-2">
                                    @foreach ($order->items as $item)
                                        <div class="flex justify-between text-sm">
                                            <span>{{ $item->product_name }} x {{ $item->quantity }}</span>
                                            <span>${{ number_format($item->total, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Refund Info -->
                            @if ($order->refunded_amount > 0)
                                <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                                    <p class="font-semibold text-yellow-800">Refund Info</p>
                                    <p class="text-sm">Refunded: ${{ number_format($order->refunded_amount, 2) }}</p>
                                    @if ($order->refund_reason)
                                        <p class="text-sm">Reason: {{ $order->refund_reason }}</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Actions -->
                            @if ($order->status === 'completed')
                                <div class="flex gap-3">
                                    <button onclick="openCancelModal({{ $order->id }})" class="flex-1 bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                        Cancel Order
                                    </button>
                                    <button onclick="openRefundModal({{ $order->id }})" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                                        Refund Order
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-gray-500">This order cannot be modified</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <p class="text-gray-500 text-lg">No orders found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
            <h3 class="text-xl font-bold mb-4">Cancel Order</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to cancel this order?</p>
            <div class="flex gap-3">
                <button onclick="closeCancelModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    No, Keep It
                </button>
                <form id="cancelForm" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        Yes, Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Refund Order Modal -->
    <div id="refundModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
            <h3 class="text-xl font-bold mb-4">Refund Order</h3>
            <form id="refundForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Refund Reason</label>
                    <textarea name="refund_reason" required class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="3" placeholder="Please enter the reason for refund..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRefundModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                        Refund Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCancelModal(orderId) {
            document.getElementById('cancelForm').action = `/orders/${orderId}/cancel`;
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }

        function openRefundModal(orderId) {
            document.getElementById('refundForm').action = `/orders/${orderId}/refund`;
            document.getElementById('refundModal').classList.remove('hidden');
        }

        function closeRefundModal() {
            document.getElementById('refundModal').classList.add('hidden');
        }

        // Close modals on outside click
        document.addEventListener('click', function(event) {
            const cancelModal = document.getElementById('cancelModal');
            const refundModal = document.getElementById('refundModal');
            
            if (event.target === cancelModal) {
                closeCancelModal();
            }
            if (event.target === refundModal) {
                closeRefundModal();
            }
        });
    </script>
</x-app-layout>
