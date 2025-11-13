# ========================================
#  Alabasi System - Final Installation
# ========================================

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "  Alabasi System Installation" -ForegroundColor Cyan
Write-Host "========================================`n" -ForegroundColor Cyan

# 1. Check XAMPP
Write-Host "[1/6] Checking XAMPP..." -ForegroundColor Yellow
$xamppPath = "D:\AAAAAA\xampp"
if (-not (Test-Path $xamppPath)) {
    Write-Host "ERROR: XAMPP not found at $xamppPath" -ForegroundColor Red
    exit 1
}
Write-Host "OK: XAMPP found`n" -ForegroundColor Green

# 2. Check MySQL
Write-Host "[2/6] Checking MySQL..." -ForegroundColor Yellow
$mysqlProcess = Get-Process mysqld -ErrorAction SilentlyContinue
if (-not $mysqlProcess) {
    Write-Host "ERROR: MySQL not running" -ForegroundColor Red
    Write-Host "Start MySQL from XAMPP Control Panel" -ForegroundColor Yellow
    Read-Host "Press Enter after starting MySQL"
}
Write-Host "OK: MySQL running`n" -ForegroundColor Green

# 3. Check Apache
Write-Host "[3/6] Checking Apache..." -ForegroundColor Yellow
$apacheProcess = Get-Process httpd -ErrorAction SilentlyContinue
if (-not $apacheProcess) {
    Write-Host "ERROR: Apache not running" -ForegroundColor Red
    Write-Host "Start Apache from XAMPP Control Panel" -ForegroundColor Yellow
    Read-Host "Press Enter after starting Apache"
}
Write-Host "OK: Apache running`n" -ForegroundColor Green

# 4. Remove old files
Write-Host "[4/6] Removing old files..." -ForegroundColor Yellow
$destPath = "$xamppPath\htdocs\alabasi"
if (Test-Path $destPath) {
    Remove-Item $destPath -Recurse -Force -ErrorAction SilentlyContinue
}
Write-Host "OK: Old files removed`n" -ForegroundColor Green

# 5. Copy files
Write-Host "[5/6] Copying files..." -ForegroundColor Yellow
$sourcePath = $PSScriptRoot

# Create destination directory
New-Item -ItemType Directory -Path $destPath -Force | Out-Null

# Copy all files and folders
Copy-Item -Path "$sourcePath\*" -Destination $destPath -Recurse -Force -Exclude "*.ps1",".git*","*_backup.php"

Write-Host "OK: Files copied to $destPath`n" -ForegroundColor Green

# 6. Create database
Write-Host "[6/6] Creating database..." -ForegroundColor Yellow
$mysqlPath = "$xamppPath\mysql\bin\mysql.exe"

# Drop and create database
& $mysqlPath -u root -e "DROP DATABASE IF EXISTS alabasi_unified; CREATE DATABASE alabasi_unified CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Import database
& $mysqlPath -u root alabasi_unified -e "SOURCE $destPath/database.sql"

Write-Host "OK: Database created`n" -ForegroundColor Green

# Success message
Write-Host "`n========================================" -ForegroundColor Green
Write-Host "  Installation Complete!" -ForegroundColor Green
Write-Host "========================================`n" -ForegroundColor Green

Write-Host "URL: http://localhost/alabasi" -ForegroundColor Cyan
Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Open: http://localhost/alabasi/fix-encoding.php" -ForegroundColor White
Write-Host "2. Then open: http://localhost/alabasi" -ForegroundColor White
Write-Host "3. Delete fix-encoding.php after use`n" -ForegroundColor White

Write-Host "Done!`n" -ForegroundColor Green

# Open browser
Start-Process "http://localhost/alabasi"
