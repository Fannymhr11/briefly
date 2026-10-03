# BRIEFLY — Content Monitoring System (Dthree)

Laravel 12 · PHP 8.2+ · MySQL · Blade · Tailwind CSS · Alpine.js (interaksi ringan)

## Menjalankan (XAMPP)
1. Pastikan **Apache** dan **MySQL** aktif di XAMPP Control Panel, dan PHP ≥ 8.2 (`php -v`).
2. Letakkan folder ini di `C:\xampp\htdocs\briefly`.
3. Buat database kosong **briefly** di phpMyAdmin (collation `utf8mb4_unicode_ci`).
4. Di folder proyek:
   ```
   composer install
   copy .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```
   (`public/build` sudah berisi aset hasil build, jadi `npm` hanya perlu jika ingin mengubah CSS/JS: `npm install && npm run build`.)
5. Buka salah satu:
   - `php artisan serve` → http://localhost:8000
   - XAMPP → ubah di `.env`: `APP_URL` dan `ASSET_URL` ke `http://localhost/briefly`, lalu buka http://localhost/briefly (butuh `mod_rewrite` aktif).

Kredensial DB ada di `.env` (`DB_USERNAME=root`, `DB_PASSWORD=` kosong = default XAMPP).
