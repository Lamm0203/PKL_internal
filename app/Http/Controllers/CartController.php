<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Menampilkan daftar keranjang
     */
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $cartItems = [];
        $total = 0;
        $totalQuantity = 0;

        foreach ($cart as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $subtotal = $product->display_price * $quantity;
                $total += $subtotal;
                $totalQuantity += $quantity;

                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
            }
        }

        return view('cart.index', compact('cartItems', 'total', 'totalQuantity'));
    }

    /**
     * Tambah produk ke keranjang
     */
    public function add(Request $request, Product $product)
    {
        $quantity = $request->input('quantity', 1);
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id] += $quantity;
        } else {
            $cart[$product->id] = $quantity;
        }

        $request->session()->put('cart', $cart);

        return redirect()->back()->with('success', "{$product->name} berhasil ditambahkan ke keranjang");
    }

    /**
     * Hapus produk dari keranjang
     */
    public function remove(Request $request, Product $product)
    {
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            unset($cart[$product->id]);
            $request->session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', "{$product->name} berhasil dihapus dari keranjang");
    }

    /**
     * Update jumlah produk
     */
    public function update(Request $request, Product $product)
    {
        $quantity = max(1, (int) $request->input('quantity', 1));
        $cart = $request->session()->get('cart', []);

        if (isset($cart[$product->id])) {
            $cart[$product->id] = $quantity;
            $request->session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', "Jumlah {$product->name} berhasil diperbarui");
    }
}
