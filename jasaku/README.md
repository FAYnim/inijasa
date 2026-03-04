# Jasaku - Platform Manajemen Bisnis Jasa

Aplikasi manajemen bisnis jasa berbasis PHP & MySQL.

## Instalasi

1. **Import Database**
   ```bash
   mysql -u root -p < database.sql
   ```

2. **Konfigurasi Database**
   Edit file `includes/db.php` dan sesuaikan kredensial database:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', 'password_anda');
   define('DB_NAME', 'jasaku');
   ```

3. **Jalankan Server**
   ```bash
   php -S localhost:8000
   ```

4. **Akses Aplikasi**
   Buka browser: http://localhost:8000

## Fitur

- ✅ Authentication (Register, Login, Logout)
- ✅ Multi-Business Support
- ✅ Manajemen Profil Bisnis
- ✅ Paket Jasa (CRUD)
- ✅ Klien Management (CRM Dasar)
- ✅ Deal Pipeline (5 Stage)
- ✅ Diskon Persentase
- ✅ Tracking Pembayaran Deal
- ✅ Keuangan (Income/Expense)
- ✅ Dashboard dengan Metrics & Charts

## Tech Stack

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5
- jQuery 3.x
- Chart.js
- Font Awesome 6
