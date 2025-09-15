# KL Gestor Pub - Performance Monitoring Script (PowerShell)
# Monitora recursos dos containers e performance da aplicação

Write-Host "======================================" -ForegroundColor Green
Write-Host "KL Gestor Pub - Performance Monitor" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green
Write-Host ""

Write-Host "=== Container Resource Usage ===" -ForegroundColor Yellow
docker stats klgestorpub_app klgestorpub_nginx klgestorpub_mysql klgestorpub_redis --no-stream
Write-Host ""

Write-Host "=== Container Status ===" -ForegroundColor Yellow
docker-compose ps
Write-Host ""

Write-Host "=== PHP-FPM Status ===" -ForegroundColor Yellow
try {
    docker-compose exec app php-fpm -t 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "PHP-FPM: OK" -ForegroundColor Green
    } else {
        Write-Host "PHP-FPM: ERROR" -ForegroundColor Red
    }
} catch {
    Write-Host "PHP-FPM: ERROR" -ForegroundColor Red
}
Write-Host ""

Write-Host "=== Application Response Time Test ===" -ForegroundColor Yellow
$stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080/" -TimeoutSec 10 -UseBasicParsing
    $stopwatch.Stop()
    Write-Host "HTTP Status: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "Response Time: $($stopwatch.ElapsedMilliseconds)ms" -ForegroundColor Green
} catch {
    $stopwatch.Stop()
    Write-Host "HTTP Status: ERROR" -ForegroundColor Red
    Write-Host "Response Time: $($stopwatch.ElapsedMilliseconds)ms" -ForegroundColor Red
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

Write-Host "=== Memory Usage Summary ===" -ForegroundColor Yellow
$stats = docker stats klgestorpub_app klgestorpub_nginx klgestorpub_mysql klgestorpub_redis --no-stream --format "table {{.Name}}\t{{.MemUsage}}\t{{.CPUPerc}}"
Write-Host $stats
Write-Host ""

Write-Host "=== Recent Logs Check ===" -ForegroundColor Yellow
Write-Host "Checking for recent errors..."
try {
    $appLogs = docker-compose logs app --tail=5 2>$null | Select-String -Pattern "ERROR|CRITICAL|FATAL" | Select-Object -Last 3
    if ($appLogs) {
        Write-Host "Recent App Errors:" -ForegroundColor Red
        $appLogs | ForEach-Object { Write-Host $_ -ForegroundColor Red }
    } else {
        Write-Host "No recent errors found in app logs" -ForegroundColor Green
    }
} catch {
    Write-Host "Could not check app logs" -ForegroundColor Yellow
}
Write-Host ""

Write-Host "======================================" -ForegroundColor Green
Write-Host "Monitoring completed at $(Get-Date)" -ForegroundColor Green
Write-Host "======================================" -ForegroundColor Green