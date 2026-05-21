<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('transactions', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('completed_at');
            }

            if (! Schema::hasColumn('transactions', 'warranty_status')) {
                $table->string('warranty_status')->default('tidak_ada')->after('received_at');
            }

            if (! Schema::hasColumn('transactions', 'warranty_claimed_at')) {
                $table->timestamp('warranty_claimed_at')->nullable()->after('warranty_status');
            }

            if (! Schema::hasColumn('transactions', 'warranty_resolved_at')) {
                $table->timestamp('warranty_resolved_at')->nullable()->after('warranty_claimed_at');
            }

            if (! Schema::hasColumn('transactions', 'refund_amount')) {
                $table->decimal('refund_amount', 12, 2)->default(0)->after('ongkir');
            }

            if (! Schema::hasColumn('transactions', 'refund_note')) {
                $table->text('refund_note')->nullable()->after('refund_amount');
            }

            if (! Schema::hasColumn('transactions', 'refunded_at')) {
                $table->timestamp('refunded_at')->nullable()->after('refund_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('transactions', 'received_at') ? 'received_at' : null,
                Schema::hasColumn('transactions', 'warranty_status') ? 'warranty_status' : null,
                Schema::hasColumn('transactions', 'warranty_claimed_at') ? 'warranty_claimed_at' : null,
                Schema::hasColumn('transactions', 'warranty_resolved_at') ? 'warranty_resolved_at' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
