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

## Akun demo (password semua: `Password123!`)
| Role | Email |
|---|---|
| Admin | admin@dthree.test |
| User | user@dthree.test |
| Marketing Communication | marketing@dthree.test |
| Tim Kreatif | creative@dthree.test |

## Alur
User upload PDF + platform + brand → status **Menunggu Review** → Marketing Communication membuka PDF lalu **Setujui** (otomatis membuat Creative Task) atau **Tolak** (catatan wajib, tidak ada task) → Tim Kreatif mengubah task: Belum Dikerjakan → Sedang Diproses → Sudah Selesai. Semua langkah tercatat di History.

## Hak akses
- **Admin**: dashboard, CRUD user, semua brief + detail/preview/unduh PDF, semua task (hanya lihat), history, profile.
- **User**: upload brief, melihat brief & catatan miliknya sendiri, history sendiri, profile.
- **Marketing**: review/setujui/tolak, semua brief, task (hanya lihat), history, profile.
- **Tim Kreatif**: hanya task dari brief *disetujui*; ubah status task; history; profile.
Middleware `role:` memeriksa role dari database di setiap request. Akses ke URL role lain dialihkan ke dashboard sendiri. PDF disimpan di `storage/app/private/briefs` dengan nama acak dan hanya dilayani lewat route ber-otorisasi.

## Struktur penting
```
app/Http/Controllers/{Admin,User,Marketing,Auth}  + Dashboard/Brief/BriefFile/Task/History/Profile
app/Http/Middleware/RoleMiddleware.php
app/Http/Requests/*            validasi
app/Models/{User,Brief,CreativeTask,ActivityHistory}.php
database/migrations, database/seeders/DatabaseSeeder.php
resources/views/components/*   komponen Blade (sidebar, topbar, modal, badge, tabel, chart, dll.)
resources/views/{dashboards,briefs,tasks,history,profile,admin,user,auth}
resources/css/app.css          token warna Light/Dark
routes/web.php
```
Tabel: `users`, `briefs`, `creative_tasks`, `activity_histories` (+ `sessions`, `password_reset_tokens`).

## Tema
Light/Dark lewat toggle di topbar & halaman login; pilihan disimpan di `localStorage` (`briefly-theme`). Logo placeholder ada di `resources/views/components/logo.blade.php`.
