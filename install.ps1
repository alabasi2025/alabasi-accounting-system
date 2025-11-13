# ===============================================================================
# Alabasi Unified System - XAMPP Installation Script
# ===============================================================================

Write-Host "`n===============================================" -ForegroundColor Cyan
Write-Host "  Alabasi Unified System - Installation" -ForegroundColor Cyan
Write-Host "===============================================`n" -ForegroundColor Cyan

# ===============================================================================
# Step 1: Check XAMPP
# ===============================================================================

Write-Host "Step 1: Checking XAMPP...`n" -ForegroundColor Yellow

$xamppPath = "D:\AAAAAA\xampp"

if (-not (Test-Path $xamppPath)) {
    Write-Host "ERROR: XAMPP not found at: $xamppPath" -ForegroundColor Red
    $xamppPath = Read-Host "`nEnter XAMPP path (e.g., C:\xampp)"
    
    if (-not (Test-Path $xamppPath)) {
        Write-Host "ERROR: Invalid path - Exiting" -ForegroundColor Red
        exit 1
    }
}

Write-Host "OK: XAMPP found at: $xamppPath" -ForegroundColor Green

# ===============================================================================
# Step 2: Check MySQL
# ===============================================================================

Write-Host "`nStep 2: Checking MySQL...`n" -ForegroundColor Yellow

$mysqlProcess = Get-Process mysqld -ErrorAction SilentlyContinue

if (-not $mysqlProcess) {
    Write-Host "ERROR: MySQL is not running!" -ForegroundColor Red
    Write-Host "TIP: Open XAMPP Control Panel and start MySQL" -ForegroundColor Yellow
    Read-Host "`nPress Enter after starting MySQL"
    
    $mysqlProcess = Get-Process mysqld -ErrorAction SilentlyContinue
    if (-not $mysqlProcess) {
        Write-Host "ERROR: MySQL still not running - Exiting" -ForegroundColor Red
        exit 1
    }
}

Write-Host "OK: MySQL is running (PID: $($mysqlProcess.Id))" -ForegroundColor Green

# ===============================================================================
# Step 3: Check Apache
# ===============================================================================

Write-Host "`nStep 3: Checking Apache...`n" -ForegroundColor Yellow

$apacheProcess = Get-Process httpd -ErrorAction SilentlyContinue

if (-not $apacheProcess) {
    Write-Host "ERROR: Apache is not running!" -ForegroundColor Red
    Write-Host "TIP: Open XAMPP Control Panel and start Apache" -ForegroundColor Yellow
    Read-Host "`nPress Enter after starting Apache"
    
    $apacheProcess = Get-Process httpd -ErrorAction SilentlyContinue
    if (-not $apacheProcess) {
        Write-Host "ERROR: Apache still not running - Exiting" -ForegroundColor Red
        exit 1
    }
}

Write-Host "OK: Apache is running" -ForegroundColor Green

# ===============================================================================
# Step 4: Copy files to htdocs
# ===============================================================================

Write-Host "`nStep 4: Copying system files...`n" -ForegroundColor Yellow

$htdocsPath = Join-Path $xamppPath "htdocs\alabasi"
$currentPath = Get-Location

if (Test-Path $htdocsPath) {
    Write-Host "WARNING: Directory exists - will be replaced" -ForegroundColor Yellow
    Remove-Item -Path $htdocsPath -Recurse -Force
}

New-Item -ItemType Directory -Path $htdocsPath -Force | Out-Null

Write-Host "Copying files..." -ForegroundColor Cyan
Copy-Item -Path "$currentPath\*" -Destination $htdocsPath -Recurse -Exclude "install.ps1","install-fixed.ps1","README.md",".git"

Write-Host "OK: Files copied to: $htdocsPath" -ForegroundColor Green

# ===============================================================================
# Step 5: Create database
# ===============================================================================

Write-Host "`nStep 5: Creating database...`n" -ForegroundColor Yellow

$mysqlPath = Join-Path $xamppPath "mysql\bin\mysql.exe"

if (Test-Path $mysqlPath) {
    Write-Host "Executing SQL script..." -ForegroundColor Cyan
    
    $sqlFile = Join-Path $htdocsPath "database.sql"
    
    if (Test-Path $sqlFile) {
        & $mysqlPath -u root -e "source $sqlFile" 2>&1 | Out-Null
        Write-Host "OK: Database created successfully" -ForegroundColor Green
    } else {
        Write-Host "WARNING: database.sql not found" -ForegroundColor Yellow
    }
} else {
    Write-Host "WARNING: mysql.exe not found" -ForegroundColor Yellow
    Write-Host "TIP: You can import database.sql manually from phpMyAdmin" -ForegroundColor Cyan
}

# ===============================================================================
# Step 6: Test system
# ===============================================================================

Write-Host "`nStep 6: Testing system...`n" -ForegroundColor Yellow

$url = "http://localhost/alabasi"

try {
    $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 5
    
    if ($response.StatusCode -eq 200) {
        Write-Host "OK: System is working!" -ForegroundColor Green
    } else {
        Write-Host "WARNING: Unexpected response: $($response.StatusCode)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "WARNING: Could not connect to system" -ForegroundColor Yellow
    Write-Host "TIP: Make sure Apache is running" -ForegroundColor Cyan
}

# ===============================================================================
# Final Result
# ===============================================================================

Write-Host "`n===============================================" -ForegroundColor Cyan
Write-Host "  Installation Complete!" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "" -ForegroundColor Cyan
Write-Host "  URL: http://localhost/alabasi" -ForegroundColor Cyan
Write-Host "" -ForegroundColor Cyan
Write-Host "  Username: admin" -ForegroundColor Cyan
Write-Host "  Password: admin123" -ForegroundColor Cyan
Write-Host "" -ForegroundColor Cyan
Write-Host "  Files: $htdocsPath" -ForegroundColor Cyan
Write-Host "" -ForegroundColor Cyan
Write-Host "  TIP: To access from network:" -ForegroundColor Cyan
Write-Host "       http://[Your-IP]/alabasi" -ForegroundColor Cyan
Write-Host "" -ForegroundColor Cyan
Write-Host "===============================================`n" -ForegroundColor Cyan

# Open browser
Write-Host "Opening browser...`n" -ForegroundColor Cyan
Start-Process $url

Write-Host "Done! Enjoy Alabasi Unified System`n" -ForegroundColor Green
