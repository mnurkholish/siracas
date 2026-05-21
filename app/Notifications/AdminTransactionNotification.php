<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminTransactionNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Transaction $transaction,
        private readonly string $action
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'title' => $this->title(),
            'message' => $this->message(),
            'url' => route('admin.transactions.show', $this->transaction),
            'type' => 'admin_transaction_'.$this->action,
        ];
    }

    private function title(): string
    {
        return match ($this->action) {
            'created' => 'Transaksi Baru',
            'paid' => 'Transaksi Siap Diproses',
            'warranty' => 'Pengajuan Garansi',
            default => 'Update Transaksi',
        };
    }

    private function message(): string
    {
        $customer = $this->transaction->user?->username ?? 'Customer';

        return match ($this->action) {
            'created' => "Transaksi #{$this->transaction->id} dari {$customer} baru dibuat dan perlu pengecekan ongkir.",
            'paid' => "Pembayaran transaksi #{$this->transaction->id} dari {$customer} sudah diterima. Transaksi siap diproses.",
            'warranty' => "Transaksi #{$this->transaction->id} dari {$customer} memiliki pengajuan garansi yang perlu ditinjau.",
            default => "Transaksi #{$this->transaction->id} dari {$customer} perlu ditinjau.",
        };
    }
}
