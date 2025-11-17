<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuideController extends Controller
{
    /**
     * عرض صفحة الدليل
     */
    public function index()
    {
        $guidePath = base_path('SYSTEM_GUIDE.md');
        $changelogPath = base_path('CHANGELOG.md');
        
        $guideContent = \File::exists($guidePath) ? \File::get($guidePath) : 'الدليل غير متوفر حالياً.';
        $changelogContent = \File::exists($changelogPath) ? \File::get($changelogPath) : 'سجل التحديثات غير متوفر حالياً.';
        
        return view('guide.index', compact('guideContent', 'changelogContent'));
    }

    /**
     * تحميل دليل النظام بصيغة PDF
     */
    public function downloadGuidePdf()
    {
        $pdfPath = base_path('SYSTEM_GUIDE.pdf');
        
        if (!\File::exists($pdfPath)) {
            return back()->with('error', 'ملف PDF غير متوفر حالياً.');
        }
        
        return \Response::download($pdfPath, 'دليل_نظام_الأباسي_المحاسبي.pdf');
    }

    /**
     * تحميل سجل التحديثات بصيغة PDF
     */
    public function downloadChangelogPdf()
    {
        $pdfPath = base_path('CHANGELOG.pdf');
        
        if (!\File::exists($pdfPath)) {
            return back()->with('error', 'ملف PDF غير متوفر حالياً.');
        }
        
        return \Response::download($pdfPath, 'سجل_تحديثات_نظام_الأباسي.pdf');
    }
}
