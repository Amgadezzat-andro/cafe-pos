<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    // Dashboard - show different summary based on user role
    $user = auth()->guard('web')->user();
    
    if ($user && $user->hasRole('admin')) {
        // Admin dashboard with financial summary
        $today = \Carbon\Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        
        // Today's summary
        $todayOrders = \App\Models\Order::whereDate('created_at', $today)->count();
        $todayRevenue = \App\Models\Order::whereDate('created_at', $today)->sum('total');
        
        // This month's summary
        $monthOrders = \App\Models\Order::whereBetween('created_at', [$startOfMonth, $today])->count();
        $monthRevenue = \App\Models\Order::whereBetween('created_at', [$startOfMonth, $today])->sum('total');
        
        // Top products this month
        $topProducts = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.total) as total_revenue')
            ->whereBetween('orders.created_at', [$startOfMonth, $today])
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
        
        // Revenue by payment method (this month)
        $paymentMethodReport = \App\Models\Order::whereBetween('created_at', [$startOfMonth, $today])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();
        
        return view('dashboard', compact('todayOrders', 'todayRevenue', 'monthOrders', 'monthRevenue', 'topProducts', 'paymentMethodReport'));
    }
    
    if ($user && $user->hasRole('cashier')) {
        // Cashier dashboard with their order summary
        $today = \Carbon\Carbon::now();
        $startOfMonth = $today->copy()->startOfMonth();
        
        // Cashier's today's summary
        $todayOrders = \App\Models\Order::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();
        $todayRevenue = \App\Models\Order::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->sum('total');
        
        // Cashier's this month summary
        $monthOrders = \App\Models\Order::where('user_id', $user->id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->count();
        $monthRevenue = \App\Models\Order::where('user_id', $user->id)
            ->whereBetween('created_at', [$startOfMonth, $today])
            ->sum('total');
        
        // Cashier's top products this month
        $topProducts = \Illuminate\Support\Facades\DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.total) as total_revenue')
            ->where('orders.user_id', $user->id)
            ->whereBetween('orders.created_at', [$startOfMonth, $today])
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
        
        return view('dashboard', compact('todayOrders', 'todayRevenue', 'monthOrders', 'monthRevenue', 'topProducts'));
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat routes for all authenticated users
    // Important: Specific routes MUST come before parameterized routes!
    Route::get('/chat/unread-count', [ChatController::class, 'unreadCount'])->name('chat.unread-count');
    Route::get('/chat/unread-by-partner', [ChatController::class, 'unreadByPartner'])->name('chat.unread-by-partner');
    Route::get('/chat/partners', [ChatController::class, 'partners'])->name('chat.partners');
    Route::get('/chat/{user}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/{user}', [ChatController::class, 'store'])->name('chat.store');
});

// Admin routes - only accessible by admin role
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Category CRUD routes
    Route::resource('categories', CategoryController::class);

    // Product CRUD routes
    Route::resource('products', ProductController::class);

    // Order management and reports
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/reports/financial', [OrderController::class, 'reports'])->name('orders.reports');
});

// Cashier routes - only accessible by cashier role
Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/pos', [CartController::class, 'index'])->name('pos.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{productId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/pos/checkout', [CartController::class, 'checkout'])->name('pos.checkout');
    Route::post('/pos/complete-checkout', [CartController::class, 'completeCheckout'])->name('pos.complete-checkout');
    Route::get('/order/{order}/receipt', [CartController::class, 'receipt'])->name('order.receipt');
    
    // Cashier order management
    Route::get('/my-orders', [OrderController::class, 'cashierOrders'])->name('cashier.orders');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
    Route::post('/orders/{order}/refund', [OrderController::class, 'refund'])->name('order.refund');
});

require __DIR__.'/auth.php';
