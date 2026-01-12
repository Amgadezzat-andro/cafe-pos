<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Inventory</h1>
                <p class="text-gray-600 mt-2">{{ $inventory->product->name }}</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg">
                    <strong>Validation errors:</strong>
                    <ul class="list-disc list-inside mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('inventory.update', $inventory) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Product Name (Read-only) -->
                <div>
                    <label for="product_name" class="block text-sm font-medium text-gray-900">Product</label>
                    <input type="text" id="product_name" value="{{ $inventory->product->name }}" readonly class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                </div>

                <!-- Current Quantity (Read-only) -->
                <div>
                    <label for="current_quantity" class="block text-sm font-medium text-gray-900">Current Quantity</label>
                    <input type="number" id="current_quantity" value="{{ $inventory->quantity }}" readonly class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                    <p class="mt-1 text-sm text-gray-600">Use the "View" option to adjust quantity</p>
                </div>

                <!-- Low Stock Threshold -->
                <div>
                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-900">Low Stock Alert Level *</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" required min="1" value="{{ old('low_stock_threshold', $inventory->low_stock_threshold) }}" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('low_stock_threshold') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-600">Alert will trigger when stock falls below this level</p>
                    @error('low_stock_threshold')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reorder Level -->
                <div>
                    <label for="reorder_level" class="block text-sm font-medium text-gray-900">Reorder Level *</label>
                    <input type="number" id="reorder_level" name="reorder_level" required min="1" value="{{ old('reorder_level', $inventory->reorder_level) }}" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('reorder_level') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-600">Recommend reorder when stock falls below this level</p>
                    @error('reorder_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-900">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes', $inventory->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                        Save Changes
                    </button>
                    <a href="{{ route('inventory.show', $inventory) }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium rounded-lg">
                        Cancel
                    </a>
                </div>
            </form>

            <!-- Delete Section -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-bold text-red-600 mb-4">Danger Zone</h3>
                <p class="text-gray-600 mb-4">Delete this inventory record. This action cannot be undone.</p>
                <form method="POST" action="{{ route('inventory.destroy', $inventory) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg" onclick="return confirm('Are you sure? This will delete the inventory record for {{ $inventory->product->name }}.')">
                        Delete Inventory
                    </button>
                </form>
            </div>
        </div>
        </div>
    </div>
</x-app-layout>