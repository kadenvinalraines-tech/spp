#!/bin/bash
# Script Deployment Aman untuk SPP Laravel (Zero Downtime)
# Jalankan script ini di VPS Anda setiap kali ada pembaruan dari GitHub

echo "======================================"
echo "Memulai proses update & deployment..."
echo "======================================"

# 1. Tahan akses user sementara (menampilkan halaman maintenance)
echo "=> Mengaktifkan mode maintenance..."
php artisan down --render="errors::503" --secret="bypass-token-spp"

# 2. Ambil kode terbaru dari GitHub
echo "=> Mengambil kode terbaru dari GitHub..."
git pull origin main

# 3. Perbarui library PHP
echo "=> Memperbarui dependensi Composer..."
composer install --optimize-autoloader --no-dev

# 4. Jalankan perubahan database (jika ada)
echo "=> Menjalankan migrasi database..."
php artisan migrate --force

# 5. Build aset Frontend (Vite)
echo "=> Membangun aset frontend..."
npm install
npm run build

# 6. Refresh semua cache sistem
echo "=> Membersihkan dan membangun ulang cache..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Buka kembali sistem
echo "=> Menonaktifkan mode maintenance..."
php artisan up

echo "======================================"
echo "Deployment Berhasil! Sistem sudah berjalan."
echo "======================================"
