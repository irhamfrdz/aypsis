#!/bin/bash

# Setup Cron Job for Laravel Scheduler
# This script will setup the Laravel scheduler to run every minute

echo "🔧 Setting up Laravel Scheduler Cron Job..."

# Get current directory
CURRENT_DIR=$(pwd)

# Create cron entry
CRON_ENTRY="* * * * * cd $CURRENT_DIR && php artisan schedule:run >> /dev/null 2>&1"

# Check if cron entry already exists
(crontab -l 2>/dev/null | grep -F "schedule:run") && echo "⚠️  Cron job already exists!" && exit 1

# Add to crontab
(crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -

echo "✅ Cron job added successfully!"
echo ""
echo "📋 Current crontab:"
crontab -l
echo ""
echo "ℹ️  The scheduler will run every minute and execute scheduled tasks."
echo "ℹ️  Your command 'tagihan:recalculate-grand-total' will run hourly."
echo ""
echo "📝 To view scheduler logs:"
echo "   tail -f storage/logs/grand-total-recalculation.log"
echo ""
echo "🔄 To manually run the scheduler once:"
echo "   php artisan schedule:run"
echo ""
echo "✅ Done!"
