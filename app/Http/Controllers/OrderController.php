<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     */
    public function index(Request $request): View
    {
        $query = Order::with('user', 'items');

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load('user', 'items');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Display financial reports.
     */
    public function reports(Request $request): View
    {
        $query = Order::query();

        // Default date range (last 30 days)
        $fromDate = $request->from_date ? \Carbon\Carbon::parse($request->from_date) : \Carbon\Carbon::now()->subDays(30);
        $toDate = $request->to_date ? \Carbon\Carbon::parse($request->to_date) : \Carbon\Carbon::now();

        $query->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate);

        // Filter by payment method if provided
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $orders = $query->get();

        // Calculate totals
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');
        $totalSubtotal = $orders->sum('subtotal');
        $totalTax = $orders->sum('tax');
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Revenue by payment method
        $paymentMethodReport = Order::whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        // Top products sold
        $topProducts = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.total) as total_revenue')
            ->whereDate('orders.created_at', '>=', $fromDate)
            ->whereDate('orders.created_at', '<=', $toDate)
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Daily revenue
        $dailyRevenue = \DB::table('orders')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total) as total')
            ->whereDate('created_at', '>=', $fromDate)
            ->whereDate('created_at', '<=', $toDate)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.orders.reports', compact(
            'fromDate',
            'toDate',
            'totalOrders',
            'totalRevenue',
            'totalSubtotal',
            'totalTax',
            'averageOrderValue',
            'paymentMethodReport',
            'topProducts',
            'dailyRevenue'
        ));
    }


    /**
     * Cancel an order.
     */
    public function cancel(Order $order): \Illuminate\Http\RedirectResponse
    {
        if ($order->status === 'cancelled') {
            return redirect()->back()
                ->with('error', 'This order is already cancelled.');
        }

        // Restore inventory for cancelled order
        $order->restoreInventory();

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Order cancelled successfully.');
    }

    /**
     * Refund an order.
     */
    public function refund(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'refund_reason' => 'required|string|max:255',
        ]);

        if ($order->status === 'cancelled' && $order->refunded_amount > 0) {
            return redirect()->back()
                ->with('error', 'This order has already been refunded.');
        }

        $refundAmount = $order->total - $order->refunded_amount;

        // Restore inventory for refunded order
        $order->restoreInventory();

        $order->update([
            'status' => 'cancelled',
            'refunded_amount' => $order->refunded_amount + $refundAmount,
            'refund_reason' => $request->refund_reason,
            'refunded_at' => now(),
            'cancelled_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', "Order refunded successfully! Amount: \${$refundAmount}");
    }

    /**
     * Display cashier's orders.
     */
    public function cashierOrders(Request $request): View
    {
        $query = Order::where('user_id', auth()->id())->with('items');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('cashier.orders', compact('orders'));
    }
}
