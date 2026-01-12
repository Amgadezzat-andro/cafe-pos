<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchases') }}
            </h2>
            <a href="{{ route('purchases.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                + New Purchase
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Purchases Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if ($purchases->count())
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Qty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Total Cost</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($purchases as $purchase)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $purchase->supplier->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->product->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($purchase->purchase_price, 2) }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-green-600">${{ number_format($purchase->getTotalCost(), 2) }}</td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600 hover:text-blue-900 font-semibold">View</a>
                                        <a href="{{ route('purchases.edit', $purchase) }}" class="text-yellow-600 hover:text-yellow-900 font-semibold">Edit</a>
                                        <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="inline" onsubmit="return confirm('This will decrease inventory. Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t">
                        {{ $purchases->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No purchases found.</p>
                        <a href="{{ route('purchases.create') }}" class="text-blue-600 hover:text-blue-900 font-semibold">Record your first purchase</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
