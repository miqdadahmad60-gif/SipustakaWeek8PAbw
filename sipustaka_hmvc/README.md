# SiPustaka (Laravel + HMVC dengan nwidart/laravel-modules)

Aplikasi perpustakaan kampus: admin mengelola buku & user, anggota (user) melihat buku dan mencatat peminjaman.

## Cara memasang (3 langkah)

1. Ekstrak zip ini ke sebuah folder, mis. `sipustaka_hmvc`.
2. Nyalakan MySQL (XAMPP/Laragon). Pastikan PHP >= 8.2 dan Composer terpasang.
3. Di terminal, dalam folder itu:

```bash
php setup.php            # user root tanpa password (default XAMPP)
# atau: php setup.php root passwordku
php artisan serve
```

`setup.php` akan mengunduh kerangka Laravel, memasang `nwidart/laravel-modules` + `wikimedia/composer-merge-plugin`,
mengatur `composer.json` (merge-plugin `Modules/*/composer.json`), membuat database **db_sipustaka**,
menjalankan migrasi + seeder, dan `storage:link`.

Akun seeder:

| Role  | Email                 | Password   |
|-------|-----------------------|------------|
| admin | admin@sipustaka.test  | admin12345 |
| user  | user@sipustaka.test   | user12345  |

## Struktur & ketentuan tugas

| Ketentuan | Lokasi |
|---|---|
| Authentication manual (di luar HMVC) | `app/Http/Controllers/AuthController.php`, `resources/views/auth/*`, `routes/web.php` |
| Kolom `role` + seeder admin & user | `database/migrations/*_add_role_to_users_table.php`, `database/seeders/DatabaseSeeder.php` |
| Middleware `role` + alias di `bootstrap/app.php` | `app/Http/Middleware/RoleMiddleware.php`, `bootstrap/app.php` |
| Register selalu berperan `user` | `AuthController@register` |
| Modul Buku (CRUD synchronous, 5 field, pencarian, pagination) | `Modules/Book` |
| Modul Peminjaman (CRUD AJAX, upload bukti jpg/png/pdf maks 2 MB) | `Modules/Loan` |
| Modul UserManagement (admin; tidak bisa hapus / ubah role diri sendiri) | `Modules/UserManagement` |
| Form Request di setiap modul + pesan error + pesan sukses | `Modules/*/app/Http/Requests`, view masing-masing |
| 403 untuk user di halaman admin | `role:admin` pada route + `resources/views/errors/403.blade.php` |

## Panduan pembuktian (untuk laporan/screenshot)

- **403**: login sebagai `user@sipustaka.test`, buka `http://127.0.0.1:8000/users` → tampil halaman 403. Atau buka `/books/create`.
- **AJAX / tab Network**: login, buka menu *Peminjaman*, tekan F12 → tab **Network** → filter **Fetch/XHR**, lalu tambah/ubah/hapus data. Request `list`, `POST /loans`, `DELETE /loans/{id}` akan terlihat. Kirim form kosong untuk menampilkan respons **422**.
- **Folder storage**: upload bukti pada form peminjaman, lalu buka folder `storage/app/public/bukti/` (file muncul di sana).
- **Kolom path di phpMyAdmin**: buka database `db_sipustaka` → tabel `loans` → kolom `bukti_file` berisi path seperti `bukti/xxxx.jpg`.
- Hapus data peminjaman → file otomatis ikut terhapus dari `storage/app/public/bukti/`.
