<!-- Admin Sidebar -->
<div class="hidden md:flex md:flex-col md:w-64 bg-gray-900 text-white h-full">
    <div class="flex-1 overflow-y-auto px-2 py-4 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('dashboard')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-2m0 0l4-2"></path>
                </svg>
                <span>Dashboard</span>
            </div>
        </a>

        <!-- Divider -->
        <div class="my-2 border-t border-gray-700"></div>

        <!-- Management Section -->
        <div class="px-2 py-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Management</p>
        </div>

        <!-- Categories -->
        <a href="{{ route('categories.index') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('categories.*')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span>Categories</span>
            </div>
        </a>

        <!-- Products -->
        <a href="{{ route('products.index') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('products.*')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8-4m0 0l8 4m0 0v10l-8 4-8-4v-10m8 4v-4m0 4l8 4m0 0l-8-4m0 0l-8 4"></path>
                </svg>
                <span>Products</span>
            </div>
        </a>

        <!-- Inventory -->
        <a href="{{ route('inventory.index') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('inventory.*')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Inventory</span>
            </div>
        </a>

        <!-- Suppliers -->
        <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('suppliers.*')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.048M12 4.354L8.646 7.707a4 4 0 116.708 0L12 4.354M12 20H8m4 0h4m0 0v-4m0 4v4"></path>
                </svg>
                <span>Suppliers</span>
            </div>
        </a>

        <!-- Purchases -->
        <a href="{{ route('purchases.index') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('purchases.*')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span>Purchases</span>
            </div>
        </a>

        <!-- Divider -->
        <div class="my-2 border-t border-gray-700"></div>

        <!-- Orders Section -->
        <div class="px-2 py-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Orders</p>
        </div>

        <!-- Orders -->
        <a href="{{ route('orders.index') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('orders.index', 'orders.show')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span>All Orders</span>
            </div>
        </a>

        <!-- Financial Reports -->
        <a href="{{ route('orders.reports') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('orders.reports')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>Financial Reports</span>
            </div>
        </a>

        <!-- Expenses -->
        <a href="{{ route('expenses.index') }}" class="block px-4 py-2 rounded-lg transition @if(request()->routeIs('expenses.*')) bg-blue-600 @else hover:bg-gray-800 @endif">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Expenses</span>
            </div>
        </a>
    </div>

    <!-- Footer Info -->
    <div class="border-t border-gray-700 p-4 text-xs text-gray-400">
        <p class="mb-2">Admin Panel</p>
        <p>{{ auth()->user()->name }}</p>
    </div>
</div>

<!-- Mobile Menu Toggle -->
<div class="md:hidden bg-gray-900 text-white p-4">
    <details class="cursor-pointer">
        <summary class="flex items-center font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            Admin Menu
        </summary>
        <div class="mt-4 space-y-2 ml-4">
            <a href="{{ route('dashboard') }}" class="block py-2 hover:text-blue-400">Dashboard</a>
            <a href="{{ route('categories.index') }}" class="block py-2 hover:text-blue-400">Categories</a>
            <a href="{{ route('products.index') }}" class="block py-2 hover:text-blue-400">Products</a>
            <a href="{{ route('inventory.index') }}" class="block py-2 hover:text-blue-400">Inventory</a>
            <a href="{{ route('orders.index') }}" class="block py-2 hover:text-blue-400">All Orders</a>
            <a href="{{ route('orders.reports') }}" class="block py-2 hover:text-blue-400">Financial Reports</a>
        </div>
    </details>
</div>
