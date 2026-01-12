<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supplier Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('suppliers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Name -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <p class="mt-2 text-lg font-semibold text-gray-900">{{ $supplier->name }}</p>
                </div>

                <!-- Phone -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <p class="mt-2 text-gray-900">
                        @if ($supplier->phone)
                            <a href="tel:{{ $supplier->phone }}" class="text-blue-600 hover:text-blue-900">
                                {{ $supplier->phone }}
                            </a>
                        @else
                            <span class="text-gray-500">Not provided</span>
                        @endif
                    </p>
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <p class="mt-2 text-gray-900">
                        @if ($supplier->email)
                            <a href="mailto:{{ $supplier->email }}" class="text-blue-600 hover:text-blue-900">
                                {{ $supplier->email }}
                            </a>
                        @else
                            <span class="text-gray-500">Not provided</span>
                        @endif
                    </p>
                </div>

                <!-- Address -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <p class="mt-2 text-gray-900">
                        @if ($supplier->address)
                            {{ $supplier->address }}
                        @else
                            <span class="text-gray-500">Not provided</span>
                        @endif
                    </p>
                </div>

                <!-- Timestamps -->
                <div class="border-t pt-4 text-xs text-gray-500">
                    <p>Created: {{ $supplier->created_at->format('Y-m-d H:i') }}</p>
                    <p>Updated: {{ $supplier->updated_at->format('Y-m-d H:i') }}</p>
                </div>

                <!-- Delete Button -->
                <div class="mt-6">
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded">
                            Delete Supplier
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
