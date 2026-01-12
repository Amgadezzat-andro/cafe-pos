<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with(['supplier', 'product'])
            ->latest('purchase_date')
            ->paginate(15);
        
        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        
        return view('purchases.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $purchase = DB::transaction(function () use ($validated) {
                // Create the purchase record
                $purchase = Purchase::create($validated);

                // Get or create inventory record
                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $validated['product_id']],
                    [
                        'quantity' => 0,
                        'low_stock_threshold' => 10,
                        'reorder_level' => 5,
                    ]
                );

                // Increase inventory stock
                $inventory->increment('quantity', $validated['quantity']);

                return $purchase;
            });

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase created successfully and inventory updated.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create purchase: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'product']);
        
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0.01',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($purchase, $validated) {
                $oldQuantity = $purchase->quantity;
                $oldProductId = $purchase->product_id;
                $newQuantity = $validated['quantity'];
                $newProductId = $validated['product_id'];

                // If product changed, adjust both old and new inventory
                if ($oldProductId !== $newProductId) {
                    // Decrease old product inventory
                    $oldInventory = Inventory::where('product_id', $oldProductId)->first();
                    if ($oldInventory) {
                        $oldInventory->decrement('quantity', $oldQuantity);
                    }

                    // Get or create new product inventory and increase it
                    $newInventory = Inventory::firstOrCreate(
                        ['product_id' => $newProductId],
                        [
                            'quantity' => 0,
                            'low_stock_threshold' => 10,
                            'reorder_level' => 5,
                        ]
                    );
                    $newInventory->increment('quantity', $newQuantity);
                } else {
                    // Same product, just adjust quantity difference
                    $quantityDifference = $newQuantity - $oldQuantity;
                    $inventory = Inventory::where('product_id', $oldProductId)->first();
                    if ($inventory) {
                        if ($quantityDifference >= 0) {
                            $inventory->increment('quantity', $quantityDifference);
                        } else {
                            $inventory->decrement('quantity', abs($quantityDifference));
                        }
                    }
                }

                // Update purchase
                $purchase->update($validated);
            });

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase updated successfully and inventory adjusted.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update purchase: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        try {
            DB::transaction(function () use ($purchase) {
                // Decrease inventory by purchase quantity
                $inventory = Inventory::where('product_id', $purchase->product_id)->first();
                if ($inventory) {
                    $inventory->decrement('quantity', $purchase->quantity);
                }

                // Delete purchase
                $purchase->delete();
            });

            return redirect()->route('purchases.index')
                ->with('success', 'Purchase deleted and inventory adjusted.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete purchase: ' . $e->getMessage());
        }
    }
}
