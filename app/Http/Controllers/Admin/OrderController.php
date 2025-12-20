<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        // Ambil semua data orders
        $laporan = Order::all(); // ini variabel $laporan

        // Kirim ke view
        return view('admin.orders.index', compact('laporan'));
    }
}
