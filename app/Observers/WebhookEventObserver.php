<?php

namespace App\Observers;

use App\Models\WebhookEvent;

class WebhookEventObserver
{
    public function creating(WebhookEvent $webhookEvent): void
    {
        if ($webhookEvent->received_at === null) {
            $webhookEvent->received_at = now();
        }

        if ($webhookEvent->status === null) {
            $webhookEvent->status = 'received';
        }
    }
}
