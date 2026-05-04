<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('address_id')->constrained()->restrictOnDelete();
            $table->timestamp('tanggal');
            $table->text('catatan')->nullable();
            $table->string('status')->default('menunggu_pembayaran');
            $table->timestamps();
        });

        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('harga_saat_transaksi', 12, 2); // Penting: harga snapshot (jaga-jaga ketika harga berubah)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
