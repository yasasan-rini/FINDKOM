#Requires -Version 5
param(
    [string]$Target = "findkom-app",
    [string]$DbUser = "root",
    [string]$DbPass = "",
    [string]$Mysql  = "mysql"
)
$ErrorActionPreference = "Stop"
$src  = $PSScriptRoot
$dest = Join-Path (Split-Path $src -Parent) $Target

Write-Host "1/7 Membuat skeleton Laravel 12 di $dest" -ForegroundColor Cyan
composer create-project laravel/laravel $dest "^12.0"

Write-Host "2/7 Menyalin berkas starter" -ForegroundColor Cyan
$dirs  = @("app","bootstrap","config","database","docs","public","resources","routes","tests")
$files = @(".env.example","phpunit.xml","README.md")
foreach ($d in $dirs) {
    $from = Join-Path $src $d
    if (Test-Path $from) {
        $to = Join-Path $dest $d
        if (-not (Test-Path $to)) { New-Item -ItemType Directory -Path $to | Out-Null }
        Copy-Item -Path (Join-Path $from "*") -Destination $to -Recurse -Force
    }
}
foreach ($f in $files) {
    $from = Join-Path $src $f
    if (Test-Path $from) { Copy-Item $from (Join-Path $dest $f) -Force }
}

Write-Host "3/7 Menghapus berkas bawaan skeleton yang bentrok" -ForegroundColor Cyan
Get-ChildItem (Join-Path $dest "database\migrations\0001_01_01_*") -ErrorAction SilentlyContinue | Remove-Item -Force
Remove-Item (Join-Path $dest "tests\Feature\ExampleTest.php") -Force -ErrorAction SilentlyContinue
Remove-Item (Join-Path $dest "tests\Unit\ExampleTest.php")    -Force -ErrorAction SilentlyContinue

Set-Location $dest
Write-Host "4/7 Menyiapkan .env" -ForegroundColor Cyan
Copy-Item ".env.example" ".env" -Force
php artisan key:generate --force

Write-Host "5/7 Membuat database (bila MySQL ada di PATH)" -ForegroundColor Cyan
$argPass = if ($DbPass -eq "") { "" } else { "-p$DbPass" }
foreach ($db in @("findkom","findkom_test")) {
    try {
        & $Mysql "-u$DbUser" $argPass "-e" "CREATE DATABASE IF NOT EXISTS ``$db`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>$null
        Write-Host "   database $db siap"
    } catch {
        Write-Warning "   buat database '$db' manual lewat phpMyAdmin, lalu ulangi langkah 6."
    }
}

Write-Host "6/7 Migrasi + seeder + storage link" -ForegroundColor Cyan
php artisan migrate --seed --force
php artisan storage:link
php artisan config:clear

Write-Host "7/7 Selesai. Jalankan: cd $Target ; php artisan serve" -ForegroundColor Green
Write-Host "   Login demo: rina.wulandari@student.ub.ac.id / budi.santoso@ub.ac.id (findkom123)"
Write-Host "   Audit: php artisan findkom:audit   |   Test: php artisan test"