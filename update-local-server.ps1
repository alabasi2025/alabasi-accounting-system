# =====================================================
# سكريبت تحديث السيرفر المحلي من GitHub
# نظام العباسي المحاسبي الموحد
# =====================================================
# الاستخدام: قم بتشغيل هذا السكريبت في PowerShell
# =====================================================

Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "   تحديث السيرفر المحلي من GitHub" -ForegroundColor Yellow
Write-Host "   نظام العباسي المحاسبي الموحد" -ForegroundColor Yellow
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

# =====================================================
# 1. تحديد المسار المحلي لـ XAMPP
# =====================================================
$xamppPath = "C:\xampp\htdocs\alabasi-xampp-system"

Write-Host "[1/6] التحقق من المسار المحلي..." -ForegroundColor Green
if (-Not (Test-Path $xamppPath)) {
    Write-Host "❌ خطأ: المسار غير موجود: $xamppPath" -ForegroundColor Red
    Write-Host "الرجاء تحديث المسار في السكريبت" -ForegroundColor Yellow
    Read-Host "اضغط Enter للخروج"
    exit
}

Write-Host "✅ المسار موجود: $xamppPath" -ForegroundColor Green
Write-Host ""

# =====================================================
# 2. الانتقال إلى المجلد
# =====================================================
Write-Host "[2/6] الانتقال إلى المجلد..." -ForegroundColor Green
Set-Location $xamppPath
Write-Host "✅ تم الانتقال إلى: $(Get-Location)" -ForegroundColor Green
Write-Host ""

# =====================================================
# 3. التحقق من حالة Git
# =====================================================
Write-Host "[3/6] التحقق من حالة Git..." -ForegroundColor Green
$gitStatus = git status --porcelain
if ($gitStatus) {
    Write-Host "⚠️  تحذير: هناك تغييرات محلية غير محفوظة:" -ForegroundColor Yellow
    Write-Host $gitStatus -ForegroundColor Gray
    Write-Host ""
    $response = Read-Host "هل تريد حفظ التغييرات المحلية قبل التحديث؟ (y/n)"
    
    if ($response -eq "y" -or $response -eq "Y") {
        Write-Host "💾 حفظ التغييرات المحلية..." -ForegroundColor Cyan
        $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
        git add .
        git commit -m "Backup: نسخة احتياطية قبل التحديث - $timestamp"
        Write-Host "✅ تم حفظ التغييرات" -ForegroundColor Green
    } else {
        Write-Host "⚠️  سيتم تجاهل التغييرات المحلية" -ForegroundColor Yellow
        git reset --hard HEAD
        Write-Host "✅ تم إعادة تعيين التغييرات" -ForegroundColor Green
    }
} else {
    Write-Host "✅ لا توجد تغييرات محلية" -ForegroundColor Green
}
Write-Host ""

# =====================================================
# 4. سحب التحديثات من GitHub
# =====================================================
Write-Host "[4/6] سحب التحديثات من GitHub..." -ForegroundColor Green
Write-Host "Repository: alabasi2025/alabasi-accounting-system" -ForegroundColor Cyan
Write-Host "Branch: master" -ForegroundColor Cyan
Write-Host ""

try {
    # جلب التحديثات
    Write-Host "📥 جلب التحديثات..." -ForegroundColor Cyan
    git fetch origin master
    
    # عرض الفرق بين النسخة المحلية والبعيدة
    Write-Host ""
    Write-Host "📊 التحديثات المتاحة:" -ForegroundColor Cyan
    $commitsDiff = git log HEAD..origin/master --oneline
    if ($commitsDiff) {
        Write-Host $commitsDiff -ForegroundColor Gray
    } else {
        Write-Host "✅ النسخة المحلية محدثة بالفعل" -ForegroundColor Green
        Write-Host ""
        Read-Host "اضغط Enter للخروج"
        exit
    }
    
    Write-Host ""
    Write-Host "🔄 دمج التحديثات..." -ForegroundColor Cyan
    git pull origin master --no-edit
    
    Write-Host "✅ تم سحب التحديثات بنجاح" -ForegroundColor Green
} catch {
    Write-Host "❌ خطأ في سحب التحديثات:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Read-Host "اضغط Enter للخروج"
    exit
}
Write-Host ""

# =====================================================
# 5. عرض ملخص التحديثات
# =====================================================
Write-Host "[5/6] ملخص التحديثات:" -ForegroundColor Green
Write-Host ""

# عرض آخر commit
Write-Host "📝 آخر Commit:" -ForegroundColor Cyan
$lastCommit = git log -1 --pretty=format:"%h - %s (%cr) <%an>"
Write-Host $lastCommit -ForegroundColor Gray
Write-Host ""

# عرض الملفات المتغيرة
Write-Host "📂 الملفات المتغيرة:" -ForegroundColor Cyan
$changedFiles = git diff --name-status HEAD@{1} HEAD
if ($changedFiles) {
    Write-Host $changedFiles -ForegroundColor Gray
} else {
    Write-Host "لا توجد تغييرات في الملفات" -ForegroundColor Gray
}
Write-Host ""

# عرض إحصائيات
Write-Host "📊 الإحصائيات:" -ForegroundColor Cyan
$stats = git diff --stat HEAD@{1} HEAD
if ($stats) {
    Write-Host $stats -ForegroundColor Gray
}
Write-Host ""

# =====================================================
# 6. التحقق من الملفات الجديدة المهمة
# =====================================================
Write-Host "[6/6] التحقق من الملفات الجديدة المهمة..." -ForegroundColor Green
Write-Host ""

$importantFiles = @(
    "sql/pending_vouchers_table.sql",
    "sql/intermediate_account_transactions.sql",
    "PENDING_VOUCHERS_TABLE_DESIGN_v2.md",
    "TRANSFER_TYPES_GUIDE.md",
    "SMART_INTERMEDIATE_ACCOUNT_DESIGN.md"
)

$foundFiles = @()
foreach ($file in $importantFiles) {
    if (Test-Path $file) {
        $foundFiles += $file
    }
}

if ($foundFiles.Count -gt 0) {
    Write-Host "✅ تم العثور على ملفات مهمة جديدة:" -ForegroundColor Green
    foreach ($file in $foundFiles) {
        Write-Host "   - $file" -ForegroundColor Cyan
    }
    Write-Host ""
    Write-Host "⚠️  تذكير: قد تحتاج إلى تنفيذ ملفات SQL على قاعدة البيانات!" -ForegroundColor Yellow
} else {
    Write-Host "ℹ️  لا توجد ملفات SQL جديدة" -ForegroundColor Gray
}
Write-Host ""

# =====================================================
# 7. خيارات إضافية
# =====================================================
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "   خيارات إضافية" -ForegroundColor Yellow
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. فتح المجلد في File Explorer" -ForegroundColor White
Write-Host "2. فتح phpMyAdmin لتنفيذ ملفات SQL" -ForegroundColor White
Write-Host "3. عرض سجل التحديثات (Git Log)" -ForegroundColor White
Write-Host "4. الخروج" -ForegroundColor White
Write-Host ""

$choice = Read-Host "اختر رقم (1-4)"

switch ($choice) {
    "1" {
        Write-Host "📂 فتح المجلد..." -ForegroundColor Cyan
        explorer.exe $xamppPath
    }
    "2" {
        Write-Host "🌐 فتح phpMyAdmin..." -ForegroundColor Cyan
        Start-Process "http://localhost/phpmyadmin"
    }
    "3" {
        Write-Host ""
        Write-Host "📜 آخر 10 تحديثات:" -ForegroundColor Cyan
        git log -10 --pretty=format:"%C(yellow)%h%Creset - %C(green)%s%Creset (%C(cyan)%cr%Creset) <%C(blue)%an%Creset>" --abbrev-commit
        Write-Host ""
        Write-Host ""
        Read-Host "اضغط Enter للمتابعة"
    }
    "4" {
        Write-Host "👋 إلى اللقاء!" -ForegroundColor Green
    }
    default {
        Write-Host "❌ خيار غير صحيح" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "   ✅ تم التحديث بنجاح!" -ForegroundColor Green
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

# الانتظار قبل الإغلاق
Read-Host "اضغط Enter للخروج"
