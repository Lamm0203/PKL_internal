<?php
// app/Observers/ProductObserver.php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        // Clear cache produk featured
        Cache::forget('featured_products');
        Cache::forget('category_' . $product->category_id . '_products');

        // Log activity hanya jika ada user login
        if (function_exists('activity') && auth()->check()) {
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->log('Produk baru dibuat: ' . $product->name);
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        Cache::forget('product_' . $product->id);
        Cache::forget('featured_products');

        if ($product->isDirty('category_id')) {
            Cache::forget('category_' . $product->getOriginal('category_id') . '_products');
            Cache::forget('category_' . $product->category_id . '_products');
        }

        if (function_exists('activity') && auth()->check()) {
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->log('Produk diperbarui: ' . $product->name);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        Cache::forget('product_' . $product->id);
        Cache::forget('featured_products');
        Cache::forget('category_' . $product->category_id . '_products');

        if (function_exists('activity') && auth()->check()) {
            activity()
                ->performedOn($product)
                ->causedBy(auth()->user())
                ->log('Produk dihapus: ' . $product->name);
        }
    }
}
