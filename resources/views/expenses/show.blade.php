<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expense Details') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('expenses.edit', $expense) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('expenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6 space-y-6">
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $expense->title }}</p>
                </div>

                <!-- Amount & Date -->
                <div class="grid grid-cols-2 gap-6 bg-gray-50 p-4 rounded">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                        <p class="mt-2 text-3xl font-bold text-red-600">${{ number_format($expense->amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expense Date</label>
                        <p class="mt-2 text-lg font-semibold text-gray-900">{{ $expense->expense_date->format('Y-m-d') }}</p>
                    </div>
                </div>

                <!-- Notes -->
                @if ($expense->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <p class="mt-2 text-gray-900">{{ $expense->notes }}</p>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="border-t pt-4 text-xs text-gray-500">
                    <p>Created: {{ $expense->created_at->format('Y-m-d H:i') }}</p>
                    <p>Updated: {{ $expense->updated_at->format('Y-m-d H:i') }}</p>
                </div>

                <!-- Delete Button -->
                <div class="border-t pt-4">
                    <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Are you sure? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded">
                            Delete Expense
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
