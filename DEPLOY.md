# Panduan Deploy (Railway / Render)

Aplikasi ini Laravel + MySQL. **Tidak bisa di Vercel** (Vercel untuk JS/static).
Gunakan Railway atau Render yang mendukung PHP + MySQL. Keduanya memakai `Dockerfile` di repo ini.

---

## A. Railway (paling mudah)

1. Buka https://railway.app → **New Project** → **Deploy from GitHub repo** → pilih `imigransorga/tabunganku`.
2. Railway mendeteksi `Dockerfile` dan mulai build.
3. Tambah database: di project → **New** → **Database** → **MySQL**.
4. Buka service aplikasi → tab **Variables** → tambahkan:

   | Variable | Value |
   |---|---|
   | `APP_NAME` | `Tabunganku` |
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_KEY` | (lihat langkah 5) |
   | `APP_URL` | URL domain Railway-mu (mis. `https://tabunganku-production.up.railway.app`) |
   | `DB_CONNECTION` | `mysql` |
   | `DB_HOST` | `${{MySQL.MYSQLHOST}}` |
   | `DB_PORT` | `${{MySQL.MYSQLPORT}}` |
   | `DB_DATABASE` | `${{MySQL.MYSQLDATABASE}}` |
   | `DB_USERNAME` | `${{MySQL.MYSQLUSER}}` |
   | `DB_PASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
   | `SESSION_DRIVER` | `database` |
   | `CACHE_STORE` | `database` |
   | `QUEUE_CONNECTION` | `database` |

   > `${{MySQL.XXX}}` adalah reference variable — Railway mengisinya otomatis dari service MySQL.

5. **APP_KEY**: jalankan di lokal `php artisan key:generate --show`, salin hasilnya
   (format `base64:....`) ke variable `APP_KEY`. Penting di-set manual agar sesi tidak
   ter-reset tiap deploy.
6. Setelah deploy sukses, akun admin otomatis dibuat oleh seeder? **Tidak** — migrate jalan
   otomatis, tapi seeder tidak. Buat akun lewat halaman **Register**, atau jalankan seeder
   sekali via Railway shell: `php artisan db:seed --force`.
7. Buka domain → selesai.

---

## B. Render

1. https://render.com → **New** → **Web Service** → connect repo → pilih **Docker**.
2. **New** → **MySQL** (atau pakai MySQL eksternal mis. Aiven/PlanetScale).
3. Di Web Service → **Environment** → isi variable yang sama seperti tabel Railway di atas
   (host/user/password ambil dari halaman database Render).
4. Deploy.

---

## Catatan
- `migrate --force` dijalankan otomatis tiap start (lihat `docker/start.sh`).
- Kalau ingin akun admin default (`admin@tabunganku.com` / `admin12345`),
  jalankan `php artisan db:seed --force` sekali setelah deploy pertama.
- Jangan commit file `.env` asli — kredensial lewat Environment Variables.
