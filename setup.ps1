# Alabasi Unified System - Simple Installation Script
# Run this script from the system folder

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Alabasi System Installation" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$xamppPath = "D:\AAAAAA\xampp"
$htdocsPath = "$xamppPath\htdocs\alabasi"
$mysqlPath = "$xamppPath\mysql\bin\mysql.exe"
$currentPath = Get-Location

# Step 1: Check XAMPP
Write-Host "[1/5] Checking XAMPP..." -ForegroundColor Yellow
if (-not (Test-Path $xamppPath)) {
    Write-Host "ERROR: XAMPP not found at $xamppPath" -ForegroundColor Red
    $xamppPath = Read-Host "Enter XAMPP path"
    if (-not (Test-Path $xamppPath)) {
        Write-Host "ERROR: Invalid path" -ForegroundColor Red
        exit 1
    }
}
Write-Host "OK: XAMPP found" -ForegroundColor Green

# Step 2: Check MySQL
Write-Host "[2/5] Checking MySQL..." -ForegroundColor Yellow
$mysql = Get-Process mysqld -ErrorAction SilentlyContinue
if (-not $mysql) {
    Write-Host "ERROR: MySQL not running" -ForegroundColor Red
    Write-Host "Start MySQL from XAMPP Control Panel" -ForegroundColor Yellow
    Read-Host "Press Enter after starting MySQL"
    $mysql = Get-Process mysqld -ErrorAction SilentlyContinue
    if (-not $mysql) {
        Write-Host "ERROR: MySQL still not running" -ForegroundColor Red
        exit 1
    }
}
Write-Host "OK: MySQL running" -ForegroundColor Green

# Step 3: Check Apache
Write-Host "[3/5] Checking Apache..." -ForegroundColor Yellow
$apache = Get-Process httpd -ErrorAction SilentlyContinue
if (-not $apache) {
    Write-Host "ERROR: Apache not running" -ForegroundColor Red
    Write-Host "Start Apache from XAMPP Control Panel" -ForegroundColor Yellow
    Read-Host "Press Enter after starting Apache"
    $apache = Get-Process httpd -ErrorAction SilentlyContinue
    if (-not $apache) {
        Write-Host "ERROR: Apache still not running" -ForegroundColor Red
        exit 1
    }
}
Write-Host "OK: Apache running" -ForegroundColor Green

# Step 4: Copy files
Write-Host "[4/5] Copying files..." -ForegroundColor Yellow
if (Test-Path $htdocsPath) {
    Remove-Item -Path $htdocsPath -Recurse -Force
}
New-Item -ItemType Directory -Path $htdocsPath -Force | Out-Null
Copy-Item -Path "$currentPath\*" -Destination $htdocsPath -Recurse -Exclude "setup.ps1","install.ps1","*.md",".git"
Write-Host "OK: Files copied to $htdocsPath" -ForegroundColor Green

# Step 5: Create database
Write-Host "[5/5] Creating database..." -ForegroundColor Yellow
if (Test-Path $mysqlPath) {
    $sqlFile = "$htdocsPath\database.sql"
    if (Test-Path $sqlFile) {
        & $mysqlPath -u root -e "source $sqlFile" 2>&1 | Out-Null
        Write-Host "OK: Database created" -ForegroundColor Green
    } else {
        Write-Host "WARNING: database.sql not found" -ForegroundColor Yellow
    }
} else {
    Write-Host "WARNING: mysql.exe not found" -ForegroundColor Yellow
    Write-Host "Import database.sql manually from phpMyAdmin" -ForegroundColor Cyan
}

# Done
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  Installation Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "URL: http://localhost/alabasi" -ForegroundColor Cyan
Write-Host "Username: admin" -ForegroundColor Cyan
Write-Host "Password: admin123" -ForegroundColor Cyan
Write-Host ""

# Open browser
Start-Process "http://localhost/alabasi"

Write-Host "Done!" -ForegroundColor Green
Write-Host ""
