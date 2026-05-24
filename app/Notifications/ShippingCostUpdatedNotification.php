<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ShippingCostUpdatedNotification extends Notification
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
        $ongkir = number_format((float) $this->transaction->ongkir, 0, ',', '.');

        return [
            'title' => 'Ongkir Sudah Ditetapkan',
            'message' => 'Ongkir untuk pesanan #'.$this->transaction->id.' sudah diisi sebesar Rp'.$ongkir.'. Silakan lanjutkan pembayaran.',
            'url' => route('transactions.show', $this->transaction),
            'type' => 'shipping_cost_updated',
        ];
    }
}
