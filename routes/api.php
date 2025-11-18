<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// جلب المؤسسات التابعة لوحدة معينة
Route::get('/companies-by-unit/{unitId}', function ($unitId) {
    $companies = \App\Models\Company::where('unit_id', $unitId)
        ->where('is_active', true)
        ->select('id', 'company_name')
        ->orderBy('company_name')
        ->get();
    
    return response()->json($companies);
});
