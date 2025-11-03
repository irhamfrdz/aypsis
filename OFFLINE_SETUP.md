# Setup untuk Mode Offline

Aplikasi ini sekarang sudah dikonfigurasi untuk bisa berjalan **tanpa koneksi internet**.

## Perubahan yang Dilakukan

### 1. Tailwind CSS
- **Sebelumnya**: Menggunakan CDN `https://cdn.tailwindcss.com`
- **Sekarang**: Menggunakan Tailwind CSS lokal via Vite

### 2. jQuery
- **Sebelumnya**: Menggunakan CDN `https://code.jquery.com/jquery-3.6.0.min.js`
- **Sekarang**: Installed via NPM dan di-bundle dengan Vite

### 3. Font Awesome
- **Sebelumnya**: Menggunakan CDN `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`
- **Sekarang**: Installed via NPM (@fortawesome/fontawesome-free)

### 4. Fonts
- **Sebelumnya**: Google Fonts (Inter) - membutuhkan internet
- **Sekarang**: Menggunakan system fonts (web-safe fonts)

## Assets yang Di-build

Semua assets ada di folder `public/build/`:
```
public/build/
├── assets/
│   ├── app-*.css (Tailwind CSS + Font Awesome CSS)
│   ├── app-*.js (jQuery + App JavaScript)
│   ├── fa-brands-400-*.woff2
│   ├── fa-regular-400-*.woff2
│   ├── fa-solid-900-*.woff2
│   └── fa-v4compatibility-*.woff2
└── manifest.json
```

## Cara Build Assets

### Development Mode (Auto-reload)
```bash
npm run dev
```

### Production Build (Untuk deployment/offline)
```bash
npm run build
```

## Setup untuk Developer Baru

1. Install Node dependencies:
```bash
npm install
```

2. Build assets:
```bash
npm run build
```

3. Assets siap digunakan offline!

## Catatan Penting

- ✅ Aplikasi bisa berjalan **100% offline** setelah assets di-build
- ✅ Semua CSS, JavaScript, dan fonts sudah lokal
- ✅ Tidak ada dependency ke CDN eksternal
- ✅ File build hasil sudah di-commit ke repository
- ⚠️  Jika edit `resources/css/app.css` atau `resources/js/app.js`, jalankan `npm run build` lagi

## Troubleshooting

**Problem**: CSS tidak muncul
- **Solusi**: Pastikan sudah jalankan `npm run build`

**Problem**: jQuery error ($ is not defined)
- **Solusi**: Build ulang dengan `npm run build`

**Problem**: Font Awesome icons tidak muncul
- **Solusi**: Check folder `public/build/assets/` ada file `.woff2`

**Problem**: Vite error saat build
- **Solusi**: 
  ```bash
  rm -rf node_modules
  npm install
  npm run build
  ```
