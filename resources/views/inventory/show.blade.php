<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventory Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <!-- Back Link -->
        <a href="{{ route('inventory.index') }}" class="text-blue-600 hover:text-blue-900 font-medium mb-6 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Inventory
        </a>

        <!-- Product Card -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $inventory->product->name }}</h1>
                    <p class="text-gray-600 mt-2">{{ $inventory->product->category->name ?? 'N/A' }} • ${{ number_format($inventory->product->price, 2) }}</p>
                </div>
                <a href="{{ route('inventory.edit', $inventory) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
                    Edit
                </a>
            </div>

            <!-- Stock Status -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Current Stock</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $inventory->quantity }}</p>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg">
                    <p class="text-sm text-gray-600">Low Stock Level</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $inventory->low_stock_threshold }}</p>
                </div>
                <div class="p-4 bg-orange-50 rounded-lg">
                    <p class="text-sm text-gray-600">Reorder Level</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $inventory->reorder_level }}</p>
                </div>
                <div class="p-4 rounded-lg 
                    @if ($inventory->getStockStatus() === 'Out of Stock')
                        bg-red-50
                    @elseif ($inventory->getStockStatus() === 'Needs Reorder')
                        bg-orange-50
                    @elseif ($inventory->getStockStatus() === 'Low Stock')
                        bg-yellow-50
                    @else
                        bg-green-50
                    @endif
                ">
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-lg font-bold
                        @if ($inventory->getStockStatus() === 'Out of Stock')
                            text-red-600
                        @elseif ($inventory->getStockStatus() === 'Needs Reorder')
                            text-orange-600
                        @elseif ($inventory->getStockStatus() === 'Low Stock')
                            text-yellow-600
                        @else
                            text-green-600
                        @endif
                    ">
                        {{ $inventory->getStockStatus() }}
                    </p>
                </div>
            </div>

            <!-- Notes -->
            @if ($inventory->notes)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 font-medium">Notes</p>
                    <p class="text-gray-900 mt-2">{{ $inventory->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Success Message -->
        @if ($message = session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">
                {{ $message }}
            </div>
        @endif

        <!-- Stock Adjustment Forms -->
        <div class="grid grid-cols-2 gap-6">
            <!-- Increase Stock -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Increase Stock</h3>
                <form method="POST" action="{{ route('inventory.increase-stock', $inventory) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="increase_quantity" class="block text-sm font-medium text-gray-900">Quantity</label>
                        <input type="number" id="increase_quantity" name="quantity" required min="1" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="increase_notes" class="block text-sm font-medium text-gray-900">Notes</label>
                        <input type="text" id="increase_notes" name="notes" placeholder="e.g., Restock from supplier" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                        Increase Stock
                    </button>
                </form>
            </div>

            <!-- Decrease Stock -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Decrease Stock</h3>
                <form method="POST" action="{{ route('inventory.decrease-stock', $inventory) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="decrease_quantity" class="block text-sm font-medium text-gray-900">Quantity</label>
                        <input type="number" id="decrease_quantity" name="quantity" required min="1" max="{{ $inventory->quantity }}" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="decrease_notes" class="block text-sm font-medium text-gray-900">Notes</label>
                        <input type="text" id="decrease_notes" name="notes" placeholder="e.g., Damaged items" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg">
                        Decrease Stock
                    </button>
                </form>
            </div>
        </div>

        <!-- Adjust to Exact -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Stock Count Adjustment</h3>
            <p class="text-gray-600 mb-4">Set stock to exact quantity (e.g., after physical inventory count)</p>
            <form method="POST" action="{{ route('inventory.adjust-stock', $inventory) }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="adjust_quantity" class="block text-sm font-medium text-gray-900">New Quantity</label>
                        <input type="number" id="adjust_quantity" name="quantity" required min="0" value="{{ $inventory->quantity }}" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="adjust_notes" class="block text-sm font-medium text-gray-900">Notes</label>
                        <input type="text" id="adjust_notes" name="notes" placeholder="e.g., Physical count completed" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                    Adjust Stock
                </button>
            </form>
        </div>

        <!-- Last Check Info -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg text-sm text-gray-600">
            Last stock check: {{ $inventory->last_stock_check ? $inventory->last_stock_check->format('M d, Y H:i A') : 'Never' }}
        </div>
        </div>
    </div>
</x-app-layout>