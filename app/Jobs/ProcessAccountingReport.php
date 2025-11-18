<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process Accounting Report Job
 * 
 * مثال على استخدام Queue System لمعالجة التقارير المحاسبية
 */
class ProcessAccountingReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $unitId,
        public int $companyId,
        public string $reportType,
        public array $parameters = []
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Processing accounting report', [
            'unit_id' => $this->unitId,
            'company_id' => $this->companyId,
            'report_type' => $this->reportType,
        ]);

        // معالجة التقرير هنا
        // يمكن إضافة منطق معالجة التقارير المحاسبية

        Log::info('Accounting report processed successfully');
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to process accounting report', [
            'unit_id' => $this->unitId,
            'company_id' => $this->companyId,
            'error' => $exception->getMessage(),
        ]);
    }
}
