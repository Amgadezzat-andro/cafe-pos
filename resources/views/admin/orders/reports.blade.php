<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Range Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Report Period</h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">All Methods</option>
                            <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                            <option value="card" @selected(request('payment_method') === 'card')>Card</option>
                            <option value="check" @selected(request('payment_method') === 'check')>Check</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Total Orders</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $totalOrders }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($totalRevenue, 2) }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Total Subtotal</p>
                    <p class="text-3xl font-bold text-purple-600">${{ number_format($totalSubtotal, 2) }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Total Tax</p>
                    <p class="text-3xl font-bold text-orange-600">${{ number_format($totalTax, 2) }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Avg Order Value</p>
                    <p class="text-3xl font-bold text-indigo-600">${{ number_format($averageOrderValue, 2) }}</p>
                </div>
            </div>

            <!-- Payment Method Report -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-xl font-bold mb-4">Revenue by Payment Method</h3>

                @if ($paymentMethodReport->count())
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Payment Method</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Transactions</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Total Revenue</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">% of Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($paymentMethodReport as $report)
                                    <tr>
                                        <td class="px-6 py-4 font-semibold capitalize">{{ $report->payment_method }}</td>
                                        <td class="px-6 py-4 text-right">{{ $report->count }}</td>
                                        <td class="px-6 py-4 text-right font-bold text-green-600">${{ number_format($report->total, 2) }}</td>
                                        <td class="px-6 py-4 text-right">{{ number_format(($report->total / $totalRevenue) * 100, 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Products -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold mb-4">Top 10 Products Sold</h3>

                    @if ($topProducts->count())
                        <div class="space-y-3">
                            @foreach ($topProducts as $product)
                                <div class="flex justify-between items-center pb-3 border-b">
                                    <div>
                                        <p class="font-semibold">{{ $product->product_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $product->total_quantity }} units</p>
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

                <!-- Daily Revenue -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-xl font-bold mb-4">Daily Revenue</h3>

                    @if ($dailyRevenue->count())
                        <div class="space-y-3">
                            @foreach ($dailyRevenue as $daily)
                                <div class="flex justify-between items-center pb-3 border-b">
                                    <div>
                                        <p class="font-semibold">{{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ $daily->count }} orders</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-green-600">${{ number_format($daily->total, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No sales data available.</p>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 space-x-3">
                <a href="{{ route('orders.index') }}" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                    Back to Orders
                </a>
                <button onclick="window.print()" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                    Print Report
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
