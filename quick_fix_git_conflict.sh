#!/bin/bash
# Quick fix untuk git conflict - jalankan di server
# Script ini akan otomatis backup dan pull

echo "🔧 Quick Fix: Git Pull Conflict Resolver"
echo "========================================="
echo ""

# Warna untuk output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function untuk print dengan warna
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

# Check if we're in a git repository
if [ ! -d .git ]; then
    print_error "Not a git repository. Please run this in your project root."
    exit 1
fi

echo "Current directory: $(pwd)"
echo "Git status:"
git status --short
echo ""

# Show what files have conflicts
echo "Files with local changes:"
git diff --name-only
echo ""

# Backup timestamp
BACKUP_TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Ask for confirmation
print_warning "This script will:"
echo "  1. Backup your local changes"
echo "  2. Stash them temporarily"
echo "  3. Pull latest from GitHub"
echo "  4. Keep stash for manual recovery if needed"
echo ""
read -p "Continue? (y/n): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_error "Operation cancelled by user"
    exit 1
fi

# Create backup directory if not exists
BACKUP_DIR="git_backups"
mkdir -p "$BACKUP_DIR"

echo ""
print_info "Step 1: Creating backups..."

# Backup all modified files
for file in $(git diff --name-only); do
    if [ -f "$file" ]; then
        backup_file="${BACKUP_DIR}/$(basename ${file}).backup.${BACKUP_TIMESTAMP}"
        cp "$file" "$backup_file"
        print_success "Backed up: $file → $backup_file"
    fi
done

echo ""
print_info "Step 2: Stashing local changes..."
git stash save "Backup before pull - ${BACKUP_TIMESTAMP}"

if [ $? -eq 0 ]; then
    print_success "Local changes stashed successfully"
else
    print_error "Failed to stash changes"
    exit 1
fi

echo ""
print_info "Step 3: Pulling latest from GitHub..."
git pull origin main

if [ $? -eq 0 ]; then
    print_success "Pull completed successfully!"
else
    print_error "Pull failed. Recovering stashed changes..."
    git stash pop
    exit 1
fi

echo ""
echo "========================================="
print_success "Git conflict resolved!"
echo "========================================="
echo ""

print_info "Summary:"
echo "  • Backups location: ./${BACKUP_DIR}/"
echo "  • Stash saved as: 'Backup before pull - ${BACKUP_TIMESTAMP}'"
echo ""

print_info "To see your stashed changes:"
echo "  git stash list"
echo ""

print_info "To restore your stashed changes (if needed):"
echo "  git stash pop"
echo ""

print_info "To compare backup with current version:"
ls -1 ${BACKUP_DIR}/*.backup.${BACKUP_TIMESTAMP} 2>/dev/null | while read backup; do
    original=$(basename "$backup" | sed "s/.backup.${BACKUP_TIMESTAMP}//")
    if [ -f "$original" ]; then
        echo "  diff ${backup} ${original}"
    fi
done

echo ""
print_success "You can now proceed with your deployment!"
echo ""
