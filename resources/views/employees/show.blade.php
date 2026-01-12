<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Employee Details</h1>
            <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ← Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <!-- Employee Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Full Name</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $employee->name }}</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Position</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $employee->role }}</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Monthly Salary</p>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($employee->salary, 2) }}</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Hire Date</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $employee->hire_date->format('M d, Y') }}</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">System Role</p>
                    <p class="text-xl">
                        <span class="px-3 py-1 rounded-full font-semibold {{ $employee->system_role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($employee->system_role) }}
                        </span>
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Employee Since</p>
                    <p class="text-lg text-gray-800">{{ $employee->hire_date->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-lg mb-8">
                <p class="text-sm text-gray-600 mb-2">Record Created</p>
                <p class="text-gray-800">{{ $employee->created_at->format('M d, Y \a\t h:i A') }}</p>
                <p class="text-sm text-gray-600 mt-4 mb-2">Last Updated</p>
                <p class="text-gray-800">{{ $employee->updated_at->format('M d, Y \a\t h:i A') }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <a href="{{ route('employees.edit', $employee) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded">
                    Edit Employee
                </a>
                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded">
                        Delete Employee
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
