<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('refund_amount', 12, 2)->default(0)->after('ongkir');
            $table->text('refund_note')->nullable()->after('refund_amount');
            $table->timestamp('refunded_at')->nullable()->after('refund_note');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['refund_amount', 'refund_note', 'refunded_at']);
        });
    }
};
