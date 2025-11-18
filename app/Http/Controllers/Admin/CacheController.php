<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    /**
     * Display cache management page
     */
    public function index()
    {
        return view('admin.cache.index');
    }

    /**
     * Clear all cache
     */
    public function clear()
    {
        try {
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم مسح جميع أنواع الذاكرة المؤقتة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear config cache
     */
    public function clearConfig()
    {
        try {
            Artisan::call('config:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم مسح Config Cache بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear route cache
     */
    public function clearRoute()
    {
        try {
            Artisan::call('route:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم مسح Route Cache بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear view cache
     */
    public function clearView()
    {
        try {
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'تم مسح View Cache بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize application
     */
    public function optimize()
    {
        try {
            Artisan::call('optimize');

            return response()->json([
                'success' => true,
                'message' => 'تم تحسين التطبيق بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}
