<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Employee Details</h1>
            <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                ← Back to List
            </a>
        </div>

        <!-- Employee Information -->
        <div class="bg-white rounded-lg shadow p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6">Personal Information</h2>
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
        </div>

        <!-- System Account Section -->
        <div class="bg-white rounded-lg shadow p-8 mb-6">
            <h2 class="text-2xl font-bold mb-6">System Account</h2>
            
            @if ($employee->user)
                <!-- Account exists -->
                <div class="bg-green-50 border border-green-200 p-6 rounded-lg mb-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-lg font-semibold text-green-800">Account Active</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded border border-green-200">
                            <p class="text-sm text-gray-600">Email Address</p>
                            <p class="font-semibold text-gray-800">{{ $employee->user->email }}</p>
                        </div>
                        <div class="bg-white p-4 rounded border border-green-200">
                            <p class="text-sm text-gray-600">Account Created</p>
                            <p class="font-semibold text-gray-800">{{ $employee->user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <p class="text-sm text-green-700 mt-4">This employee can now log in to the system and access the POS.</p>
                </div>
            @else
                <!-- No account yet -->
                <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg mb-6">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-lg font-semibold text-yellow-800">No Account Created</span>
                    </div>

                    <p class="text-sm text-yellow-700 mb-6">This employee does not yet have a system account. Create one to allow them to access the POS and view their orders.</p>

                    <form action="{{ route('employees.create-account', $employee) }}" method="POST" class="bg-white p-6 rounded border border-yellow-200">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                            <input type="email" id="email" name="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                            <input type="password" id="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded">
                            Create Account
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-4 mb-6">
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
</x-app-layout>
