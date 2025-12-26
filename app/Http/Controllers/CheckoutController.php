<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;

class CheckoutController extends Controller
{
    /**
     * Tampilkan halaman checkout
     */
    public function index()
    {
        $user = auth()->user();

        // Pastikan user punya cart
        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        // Hitung ongkir & total
        $shippingCost = 10000; // bisa diganti sesuai logic
        $total = $cart->items->sum(function ($item) {
            return $item->quantity * ($item->product?->display_price ?? 0);
        }) + $shippingCost;

        return view('checkout.index', compact('cart', 'shippingCost', 'total'));
    }

    /**
     * Proses checkout & buat order
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
        ]);

        // Pastikan cart ada dan tidak kosong
        $cart = Cart::where('user_id', $user->id)->with('items.product')->first();
        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('checkout.index')
                ->with('error', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
        }

        // Hitung total
        $shippingCost = 10000;
        $totalAmount = $cart->items->sum(function ($item) {
            return $item->quantity * ($item->product?->display_price ?? 0);
        }) + $shippingCost;

        // Buat order
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'shipping_name' => $request->name,
            'shipping_address' => $request->address,
            'shipping_phone' => $request->phone,
            'total_amount' => $totalAmount,
            'shipping_cost' => $shippingCost,
        ]);

        // Buat order items dari cart
        foreach ($cart->items as $item) {
            $product = $item->product;

            $price = $product?->display_price ?? 0;
            $subtotal = $item->quantity * $price;

            $order->items()->create([
                'product_id' => $product?->id,
                'product_name' => $product?->name ?? 'Produk tidak tersedia',
                'quantity' => $item->quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ]);
        }

        // Kosongkan cart
        $cart->items()->delete();

        return redirect()->route('checkout.index')
            ->with('success', 'Pesanan berhasil dibuat!');
    }
}
