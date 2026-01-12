<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Monthly Expense Report') }}
            </h2>
            <a href="{{ route('expenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                Back to Expenses
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filter Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form action="{{ route('expenses.monthly-report') }}" method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                            <input type="number" name="year" id="year" min="2020" max="2099" value="{{ $year }}"
                                   class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded w-full">
                                Filter Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Total Year Expenses</p>
                    <p class="text-3xl font-bold text-red-600">${{ number_format($totalAmount, 2) }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Months with Expenses</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $monthlyExpenses->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-600">Average Per Month</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($averageAmount, 2) }}</p>
                </div>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if ($monthlyExpenses->count())
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Number of Expenses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Total Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Average</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($monthlyExpenses as $monthly)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $monthly->month_name }} {{ $year }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $monthly->count }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-red-600">${{ number_format($monthly->total, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($monthly->total / $monthly->count, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-6 text-center text-gray-500">
                        <p class="text-lg">No expenses found for the year {{ $year }}.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
