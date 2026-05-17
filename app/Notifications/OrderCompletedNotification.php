<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCompletedNotification extends Notification
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
            'title' => 'Pesanan Selesai',
            'message' => 'Pesanan #'.$this->transaction->id.' sudah selesai. Terima kasih sudah berbelanja.',
            'url' => route('transactions.show', $this->transaction),
            'type' => 'order_completed',
        ];
    }
}
