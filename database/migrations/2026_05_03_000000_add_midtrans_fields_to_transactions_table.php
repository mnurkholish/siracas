<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('order_id')->nullable()->unique()->after('status');
            $table->string('snap_token')->nullable()->after('order_id');
            $table->string('payment_type')->nullable()->after('snap_token');
            $table->timestamp('paid_at')->nullable()->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
            $table->dropColumn([
                'order_id',
                'snap_token',
                'payment_type',
                'paid_at',
            ]);
        });
    }
};
