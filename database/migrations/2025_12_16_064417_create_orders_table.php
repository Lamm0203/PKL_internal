<?php
// database/migrations/xxxx_create_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Relasi ke user
            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // Nomor order unik
            $table->string('order_number', 50)->unique();

            // Total harga order (termasuk ongkir)
            $table->decimal('total_amount', 15, 2);

            // Ongkos kirim
            $table->decimal('shipping_cost', 12, 2)->default(0);

            // Status order
            $table->enum('status', [
                'pending',       // Menunggu pembayaran
                'processing',    // Sedang diproses
                'shipped',       // Sudah dikirim
                'delivered',     // Sudah diterima
                'cancelled'      // Dibatalkan
            ])->default('pending');

            // Informasi pengiriman
            $table->string('shipping_name');
            $table->string('shipping_phone', 20);
            $table->text('shipping_address');

            // Metode pembayaran
            $table->string('payment_method')->nullable();

            // Catatan pembeli
            $table->text('notes')->nullable();

            // Tracking tanggal
            $table->timestamps();

            // Index tambahan untuk mempercepat query laporan
            $table->index(['status', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
