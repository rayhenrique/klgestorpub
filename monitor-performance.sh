#!/bin/bash

# KL Gestor Pub - Performance Monitoring Script
# Monitora recursos dos containers e performance da aplicação

echo "======================================"
echo "KL Gestor Pub - Performance Monitor"
echo "======================================"
echo ""

echo "=== Container Resource Usage ==="
docker stats klgestorpub_app klgestorpub_nginx klgestorpub_mysql klgestorpub_redis --no-stream
echo ""

echo "=== Container Status ==="
docker-compose ps
echo ""

echo "=== PHP-FPM Status ==="
docker-compose exec app php-fpm -t 2>/dev/null && echo "PHP-FPM: OK" || echo "PHP-FPM: ERROR"
echo ""

echo "=== Nginx Access Log (Last 5 entries) ==="
docker-compose logs nginx --tail=5 2>/dev/null | grep -v "warning" | tail -5
echo ""

echo "=== Laravel Log (Last 3 entries) ==="
docker-compose exec app tail -3 storage/logs/laravel.log 2>/dev/null
echo ""

echo "=== Database Connection Test ==="
docker-compose exec app php artisan tinker --execute="echo 'DB Connection: ' . (DB::connection()->getPdo() ? 'OK' : 'FAILED');" 2>/dev/null
echo ""

echo "=== Redis Connection Test ==="
docker-compose exec redis redis-cli ping 2>/dev/null && echo "Redis: PONG" || echo "Redis: ERROR"
echo ""

echo "=== Application Response Time Test ==="
start_time=$(date +%s%N)
curl -s -o /dev/null -w "HTTP Status: %{http_code}\nResponse Time: %{time_total}s\n" http://localhost:8080/ 2>/dev/null
echo ""

echo "=== Memory Usage Summary ==="
echo "App Container: $(docker stats klgestorpub_app --no-stream --format 'table {{.MemUsage}}' | tail -1)"
echo "MySQL Container: $(docker stats klgestorpub_mysql --no-stream --format 'table {{.MemUsage}}' | tail -1)"
echo "Nginx Container: $(docker stats klgestorpub_nginx --no-stream --format 'table {{.MemUsage}}' | tail -1)"
echo "Redis Container: $(docker stats klgestorpub_redis --no-stream --format 'table {{.MemUsage}}' | tail -1)"
echo ""

echo "======================================"
echo "Monitoring completed at $(date)"
echo "======================================"