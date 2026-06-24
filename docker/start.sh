#!/usr/bin/env bash
set -e

# Jaminan runtime: mod_php hanya boleh dengan mpm_prefork. Hapus MPM lain bila ada.
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork rewrite >/dev/null 2>&1 || true

# Railway/Render menentukan port lewat $PORT; Apache harus mendengarkan port itu.
PORT="${PORT:-80}"
sed -ri "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/\*:80>/*:${PORT}>/" /etc/apache2/sites-available/000-default.conf

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

exec apache2-foreground
