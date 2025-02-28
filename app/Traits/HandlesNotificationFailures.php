<?php

namespace App\Traits;

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Log;

trait HandlesNotificationFailures
{
    public function failed(NotificationFailed $event)
    {
        Log::error('Notification failed', [
            'notification' => get_class($event->notification),
            'notifiable' => get_class($event->notifiable),
            'error' => $event->exception->getMessage(),
        ]);
    }
} 