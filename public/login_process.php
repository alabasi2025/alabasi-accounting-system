<?php
session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Main\Unit;
use App\Models\Main\Company;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit_id = $_POST['unit_id'] ?? null;
    $company_id = $_POST['company_id'] ?? null;
    
    if (!$unit_id) {
        header('Location: login.php?error=no_unit');
        exit;
    }
    
    // حفظ بيانات الجلسة
    $_SESSION['unit_id'] = $unit_id;
    
    if ($unit_id === 'main') {
        // القاعدة المركزية
        $_SESSION['unit_name'] = 'القاعدة المركزية';
        $_SESSION['database'] = 'main';
        $_SESSION['is_main'] = true;
    } else {
        // وحدة عمل
        if (!$company_id) {
            header('Location: login.php?error=no_company');
            exit;
        }
        
        $unit = Unit::find($unit_id);
        $company = Company::find($company_id);
        
        if (!$unit || !$company) {
            header('Location: login.php?error=invalid');
            exit;
        }
        
        $_SESSION['unit_name'] = $unit->name;
        $_SESSION['company_id'] = $company_id;
        $_SESSION['company_name'] = $company->name;
        $_SESSION['database'] = $unit->database_name;
        $_SESSION['is_main'] = false;
    }
    
    header('Location: dashboard.php');
    exit;
}

header('Location: login.php');
exit;
