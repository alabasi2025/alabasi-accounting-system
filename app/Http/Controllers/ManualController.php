<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ManualController extends Controller
{
    /**
     * عرض صفحة الدليل
     */
    public function index()
    {
        // قراءة محتوى الدليل من ملف docs/user_manual.md
        $manualPath = base_path('docs/user_manual.md');
        $manualContent = File::exists($manualPath) 
            ? File::get($manualPath) 
            : '# لا يوجد دليل متاح حالياً';
        
        return view('manual.index', compact('manualContent'));
    }

    /**
     * تحديث الدليل (API endpoint للوكيل)
     */
    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $manualPath = base_path('docs/user_manual.md');
        File::put($manualPath, $request->content);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الدليل بنجاح',
        ]);
    }

    /**
     * تصدير الدليل كـ PDF
     */
    public function export()
    {
        // سيتم تطويره لاحقاً
        return response()->json([
            'message' => 'ميزة التصدير قيد التطوير',
        ]);
    }
}
