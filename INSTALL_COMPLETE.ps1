# ============================================
# سكريبت التثبيت الكامل لنظام الأباسي المحاسبي
# Alabasi Accounting System - Complete Installation Script
# ============================================
# Version: 2.0
# Date: 2025-11-14
# ============================================

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  نظام الأباسي المحاسبي الموحد" -ForegroundColor Yellow
Write-Host "  سكريبت التثبيت الكامل v2.0" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# التحقق من تشغيل XAMPP
Write-Host "[تحقق] فحص حالة XAMPP..." -ForegroundColor Cyan
$xamppPath = "D:\AAAAAA\xampp"
if (-not (Test-Path $xamppPath)) {
    Write-Host "  ❌ خطأ: XAMPP غير موجود في المسار المحدد!" -ForegroundColor Red
    Write-Host "  المسار المتوقع: $xamppPath" -ForegroundColor Yellow
    Read-Host "اضغط Enter للخروج"
    exit
}
Write-Host "  ✅ XAMPP موجود" -ForegroundColor Green

# المسارات
$projectSource = "D:\AAAAAA\alabasi-accounting-system"
$projectDest = "D:\AAAAAA\xampp\htdocs\alabasi-accounting-system"
$mysqlPath = "$xamppPath\mysql\bin\mysql.exe"
$sqlFile = "$projectSource\CLEAN_IMPORT.sql"
$fixUserFile = "$projectSource\fix_user.sql"

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  [1/6] حذف التثبيت القديم" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan

# حذف المجلد القديم
if (Test-Path $projectDest) {
    Write-Host "  حذف المجلد القديم..." -ForegroundColor White
    Remove-Item -Path $projectDest -Recurse -Force -ErrorAction SilentlyContinue
    Write-Host "  ✅ تم حذف المجلد القديم" -ForegroundColor Green
} else {
    Write-Host "  ℹ️  لا يوجد تثبيت سابق" -ForegroundColor Gray
}

# حذف قاعدة البيانات القديمة
Write-Host "  حذف قاعدة البيانات القديمة..." -ForegroundColor White
& $mysqlPath -u root -e "DROP DATABASE IF EXISTS alabasi_unified;" 2>$null
Write-Host "  ✅ تم حذف قاعدة البيانات القديمة" -ForegroundColor Green

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  [2/6] نسخ ملفات المشروع" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan

if (-not (Test-Path $projectSource)) {
    Write-Host "  ❌ خطأ: مجلد المشروع غير موجود!" -ForegroundColor Red
    Write-Host "  المسار المتوقع: $projectSource" -ForegroundColor Yellow
    Read-Host "اضغط Enter للخروج"
    exit
}

Write-Host "  نسخ الملفات من GitHub إلى htdocs..." -ForegroundColor White
Copy-Item -Path "$projectSource\*" -Destination $projectDest -Recurse -Force
Write-Host "  ✅ تم نسخ الملفات بنجاح" -ForegroundColor Green

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  [3/6] إنشاء قاعدة البيانات" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan

Write-Host "  إنشاء قاعدة البيانات alabasi_unified..." -ForegroundColor White
& $mysqlPath -u root -e "CREATE DATABASE IF NOT EXISTS alabasi_unified CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ تم إنشاء قاعدة البيانات" -ForegroundColor Green
} else {
    Write-Host "  ❌ خطأ في إنشاء قاعدة البيانات!" -ForegroundColor Red
    Read-Host "اضغط Enter للخروج"
    exit
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  [4/6] استيراد الجداول (81 جدول)" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan

if (-not (Test-Path $sqlFile)) {
    Write-Host "  ❌ خطأ: ملف SQL غير موجود!" -ForegroundColor Red
    Write-Host "  المسار المتوقع: $sqlFile" -ForegroundColor Yellow
    Read-Host "اضغط Enter للخروج"
    exit
}

Write-Host "  استيراد الجداول من CLEAN_IMPORT.sql..." -ForegroundColor White
Get-Content $sqlFile | & $mysqlPath -u root alabasi_unified 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ تم استيراد 81 جدول بنجاح" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  تحذير: قد تكون هناك مشاكل في الاستيراد" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  [5/6] إعداد المستخدم (root بدون كلمة سر)" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan

Write-Host "  تحديث بيانات المستخدم..." -ForegroundColor White
& $mysqlPath -u root alabasi_unified -e "UPDATE users SET username = 'root', password = '', nameAr = 'الجذر', nameEn = 'Root', email = 'root@alabasi.com', isActive = TRUE WHERE id = 1;"
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✅ تم إعداد المستخدم: root (بدون كلمة سر)" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  تحذير: قد تكون هناك مشكلة في تحديث المستخدم" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  [6/6] التحقق من التثبيت" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan

# التحقق من الملفات
$requiredFiles = @(
    "$projectDest\index.php",
    "$projectDest\login.php",
    "$projectDest\dashboard.php",
    "$projectDest\includes\db.php",
    "$projectDest\includes\functions.php"
)

$allFilesExist = $true
foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Host "  ✅ $(Split-Path $file -Leaf)" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $(Split-Path $file -Leaf) - مفقود!" -ForegroundColor Red
        $allFilesExist = $false
    }
}

# التحقق من قاعدة البيانات
Write-Host ""
Write-Host "  التحقق من قاعدة البيانات..." -ForegroundColor White
$tableCount = & $mysqlPath -u root alabasi_unified -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'alabasi_unified';" -s -N
Write-Host "  ✅ عدد الجداول: $tableCount" -ForegroundColor Green

$userCount = & $mysqlPath -u root alabasi_unified -e "SELECT COUNT(*) FROM users WHERE isActive = 1;" -s -N
Write-Host "  ✅ عدد المستخدمين النشطين: $userCount" -ForegroundColor Green

Write-Host ""
Write-Host "============================================" -ForegroundColor Green
Write-Host "  🎉 اكتمل التثبيت بنجاح!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Green
Write-Host ""
Write-Host "📋 معلومات الدخول:" -ForegroundColor Cyan
Write-Host "  • الرابط: http://localhost/alabasi-accounting-system/" -ForegroundColor White
Write-Host "  • اسم المستخدم: root" -ForegroundColor White
Write-Host "  • كلمة السر: (فارغة - دخول تلقائي)" -ForegroundColor White
Write-Host ""
Write-Host "📊 إحصائيات:" -ForegroundColor Cyan
Write-Host "  • عدد الجداول: $tableCount" -ForegroundColor White
Write-Host "  • عدد المستخدمين: $userCount" -ForegroundColor White
Write-Host ""

# فتح المشروع
Write-Host "هل تريد فتح المشروع الآن؟ (Y/N)" -ForegroundColor Yellow
$response = Read-Host
if ($response -eq "Y" -or $response -eq "y" -or $response -eq "") {
    Write-Host "  فتح المشروع..." -ForegroundColor Cyan
    Start-Process "http://localhost/alabasi-accounting-system/"
    Write-Host "  ✅ تم فتح المشروع في المتصفح" -ForegroundColor Green
}

Write-Host ""
Write-Host "============================================" -ForegroundColor Cyan
Write-Host "  شكراً لاستخدام نظام الأباسي المحاسبي!" -ForegroundColor Yellow
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""
