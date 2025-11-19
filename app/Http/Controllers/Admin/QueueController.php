<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class QueueController extends Controller
{
    /**
     * Display queue management page
     */
    public function index()
    {
        return view('admin.queues.index');
    }

    /**
     * Get queue statistics
     */
    public function stats()
    {
        try {
            $stats = [
                'pending' => 0,
                'processing' => 0,
                'completed' => 0,
                'failed' => DB::table('failed_jobs')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry failed jobs
     */
    public function retryFailed()
    {
        try {
            Artisan::call('queue:retry', ['id' => 'all']);

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة محاولة جميع المهام الفاشلة'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear failed jobs
     */
    public function clearFailed()
    {
        try {
            Artisan::call('queue:flush');

            return response()->json([
                'success' => true,
                'message' => 'تم حذف جميع المهام الفاشلة'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get failed jobs list
     */
    public function failedJobs()
    {
        try {
            $failedJobs = DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'jobs' => $failedJobs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}
