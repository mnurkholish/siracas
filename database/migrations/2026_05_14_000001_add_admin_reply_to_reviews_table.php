<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('foto');
            $table->timestamp('admin_replied_at')->nullable()->after('admin_reply');
            $table->foreignId('admin_replied_by')
                ->nullable()
                ->after('admin_replied_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_replied_by');
            $table->dropColumn(['admin_reply', 'admin_replied_at']);
        });
    }
};
