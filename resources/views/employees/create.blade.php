<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Add New Employee</h1>

        <div class="bg-white rounded-lg shadow p-8">
            <form action="{{ route('employees.store') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position/Role -->
                <div class="mb-6">
                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Position/Role *</label>
                    <input type="text" id="role" name="role" value="{{ old('role') }}" placeholder="e.g., Manager, Barista, Cashier" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror">
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Salary -->
                <div class="mb-6">
                    <label for="salary" class="block text-sm font-semibold text-gray-700 mb-2">Monthly Salary *</label>
                    <div class="relative">
                        <span class="absolute left-4 top-2 text-gray-500">$</span>
                        <input type="number" id="salary" name="salary" value="{{ old('salary') }}" step="0.01" min="0" required placeholder="0.00" class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('salary') border-red-500 @enderror">
                    </div>
                    @error('salary')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hire Date -->
                <div class="mb-6">
                    <label for="hire_date" class="block text-sm font-semibold text-gray-700 mb-2">Hire Date *</label>
                    <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date', now()->toDateString()) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hire_date') border-red-500 @enderror">
                    @error('hire_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- System Role -->
                <div class="mb-6">
                    <label for="system_role" class="block text-sm font-semibold text-gray-700 mb-2">System Role *</label>
                    <select id="system_role" name="system_role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('system_role') border-red-500 @enderror">
                        <option value="">-- Select System Role --</option>
                        @foreach ($systemRoles as $sysRole)
                            <option value="{{ $sysRole }}" {{ old('system_role') === $sysRole ? 'selected' : '' }}>
                                {{ ucfirst($sysRole) }}
                            </option>
                        @endforeach
                    </select>
                    @error('system_role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Create Account Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="create_account" name="create_account" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-gray-300" onchange="toggleAccountFields()">
                        <label for="create_account" class="ml-2 block text-sm font-semibold text-gray-700">Create System Account</label>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Enable this employee to log in and access the POS system</p>

                    <!-- Email (hidden by default) -->
                    <div class="mb-4" id="email_field" style="display: none;">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password (hidden by default) -->
                    <div class="mb-4" id="password_field" style="display: none;">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password (hidden by default) -->
                    <div id="password_confirm_field" style="display: none;">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                        Create Employee
                    </button>
                    <a href="{{ route('employees.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-6 rounded">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAccountFields() {
            const checkbox = document.getElementById('create_account');
            const emailField = document.getElementById('email_field');
            const passwordField = document.getElementById('password_field');
            const passwordConfirmField = document.getElementById('password_confirm_field');
            
            if (checkbox.checked) {
                emailField.style.display = 'block';
                passwordField.style.display = 'block';
                passwordConfirmField.style.display = 'block';
            } else {
                emailField.style.display = 'none';
                passwordField.style.display = 'none';
                passwordConfirmField.style.display = 'none';
            }
        }
    </script>
</x-app-layout>
