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
                <div class="mb-8">
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
</x-app-layout>
