<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the POS cart page.
     */
    public function index(): View
    {
        $cart = session()->get('cart', []);
        $products = Product::where('is_active', true)->with('category')->get();
        
        $total = $this->calculateTotal($cart);
        $itemCount = count($cart);

        return view('pos.index', compact('cart', 'products', 'total', 'itemCount'));
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);

        // Check if product already in cart
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $request->quantity;
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'category' => $product->category->name,
                'quantity' => $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('pos.index')
            ->with('success', $product->name . ' added to cart!');
    }

    /**
     * Update product quantity in cart.
     */
    public function update(Request $request, $productId): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $request->quantity;
            session()->put('cart', $cart);

            return redirect()->route('pos.index')
                ->with('success', 'Cart updated!');
        }

        return redirect()->route('pos.index')
            ->with('error', 'Product not found in cart.');
    }

    /**
     * Remove product from cart.
     */
    public function remove($productId): RedirectResponse
    {
        $cart = session()->get('cart', []);
        $productName = $cart[$productId]['name'] ?? 'Product';

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);

            return redirect()->route('pos.index')
                ->with('success', $productName . ' removed from cart!');
        }

        return redirect()->route('pos.index')
            ->with('error', 'Product not found in cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('cart');

        return redirect()->route('pos.index')
            ->with('success', 'Cart cleared!');
    }

    /**
     * Checkout - create order from cart.
     */
    public function checkout(): View|RedirectResponse
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos.index')
                ->with('error', 'Cart is empty!');
        }

        $total = $this->calculateTotal($cart);
        $subtotal = $total;
        $tax = $subtotal * 0.05; // 5% tax
        $grandTotal = $subtotal + $tax;

        return view('pos.checkout', compact('cart', 'subtotal', 'tax', 'grandTotal'));
    }

    /**
     * Complete the checkout and clear cart.
     */
    public function completeCheckout(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,check',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos.index')
                ->with('error', 'Cart is empty!');
        }

        // Here you would typically create an Order record in the database
        // For now, we'll just clear the cart and show a success message
        
        $total = $this->calculateTotal($cart);
        
        session()->forget('cart');

        return redirect()->route('pos.index')
            ->with('success', "Order completed! Total: \${$total}");
    }

    /**
     * Calculate cart total.
     */
    private function calculateTotal(array $cart): float
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
}
