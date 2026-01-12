<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Admin-only access handled via routes middleware
    }

    /**
     * Display a listing of all inventory.
     */
    public function index(): View
    {
        $inventories = Inventory::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $lowStockCount = Inventory::whereRaw('quantity <= low_stock_threshold')->count();
        $outOfStockCount = Inventory::where('quantity', 0)->count();

        return view('inventory.index', compact('inventories', 'lowStockCount', 'outOfStockCount'));
    }

    /**
     * Show the form for creating new inventory.
     */
    public function create(): View
    {
        $products = Product::whereDoesntHave('inventory')->get();

        return view('inventory.create', compact('products'));
    }

    /**
     * Store a newly created inventory in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id|unique:inventories',
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'reorder_level' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        Inventory::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory created successfully!');
    }

    /**
     * Display the specified inventory.
     */
    public function show(Inventory $inventory): View
    {
        return view('inventory.show', compact('inventory'));
    }

    /**
     * Show the form for editing the inventory.
     */
    public function edit(Inventory $inventory): View
    {
        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified inventory in database.
     */
    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'reorder_level' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Inventory updated successfully!');
    }

    /**
     * Delete the specified inventory.
     */
    public function destroy(Inventory $inventory): RedirectResponse
    {
        $product = $inventory->product->name;
        $inventory->delete();

        return redirect()->route('inventory.index')
            ->with('success', "Inventory for {$product} deleted successfully!");
    }

    /**
     * Increase stock for a product.
     */
    public function increaseStock(Request $request, Inventory $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $inventory->increaseStock($validated['quantity']);

        $message = "Stock increased by {$validated['quantity']} for {$inventory->product->name}";
        if ($validated['notes']) {
            $message .= " - {$validated['notes']}";
        }

        return redirect()->route('inventory.show', $inventory)
            ->with('success', $message);
    }

    /**
     * Decrease stock for a product.
     */
    public function decreaseStock(Request $request, Inventory $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!$inventory->decreaseStock($validated['quantity'])) {
            return redirect()->route('inventory.show', $inventory)
                ->with('error', 'Insufficient stock available!');
        }

        $message = "Stock decreased by {$validated['quantity']} for {$inventory->product->name}";
        if ($validated['notes']) {
            $message .= " - {$validated['notes']}";
        }

        return redirect()->route('inventory.show', $inventory)
            ->with('success', $message);
    }

    /**
     * Adjust stock to exact quantity.
     */
    public function adjustStock(Request $request, Inventory $inventory): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $oldQuantity = $inventory->quantity;
        $inventory->setStock($validated['quantity']);

        $difference = $validated['quantity'] - $oldQuantity;
        $action = $difference > 0 ? 'increased' : 'decreased';
        $message = "Stock {$action} by " . abs($difference) . " for {$inventory->product->name}";
        if ($validated['notes']) {
            $message .= " - {$validated['notes']}";
        }

        return redirect()->route('inventory.show', $inventory)
            ->with('success', $message);
    }

    /**
     * View low stock products.
     */
    public function lowStock(): View
    {
        $inventories = Inventory::whereRaw('quantity <= low_stock_threshold')
            ->with('product')
            ->orderBy('quantity', 'asc')
            ->paginate(15);

        return view('inventory.low-stock', compact('inventories'));
    }

    /**
     * View products needing reorder.
     */
    public function needsReorder(): View
    {
        $inventories = Inventory::whereRaw('quantity <= reorder_level')
            ->with('product')
            ->orderBy('quantity', 'asc')
            ->paginate(15);

        return view('inventory.needs-reorder', compact('inventories'));
    }
}
