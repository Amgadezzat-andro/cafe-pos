<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $user = auth()->user();
                $isAdmin = $user && $user->role?->value === 'admin';
                $isCashier = $user && $user->role?->value === 'cashier';
            @endphp

            @if ($isAdmin)
                <!-- Admin Dashboard with Financial Summary -->
                
                <!-- Today's Summary -->
                <div class="mb-6">
                    <h3 class="text-2xl font-bold mb-4">Today's Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Orders Today</p>
                            <p class="text-4xl font-bold text-blue-600">{{ $todayOrders ?? 0 }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Revenue Today</p>
                            <p class="text-4xl font-bold text-green-600">${{ number_format($todayRevenue ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- This Month's Summary -->
                <div class="mb-6">
                    <h3 class="text-2xl font-bold mb-4">This Month's Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Orders This Month</p>
                            <p class="text-4xl font-bold text-blue-600">{{ $monthOrders ?? 0 }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Revenue This Month</p>
                            <p class="text-4xl font-bold text-green-600">${{ number_format($monthRevenue ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Top Products -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold">Top 5 Products (This Month)</h3>
                            <a href="{{ route('orders.reports') }}" class="text-blue-600 hover:text-blue-900 text-sm">View All</a>
                        </div>

                        @if (isset($topProducts) && $topProducts->count())
                            <div class="space-y-3">
                                @foreach ($topProducts as $product)
                                    <div class="flex justify-between items-center pb-3 border-b">
                                        <div>
                                            <p class="font-semibold">{{ $product->product_name }}</p>
                                            <p class="text-sm text-gray-600">{{ $product->total_quantity }} units sold</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600">${{ number_format($product->total_revenue, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No sales data available.</p>
                        @endif
                    </div>

                    <!-- Payment Methods -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold">Payment Methods (This Month)</h3>
                            <a href="{{ route('orders.reports') }}" class="text-blue-600 hover:text-blue-900 text-sm">View All</a>
                        </div>

                        @if (isset($paymentMethodReport) && $paymentMethodReport->count())
                            <div class="space-y-3">
                                @foreach ($paymentMethodReport as $report)
                                    <div class="flex justify-between items-center pb-3 border-b">
                                        <div>
                                            <p class="font-semibold capitalize">{{ $report->payment_method }}</p>
                                            <p class="text-sm text-gray-600">{{ $report->count }} transactions</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600">${{ number_format($report->total, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No transaction data available.</p>
                        @endif
                    </div>
                </div>

                <!-- Inventory Section -->
                <div class="mt-6">
                    <h3 class="text-2xl font-bold mb-4">Inventory Overview</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Total Products Tracked</p>
                            <p class="text-3xl font-bold text-indigo-600">{{ $totalProductsTracked ?? 0 }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Low Stock Alerts</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $lowStockCount ?? 0 }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Products Needing Reorder</p>
                            <p class="text-3xl font-bold text-red-600">{{ $needsReorderCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('categories.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-6 rounded text-center">
                        Manage Categories
                    </a>
                    <a href="{{ route('products.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-4 px-6 rounded text-center">
                        Manage Products
                    </a>
                    <a href="{{ route('inventory.index') }}" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-4 px-6 rounded text-center">
                        Manage Inventory
                    </a>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('suppliers.index') }}" class="bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-4 px-6 rounded text-center">
                        Manage Suppliers
                    </a>
                    <a href="{{ route('purchases.index') }}" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-4 px-6 rounded text-center">
                        Record Purchases
                    </a>
                    <a href="{{ route('orders.index') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded text-center">
                        View Orders
                    </a>
                </div>

                <div class="mt-4">
                    <a href="{{ route('orders.reports') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-4 px-6 rounded text-center block">
                        Financial Reports
                    </a>
                </div>
            @elseif ($isCashier)
                <!-- Cashier Dashboard with Personal Order Summary -->

                <div class="mb-6">
                    <h3 class="text-2xl font-bold mb-4">Your Performance Today</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Orders Today</p>
                            <p class="text-4xl font-bold text-blue-600">{{ $todayOrders ?? 0 }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Revenue Today</p>
                            <p class="text-4xl font-bold text-green-600">${{ number_format($todayRevenue ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-2xl font-bold mb-4">This Month's Performance</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Orders This Month</p>
                            <p class="text-4xl font-bold text-blue-600">{{ $monthOrders ?? 0 }}</p>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">Revenue This Month</p>
                            <p class="text-4xl font-bold text-green-600">${{ number_format($monthRevenue ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-xl font-bold">Your Top Products (This Month)</h3>
                            <a href="{{ route('cashier.orders') }}" class="text-blue-600 hover:text-blue-900 text-sm">View All Orders</a>
                        </div>

                        @if (isset($topProducts) && $topProducts->count())
                            <div class="space-y-3">
                                @foreach ($topProducts as $product)
                                    <div class="flex justify-between items-center pb-3 border-b">
                                        <div>
                                            <p class="font-semibold">{{ $product->product_name }}</p>
                                            <p class="text-sm text-gray-600">{{ $product->total_quantity }} units sold</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-green-600">${{ number_format($product->total_revenue, 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No sales data available.</p>
                        @endif
                    </div>
                </div>

                <!-- Quick Links for Cashier -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('pos.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-4 px-6 rounded text-center">
                        Start POS
                    </a>
                    <a href="{{ route('cashier.orders') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded text-center">
                        View My Orders
                    </a>
                </div>

            @else
                <!-- Regular User Dashboard -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
