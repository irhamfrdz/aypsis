# 🚨 FIX: Vite manifest not found

Error ini muncul karena file build Vite belum ada di server production setelah git pull.

## ⚡ SOLUSI CEPAT (Langsung di Server)

```bash
cd /var/www/aypsis

# Hapus node_modules dan package-lock yang corrupt
rm -rf node_modules package-lock.json

# Install SEMUA dependencies (termasuk devDependencies)
npm install

# Build dengan npx (langsung dari node_modules)
npx vite build

# Verify build berhasil
ls -la public/build/manifest.json

# Clear cache Laravel
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Restart server jika perlu
sudo systemctl restart nginx
# atau
sudo systemctl restart apache2
```

## 🚨 Jika `npm install` Gagal

```bash
# Check Node.js version (minimal v16)
node --version

# Jika terlalu lama, update Node.js:
# Ubuntu/Debian:
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify npm version
npm --version

# Clean npm cache
npm cache clean --force

# Install ulang
npm install
```

## 📋 Penjelasan

Sejak kita mengubah dari CDN ke local assets (offline support), aplikasi membutuhkan:

1. **File manifest**: `/var/www/aypsis/public/build/manifest.json`
2. **Asset files**: 
   - `/var/www/aypsis/public/build/assets/app-*.css`
   - `/var/www/aypsis/public/build/assets/app-*.js`
   - `/var/www/aypsis/public/build/assets/*.woff2` (Font Awesome fonts)

File-file ini **TIDAK di-commit ke Git** (ada di `.gitignore`), jadi harus di-build di server.

## 🔍 Cek File Build Sudah Ada

```bash
ls -la /var/www/aypsis/public/build/
ls -la /var/www/aypsis/public/build/assets/
```

Harusnya ada:
```
build/
├── manifest.json          # Wajib ada!
└── assets/
    ├── app-*.css         # Tailwind + Font Awesome CSS
    ├── app-*.js          # jQuery + App JS
    ├── fa-brands-400-*.woff2
    ├── fa-regular-400-*.woff2
    ├── fa-solid-900-*.woff2
    └── fa-v4compatibility-*.woff2
```

## ⚙️ Requirements di Server

Pastikan sudah install:

```bash
# Check Node.js version (minimal v16)
node --version

# Check npm version
npm --version

# Jika belum ada, install Node.js
# Ubuntu/Debian:
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# CentOS/RHEL:
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install -y nodejs
```

## 🚀 Automated Solution (Update server_pull_commands.sh)

Script sudah diupdate untuk otomatis build Vite:

```bash
# Jalankan script lengkap
bash server_pull_commands.sh
```

Script sekarang include:
- ✅ `npm install` - Install dependencies
- ✅ `npm run build` - Build Vite assets
- ✅ Cache clear
- ✅ Permissions fix

## 🔐 Permissions Check

Pastikan folder build bisa diakses web server:

```bash
sudo chown -R www-data:www-data /var/www/aypsis/public/build
sudo chmod -R 755 /var/www/aypsis/public/build
```

## 🐛 Troubleshooting

### Error: `npm: command not found`
```bash
# Install Node.js terlebih dahulu
# Ubuntu/Debian:
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs

# CentOS/RHEL:
curl -fsSL https://rpm.nodesource.com/setup_18.x | sudo bash -
sudo yum install -y nodejs
```

### Error: `vite: not found` setelah npm install
```bash
# Hapus dan install ulang
rm -rf node_modules package-lock.json
npm install

# Gunakan npx untuk build (langsung execute dari node_modules)
npx vite build

# Atau install vite secara explicit
npm install --save-dev vite@^5.0
npm run build
```

### Error: `EACCES: permission denied`
```bash
# Run dengan sudo atau fix npm permissions
sudo npm install
sudo npm run build
```

### Build berhasil tapi masih error
```bash
# Clear semua cache
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart web server
sudo systemctl restart nginx
```

### File manifest.json ada tapi masih error
```bash
# Check isi file manifest
cat /var/www/aypsis/public/build/manifest.json

# Harusnya berisi mapping seperti:
# {
#   "resources/css/app.css": {...},
#   "resources/js/app.js": {...}
# }

# Jika kosong atau corrupt, build ulang:
rm -rf /var/www/aypsis/public/build
npm run build
```

## 📝 Update .gitignore

File build **TIDAK boleh di-commit** ke Git. Pastikan `.gitignore` sudah benar:

```gitignore
/public/build
/public/hot
```

## 🎯 Best Practice untuk Production

1. **Always build di server** - Jangan commit file build
2. **Use CI/CD** - Automate build process
3. **Monitor build size** - Jangan sampai terlalu besar
4. **Cache assets** - Set proper cache headers di nginx/apache

## 📞 Next Steps

Setelah fix error ini:

1. ✅ Test login
2. ✅ Test offline mode (matikan internet, reload page)
3. ✅ Check Font Awesome icons muncul
4. ✅ Check CSS Tailwind terload
5. ✅ Test import CSV mobil

---

**🆘 Jika masih error, kirim output dari:**
```bash
npm run build
ls -la /var/www/aypsis/public/build/
cat /var/www/aypsis/public/build/manifest.json
```
