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
        // Region tables (Provinsi, Kota, Kecamatan)


        Schema::create('provinsis', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provinsi_id')->constrained('provinsis')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kota_id')->constrained('kotas')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('kecamatan_id')
                ->constrained('kecamatans')
                ->restrictOnDelete();

            $table->text('detail_alamat');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
