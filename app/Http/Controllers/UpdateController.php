<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class UpdateController extends Controller
{
    /**
     * عرض صفحة التحديثات
     */
    public function index()
    {
        // قراءة محتوى التحديثات من ملف docs/changelog.md
        $changelogPath = base_path('docs/changelog.md');
        $changelogContent = File::exists($changelogPath) 
            ? File::get($changelogPath) 
            : '# لا توجد تحديثات متاحة حالياً';
        
        return view('updates.index', compact('changelogContent'));
    }

    /**
     * مزامنة التحديثات مع الوكيل (API endpoint)
     */
    public function sync(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $changelogPath = base_path('docs/changelog.md');
        File::put($changelogPath, $request->content);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث سجل التحديثات بنجاح',
        ]);
    }
}
