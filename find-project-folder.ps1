# =====================================================
# البحث عن مجلد المشروع
# =====================================================

Write-Host "🔍 البحث عن مجلد المشروع في D:\AAAAAA\" -ForegroundColor Cyan
Write-Host ""

# البحث عن المجلدات التي تحتوي على ملف README.md أو includes/db.php
$searchPath = "D:\AAAAAA"

Write-Host "📂 المجلدات الموجودة في D:\AAAAAA\" -ForegroundColor Green
Write-Host ""

# عرض جميع المجلدات
Get-ChildItem -Path $searchPath -Directory | ForEach-Object {
    $folderName = $_.Name
    $folderPath = $_.FullName
    
    # التحقق من وجود ملفات المشروع
    $hasReadme = Test-Path "$folderPath\README.md"
    $hasDb = Test-Path "$folderPath\includes\db.php"
    $hasGit = Test-Path "$folderPath\.git"
    
    if ($hasReadme -or $hasDb -or $hasGit) {
        Write-Host "✅ $folderName" -ForegroundColor Green
        Write-Host "   المسار: $folderPath" -ForegroundColor Gray
        
        if ($hasGit) {
            Write-Host "   🔗 Git: نعم" -ForegroundColor Cyan
            
            # عرض remote URL
            Push-Location $folderPath
            $remoteUrl = git remote get-url origin 2>$null
            if ($remoteUrl) {
                Write-Host "   📡 Remote: $remoteUrl" -ForegroundColor Cyan
            }
            Pop-Location
        }
        
        if ($hasReadme) { Write-Host "   📄 README.md: نعم" -ForegroundColor Gray }
        if ($hasDb) { Write-Host "   🗄️  includes/db.php: نعم" -ForegroundColor Gray }
        
        Write-Host ""
    } else {
        Write-Host "❌ $folderName" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host "   ℹ️  ابحث عن المجلد الذي يحتوي على علامة ✅" -ForegroundColor Yellow
Write-Host "=====================================================" -ForegroundColor Cyan
Write-Host ""

Read-Host "اضغط Enter للخروج"
