#!/bin/bash

# 502 Bad Gateway Troubleshooting Script untuk Permission Edit
# Jalankan script ini di server Ubuntu untuk diagnosis dan perbaikan

echo "=== 502 BAD GATEWAY TROUBLESHOOTING ==="
echo "Date: $(date)"
echo "Server: $(hostname)"
echo ""

# Function untuk print dengan warna
print_status() {
    case $1 in
        "OK") echo -e "✅ $2" ;;
        "ERROR") echo -e "❌ $2" ;;
        "WARNING") echo -e "⚠️  $2" ;;
        "INFO") echo -e "ℹ️  $2" ;;
    esac
}

# 1. Cek status service
echo "🔍 CHECKING SERVICES STATUS:"
services=("nginx" "php8.1-fpm" "php8.2-fpm" "php-fpm")
for service in "${services[@]}"; do
    if systemctl is-active --quiet "$service" 2>/dev/null; then
        print_status "OK" "$service is running"
    else
        if systemctl list-unit-files --type=service | grep -q "$service"; then
            print_status "ERROR" "$service is installed but not running"
            echo "  💡 Try: sudo systemctl start $service"
        fi
    fi
done
echo ""

# 2. Cek log error terbaru
echo "📋 RECENT ERROR LOGS (last 20 lines):"
echo "--- Nginx Error Log ---"
if [ -f /var/log/nginx/error.log ]; then
    tail -20 /var/log/nginx/error.log | grep -E "(502|Bad Gateway|upstream|php-fpm)" || echo "No recent 502 errors in nginx log"
else
    print_status "WARNING" "Nginx error log not found at /var/log/nginx/error.log"
fi

echo ""
echo "--- PHP-FPM Error Log ---"
php_fpm_logs=("/var/log/php8.1-fpm.log" "/var/log/php8.2-fpm.log" "/var/log/php-fpm.log")
for log in "${php_fpm_logs[@]}"; do
    if [ -f "$log" ]; then
        echo "Checking $log:"
        tail -10 "$log" | grep -E "(NOTICE|WARNING|ERROR|CRITICAL)" | tail -5 || echo "No recent errors"
        break
    fi
done
echo ""

# 3. Cek konfigurasi PHP
echo "⚙️  PHP CONFIGURATION CHECK:"
php_version=$(php -v | head -1 | cut -d' ' -f2 | cut -d'.' -f1,2)
print_status "INFO" "PHP Version: $php_version"

# Check memory limit
memory_limit=$(php -r "echo ini_get('memory_limit');")
print_status "INFO" "Memory Limit: $memory_limit"

# Check max execution time
max_exec_time=$(php -r "echo ini_get('max_execution_time');")
print_status "INFO" "Max Execution Time: ${max_exec_time}s"

# Check max input vars
max_input_vars=$(php -r "echo ini_get('max_input_vars');")
print_status "INFO" "Max Input Vars: $max_input_vars"

# Recommendations
if [[ $memory_limit =~ ^[0-9]+M$ ]]; then
    memory_mb=${memory_limit%M}
    if [ "$memory_mb" -lt 512 ]; then
        print_status "WARNING" "Memory limit is low ($memory_limit). Recommend 512M or higher for complex permissions"
    fi
fi

if [ "$max_exec_time" -lt 60 ]; then
    print_status "WARNING" "Max execution time is low (${max_exec_time}s). Recommend 60s or higher"
fi

if [ "$max_input_vars" -lt 3000 ]; then
    print_status "WARNING" "Max input vars is low ($max_input_vars). Recommend 3000+ for large permission matrices"
fi
echo ""

# 4. Cek disk space
echo "💾 DISK SPACE CHECK:"
disk_usage=$(df -h / | awk 'NR==2{print $5}' | sed 's/%//')
if [ "$disk_usage" -gt 90 ]; then
    print_status "ERROR" "Disk space critical: ${disk_usage}% used"
    echo "  💡 Clean up logs: sudo find /var/log -name '*.log' -type f -size +100M"
else
    print_status "OK" "Disk space OK: ${disk_usage}% used"
fi
echo ""

# 5. Cek proses PHP-FPM
echo "🔄 PHP-FPM PROCESS CHECK:"
php_processes=$(pgrep php-fpm | wc -l)
if [ "$php_processes" -gt 0 ]; then
    print_status "OK" "$php_processes PHP-FPM processes running"
    
    # Check for zombie processes
    zombie_count=$(ps aux | grep php-fpm | grep -c '<defunct>')
    if [ "$zombie_count" -gt 0 ]; then
        print_status "WARNING" "$zombie_count zombie PHP-FPM processes found"
        echo "  💡 Consider restarting PHP-FPM"
    fi
else
    print_status "ERROR" "No PHP-FPM processes found"
fi
echo ""

# 6. Test Laravel aplikasi
echo "🧪 LARAVEL APPLICATION TEST:"
if [ -f "artisan" ]; then
    print_status "INFO" "Found Laravel artisan"
    
    # Test database connection
    if timeout 10 php artisan migrate:status > /dev/null 2>&1; then
        print_status "OK" "Database connection working"
    else
        print_status "ERROR" "Database connection failed"
        echo "  💡 Check .env database settings"
    fi
    
    # Test cache
    if php artisan config:cache > /dev/null 2>&1; then
        print_status "OK" "Config cache successful"
    else
        print_status "ERROR" "Config cache failed"
    fi
else
    print_status "WARNING" "Laravel artisan not found in current directory"
fi
echo ""

# 7. Generate fix recommendations
echo "💡 AUTOMATED FIX RECOMMENDATIONS:"

# Memory optimization
cat > /tmp/php_optimization.ini << 'EOF'
; PHP Optimization for Permission Management
memory_limit = 512M
max_execution_time = 120
max_input_vars = 5000
post_max_size = 50M
upload_max_filesize = 50M
max_file_uploads = 20

; PHP-FPM Pool Optimization
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
EOF

print_status "INFO" "Generated PHP optimization config at /tmp/php_optimization.ini"
echo "  💡 Review and apply settings to your PHP configuration"
echo ""

# Quick fix script
echo "🚀 QUICK FIX COMMANDS:"
echo "# 1. Restart services"
echo "sudo systemctl restart nginx"
echo "sudo systemctl restart php8.1-fpm  # or php8.2-fpm depending on your version"
echo ""
echo "# 2. Clear Laravel cache"
echo "php artisan config:clear"
echo "php artisan cache:clear"
echo "php artisan view:clear"
echo ""
echo "# 3. Check permissions"
echo "sudo chown -R www-data:www-data storage/ bootstrap/cache/"
echo "sudo chmod -R 775 storage/ bootstrap/cache/"
echo ""
echo "# 4. If still failing, check specific error:"
echo "tail -f /var/log/nginx/error.log"
echo ""

echo "=== TROUBLESHOOTING COMPLETE ==="
echo "💬 If issues persist:"
echo "   1. Check the generated debug_permission_error.php script output"
echo "   2. Consider implementing permission caching"
echo "   3. Optimize the convertPermissionsToMatrix method"
echo "   4. Use pagination for large permission sets"