<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Transaction $transaction)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pembayaran Berhasil',
            'message' => 'Pembayaran untuk pesanan #'.$this->transaction->id.' berhasil diterima.',
            'url' => route('transactions.show', $this->transaction),
            'type' => 'order_paid',
        ];
    }
}
