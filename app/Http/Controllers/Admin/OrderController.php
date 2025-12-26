<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Services\MidtransService;

class OrderController extends Controller
{
    /**
     * Menampilkan laporan semua order per tanggal.
     */
    public function index()
    {
        // Laporan penjualan per tanggal
        $laporan = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_sales')
        )
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->get();

        return view('admin.orders.index', compact('laporan'));
    }

    /**
     * Menampilkan detail order.  
     * Jika order belum memiliki Snap Token dan status unpaid, akan generate token otomatis.
     */
    public function show(Order $order, MidtransService $midtrans)
    {
        $snapToken = $order->snap_token;

        // Generate token baru jika belum ada dan status pembayaran unpaid
        if (!$snapToken && $order->payment_status === 'unpaid') {
            try {
                $snapToken = $midtrans->createSnapToken($order);

                // Simpan token ke database agar bisa digunakan kembali
                $order->snap_token = $snapToken;
                $order->save();

            } catch (\Exception $e) {
                // Log error supaya tidak crash halaman
                logger()->error('Midtrans Snap Token Error', [
                    'order_id' => $order->id,
                    'error'    => $e->getMessage(),
                ]);
                $snapToken = null; // tetap aman untuk Blade
            }
        }

        // Kirim order dan snapToken ke view
        return view('admin.orders.show', compact('order', 'snapToken'));
    }

    /**
     * Optional: cancel order di Midtrans (jika diperlukan)
     */
    public function cancel(Order $order, MidtransService $midtrans)
    {
        try {
            $response = $midtrans->cancelTransaction($order->order_number);

            $order->payment_status = 'cancelled';
            $order->save();

            return redirect()->back()->with('success', 'Transaksi berhasil dibatalkan di Midtrans.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }
}