<?php

use App\Models\Transaction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('transactions:auto-complete-received', function () {
    $updated = Transaction::query()
        ->where('status', 'diterima')
        ->where('warranty_status', 'tidak_ada')
        ->whereNotNull('received_at')
        ->where('received_at', '<', now()->subDay())
        ->update([
            'status' => 'selesai',
            'completed_at' => now(),
            'updated_at' => now(),
        ]);

    $this->info("{$updated} transaksi otomatis diselesaikan.");
})->purpose('Selesaikan transaksi diterima yang melewati batas garansi 1 hari.');

Schedule::command('transactions:auto-complete-received')->hourly();
