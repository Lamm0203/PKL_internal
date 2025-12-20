<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product; // jika ada model Product

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all(); // ambil semua produk
        return view('admin.products.index', compact('products'));
    }
}
