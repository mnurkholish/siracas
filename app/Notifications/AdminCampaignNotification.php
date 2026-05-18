<?php

namespace App\Notifications;

use App\Models\NotificationCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminCampaignNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly NotificationCampaign $campaign)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->campaign->title,
            'message' => $this->campaign->message,
            'url' => filled($this->campaign->url) ? $this->campaign->url : null,
            'type' => $this->campaign->type,
        ];
    }
}
