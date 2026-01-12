<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expenses') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('expenses.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    + Add Expense
                </a>
                <a href="{{ route('expenses.daily-report') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                    Daily Report
                </a>
                <a href="{{ route('expenses.monthly-report') }}" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
                    Monthly Report
                </a>
            </div>
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

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Today's Expenses</p>
                    <p class="text-3xl font-bold text-red-600">${{ number_format(\App\Models\Expense::todayTotal(), 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-3xl font-bold text-orange-600">${{ number_format(\App\Models\Expense::monthTotal(), 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">This Year</p>
                    <p class="text-3xl font-bold text-yellow-600">${{ number_format(\App\Models\Expense::yearTotal(), 2) }}</p>
                </div>
            </div>

            <!-- Expenses Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if ($expenses->count())
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Notes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($expenses as $expense)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->expense_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $expense->title }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-red-600">${{ number_format($expense->amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $expense->notes ? Str::limit($expense->notes, 30) : '-' }}</td>
                                    <td class="px-6 py-4 text-sm space-x-2">
                                        <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:text-blue-900 font-semibold">View</a>
                                        <a href="{{ route('expenses.edit', $expense) }}" class="text-yellow-600 hover:text-yellow-900 font-semibold">Edit</a>
                                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
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
                        {{ $expenses->links() }}
                    </div>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No expenses found.</p>
                        <a href="{{ route('expenses.create') }}" class="text-blue-600 hover:text-blue-900 font-semibold">Record your first expense</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
