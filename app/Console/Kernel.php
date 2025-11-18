<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // مثال: نسخ احتياطي يومي في الساعة 2 صباحاً
        // $schedule->command('backup:run')->daily()->at('02:00');

        // مثال: مسح الذاكرة المؤقتة أسبوعياً
        // $schedule->command('cache:clear')->weekly();

        // مثال: تنظيف Telescope يومياً
        $schedule->command('telescope:prune')->daily();

        // مثال: تحديث الإحصائيات كل ساعة
        $schedule->call(function () {
            \Illuminate\Support\Facades\Cache::forget('admin_stats');
        })->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
