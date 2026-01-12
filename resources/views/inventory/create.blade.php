<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Add Inventory</h1>
                <p class="text-gray-600 mt-2">Create a new inventory record for a product</p>
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

            <form method="POST" action="{{ route('inventory.store') }}" class="space-y-6">
                @csrf

                <!-- Product Selection -->
                <div>
                    <label for="product_id" class="block text-sm font-medium text-gray-900">Product *</label>
                    <select id="product_id" name="product_id" required class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('product_id') border-red-500 @enderror">
                        <option value="">Select a product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                {{ $product->name }} ({{ $product->category->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Initial Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-900">Initial Quantity *</label>
                    <input type="number" id="quantity" name="quantity" required min="0" value="{{ old('quantity', 0) }}" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('quantity') border-red-500 @enderror">
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Low Stock Threshold -->
                <div>
                    <label for="low_stock_threshold" class="block text-sm font-medium text-gray-900">Low Stock Alert Level *</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" required min="1" value="{{ old('low_stock_threshold', 10) }}" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('low_stock_threshold') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-600">Alert will trigger when stock falls below this level</p>
                    @error('low_stock_threshold')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reorder Level -->
                <div>
                    <label for="reorder_level" class="block text-sm font-medium text-gray-900">Reorder Level *</label>
                    <input type="number" id="reorder_level" name="reorder_level" required min="1" value="{{ old('reorder_level', 20) }}" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('reorder_level') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-gray-600">Recommend reorder when stock falls below this level</p>
                    @error('reorder_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-900">Notes</label>
                    <textarea id="notes" name="notes" rows="4" class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                        Create Inventory
                    </button>
                    <a href="{{ route('inventory.index') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium rounded-lg">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
        </div>
    </div>
</x-app-layout>