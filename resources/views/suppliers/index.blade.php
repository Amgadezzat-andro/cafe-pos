<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Suppliers') }}
            </h2>
            <a href="{{ route('suppliers.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                + Add Supplier
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

            <!-- Suppliers Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if ($suppliers->count())
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($suppliers as $supplier)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $supplier->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->phone ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->email ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->address ? Str::limit($supplier->address, 30) : '-' }}</td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-900 font-semibold">View</a>
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-yellow-600 hover:text-yellow-900 font-semibold">Edit</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
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
                        {{ $suppliers->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No suppliers found.</p>
                        <a href="{{ route('suppliers.create') }}" class="text-blue-600 hover:text-blue-900 font-semibold">Create the first supplier</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
