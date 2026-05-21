<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('chats');
    }

    public function down(): void
    {
        // Chat internal sudah tidak dipakai.
    }
};
