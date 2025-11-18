<?php

namespace App\Listeners;

use App\Events\AccountCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * Send Account Created Notification Listener
 * 
 * مستمع يرسل إشعار عند إنشاء حساب جديد
 */
class SendAccountCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AccountCreated $event): void
    {
        Log::info('New account created', [
            'account_id' => $event->account->id,
            'account_name' => $event->account->name,
        ]);

        // يمكن إضافة منطق إرسال الإشعارات هنا
        // مثال: إرسال بريد إلكتروني أو إشعار داخل النظام
    }

    /**
     * Handle a job failure.
     */
    public function failed(AccountCreated $event, \Throwable $exception): void
    {
        Log::error('Failed to send account created notification', [
            'account_id' => $event->account->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
