# =====================================================
# تحديث سريع من GitHub
# =====================================================

# المسار المحلي (عدّله حسب مسارك)
$xamppPath = "D:\AAAAAA\alabasi-xampp-system"

Write-Host "🔄 تحديث سريع من GitHub..." -ForegroundColor Cyan
Write-Host ""

# الانتقال إلى المجلد
Set-Location $xamppPath

# سحب التحديثات
Write-Host "📥 سحب التحديثات..." -ForegroundColor Green
git pull origin master

Write-Host ""
Write-Host "✅ تم التحديث بنجاح!" -ForegroundColor Green
Write-Host ""

# عرض آخر commit
Write-Host "📝 آخر تحديث:" -ForegroundColor Cyan
git log -1 --pretty=format:"%h - %s (%cr)"
Write-Host ""
Write-Host ""

Read-Host "اضغط Enter للخروج"
