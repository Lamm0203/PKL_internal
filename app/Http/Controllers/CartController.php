<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    // Menampilkan keranjang
    public function index()
    {
        // Ambil semua cart user beserta item & product + primaryImage
        $carts = Cart::with('items.product.primaryImage')
            ->where('user_id', auth()->id())
            ->get();

        return view('cart.index', compact('carts'));
    }

    // Menambahkan produk ke keranjang
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        $this->cartService->addProduct($product, $request->quantity);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    // Update quantity item di cart
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::findOrFail($itemId);
        $item->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    // Hapus item dari keranjang
    public function remove($itemId)
    {
        CartItem::findOrFail($itemId)->delete();

        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
