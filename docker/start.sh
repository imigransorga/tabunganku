#!/usr/bin/env bash
set -e

# Railway/Render menentukan port lewat $PORT.
PORT="${PORT:-8080}"

# Pastikan ada APP_KEY (sebaiknya di-set manual sebagai env var agar tidak berubah tiap deploy).
if [ -z "${APP_KEY}" ]; then
    php artisan key:generate --force || true
fi

# Buat tabel/struktur DB.
php artisan migrate --force || true

# Cache konfigurasi untuk performa (membaca env var dari Railway/Render).
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan server bawaan Laravel.
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
