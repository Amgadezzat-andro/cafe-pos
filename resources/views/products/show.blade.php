<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __($product->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold mb-6">{{ $product->name }}</h1>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Product ID</p>
                    <p class="text-lg text-gray-900">{{ $product->id }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Category</p>
                    <p class="text-lg text-gray-900">{{ $product->category->name }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Price</p>
                    <p class="text-lg text-gray-900 font-semibold">${{ number_format($product->price, 2) }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Status</p>
                    @if ($product->is_active)
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Inactive
                        </span>
                    @endif
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Created At</p>
                    <p class="text-lg text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</p>
                </div>

                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Updated At</p>
                    <p class="text-lg text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('products.edit', $product) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-center">
                        Edit
                    </a>
                    <a href="{{ route('products.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded text-center">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
