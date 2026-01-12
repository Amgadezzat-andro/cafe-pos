<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header with Title and Add Button -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Employees</h1>
            <a href="{{ route('employees.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                + Add Employee
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Total Employees</p>
                <p class="text-3xl font-bold text-blue-600">{{ $totalEmployees }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Total Payroll</p>
                <p class="text-3xl font-bold text-green-600">${{ number_format($totalPayroll, 2) }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-sm text-gray-600">Average Salary</p>
                <p class="text-3xl font-bold text-purple-600">${{ number_format($avgSalary, 2) }}</p>
            </div>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Employees Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Position</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">System Role</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Salary</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Hire Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $employee->name }}</td>
                            <td class="px-6 py-4">{{ $employee->role }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $employee->system_role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($employee->system_role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">${{ number_format($employee->salary, 2) }}</td>
                            <td class="px-6 py-4">{{ $employee->hire_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="{{ route('employees.show', $employee) }}" class="text-blue-500 hover:text-blue-700 text-sm font-semibold">View</a>
                                <a href="{{ route('employees.edit', $employee) }}" class="text-yellow-500 hover:text-yellow-700 text-sm font-semibold">Edit</a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No employees found. <a href="{{ route('employees.create') }}" class="text-blue-500 hover:text-blue-700">Create one</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $employees->links() }}
        </div>
    </div>
</x-app-layout>
