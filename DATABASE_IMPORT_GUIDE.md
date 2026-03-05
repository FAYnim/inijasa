# Panduan Import Ulang Database

## Masalah yang Diperbaiki
- Nama tabel `service_packages` diubah menjadi `services`
- Nama kolom `package_name` diubah menjadi `service_name`
- Nama kolom `service_package_id` di tabel `deals` diubah menjadi `service_id`
- Menambahkan kolom `updated_at` di semua tabel untuk konsistensi

## Langkah-langkah Import Ulang Database

### Opsi 1: Menggunakan phpMyAdmin

1. Buka phpMyAdmin di browser: `http://localhost/phpmyadmin`
2. Pilih database `jasaku_db` di sidebar kiri
3. Klik tab **"Operations"** (atau "Operasi")
4. Scroll ke bawah ke bagian **"Remove database"** (atau "Hapus database")
5. Klik tombol **"Drop the database (DROP)"**
6. Konfirmasi penghapusan database
7. Klik tab **"Import"**
8. Klik tombol **"Choose File"** dan pilih file `database/schema.sql`
9. Klik tombol **"Go"** (atau "Kirim") di bagian bawah
10. Database akan dibuat ulang dengan struktur yang benar

### Opsi 2: Menggunakan Command Line

```bash
# Masuk ke direktori project
cd C:\xampp\htdocs\faydev\jasaku

# Drop database lama dan import ulang
mysql -u root -p -e "DROP DATABASE IF EXISTS jasaku_db;"
mysql -u root -p < database/schema.sql
```

**Catatan:** Jika MySQL root tidak menggunakan password, hapus `-p` dari command di atas.

### Opsi 3: Menggunakan MySQL Workbench

1. Buka MySQL Workbench
2. Connect ke MySQL server
3. Buka file `database/schema.sql`
4. Jalankan seluruh script (Ctrl+Shift+Enter atau klik tombol lightning bolt)

## Verifikasi

Setelah import berhasil, Anda dapat:

1. Login menggunakan akun sample:
   - **Email:** `admin@jasaku.com`
   - **Password:** `password`

2. Data sample yang tersedia:
   - 1 user
   - 1 business (Jasaku Digital Agency)
   - 3 service packages
   - 3 clients
   - 3 deals
   - 4 transactions (2 income, 2 expense)
   - 1 deal payment

3. Cek halaman berikut untuk memastikan semuanya berjalan:
   - `/pages/dashboard.php` - Dashboard dengan metrics
   - `/pages/services.php` - List paket jasa
   - `/pages/deals.php` - List deals
   - `/pages/finance.php` - Keuangan

## Troubleshooting

### Error: Table doesn't exist
- Pastikan Anda sudah menjalankan file `schema.sql` yang terbaru
- Cek apakah database `jasaku_db` sudah dibuat

### Error: Cannot delete or update a parent row
- Ini terjadi karena ada foreign key constraint
- Solusinya adalah drop database terlebih dahulu, kemudian import ulang

### Error: Access denied
- Pastikan MySQL service sudah berjalan
- Cek username dan password MySQL di `includes/db.php`
