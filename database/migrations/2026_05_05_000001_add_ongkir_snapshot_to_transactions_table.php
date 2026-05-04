<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('city')->nullable()->after('address_id');
            $table->string('province')->nullable()->after('city');
            $table->decimal('ongkir', 12, 2)->nullable()->after('province');
            $table->decimal('total_barang', 12, 2)->default(0)->after('ongkir');
            $table->decimal('total_bayar', 12, 2)->nullable()->after('total_barang');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'province',
                'ongkir',
                'total_barang',
                'total_bayar',
            ]);
        });
    }
};
