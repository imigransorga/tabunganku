# Image untuk menjalankan Laravel di Railway / Render.
# Pakai PHP CLI + server bawaan Laravel (artisan serve) — tanpa Apache,
# jadi tidak ada masalah konfigurasi MPM.
FROM php:8.2-cli

# Dependency sistem + ekstensi PHP + Node (untuk build aset Vite).
RUN apt-get update && apt-get install -y \
        git unzip libzip-dev libpng-dev libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin source (vendor & node_modules diabaikan via .dockerignore).
COPY . .

# .env sementara agar perintah artisan saat build tidak error.
# Konfigurasi sebenarnya dari Environment Variables Railway/Render saat runtime.
RUN cp .env.example .env \
    && composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci && npm run build && rm -rf node_modules \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 8080
CMD ["start.sh"]
