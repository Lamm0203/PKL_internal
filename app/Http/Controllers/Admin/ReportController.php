<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales()
    {
        // Ambil data laporan, contoh dari tabel orders
        $laporan = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total_orders, SUM(total_amount) as total_sales')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Kirim data ke view
        return view('admin.laporan.index', compact('laporan'));
    }
}

