# 📋 Product Requirements Document (PRD)

## IniJasa — Platform Manajemen Bisnis Jasa

**Versi:** 1.0 (MVP)  
**Tanggal:** 5 Maret 2026  
**Status:** Draft  
**Target Launch:** TBD

---

## 1. Executive Summary

### 1.1 Visi Produk
Menjadi "command center" sederhana namun powerful bagi Agensi Jasa dan UMKM Jasa di Indonesia untuk mengelola operasional bisnis mereka — dari pengelolaan klien, penawaran jasa, hingga pencatatan keuangan — dalam satu platform terintegrasi.

### 1.2 Target Pengguna
| Segmen | Karakteristik | Kebutuhan Utama |
|--------|--------------|-----------------|
| **Agensi Jasa** | Tim kecil (2-10 orang), project-based, perlu tracking deal dan klien | Pipeline penjualan, manajemen banyak proyek |
| **UMKM Jasa** | Owner-operator, bisnis lokal, sederhana tapi perlu tercatat | Pencatatan keuangan, database klien, promo |

### 1.3 Value Proposition
> *"Kelola bisnis jasamu tanpa ribet — dari pertama klien hubungi sampai uang masuk rekening, semua tercatat rapi di satu tempat."*

---

## 2. Scope MVP

### 2.1 In-Scope (Fitur MVP)
| Modul | Fitur | Keterangan |
|-------|-------|------------|
| **Auth & Onboarding** | Register, Login, First-time business setup | Multi-business per user |
| **Business Profile** | Kelola profil bisnis (nama, deskripsi, kontak, logo) | Multiple business support |
| **Service Package** | CRUD paket jasa one-time | Nama, deskripsi, harga, status aktif/nonaktif |
| **Client Management** | Database klien (nama, kontak, perusahaan, catatan) | Basic CRM |
| **Deal Pipeline** | Tracking kesepakatan dengan stage fixed | 5 stage: Lead → Qualified → Proposal → Negotiation → Won/Lost |
| **Promotion** | Diskon percentage untuk deal | Simple percentage discount |
| **Financial Tracking** | Income & Expense manual + Payment tracking | Status: Pending, Paid, Partial, Cancelled |
| **Dashboard** | Overview metrics | Total revenue, active deals, total clients |

### 2.2 Out-of-Scope (Future Development)
- Multi-user/role dalam satu bisnis (MVP: owner only)
- Recurring/subscription service
- Invoice generation & email
- Payment gateway integration
- Client portal (klien login)
- Advanced reporting & analytics
- Mobile app (responsive web only)
- Chat/komunikasi terintegrasi
- Audit trail & compliance features

---

## 3. User Flow & Journey

### 3.1 Onboarding Flow
```
Landing Page → Register → Login → 
[First Time?] → Business Setup Form → Dashboard
                [Not First Time] → Dashboard
```

### 3.2 Core Workflow
```
1. Setup Business Profile (wajib pertama kali)
2. Create Service Package(s)
3. Add Client ke database
4. Create Deal (pilih client + service package)
5. Move Deal through Pipeline stages
6. Apply Discount jika ada
7. Track Payment status
8. Record Income/Expense
```

---

## 4. Functional Requirements

### 4.1 Authentication & User Management

#### 4.1.1 Register
- **Input:** Nama lengkap, email, password, konfirmasi password
- **Validasi:** Email unik, password min 8 karakter
- **Proses:** Insert ke tabel `users`, hash password dengan `password_hash()`
- **Output:** Akun aktif, redirect ke login

#### 4.1.2 Login
- **Input:** Email, password, remember me (optional)
- **Validasi:** Cek credentials, `password_verify()`
- **Session:** Set `$_SESSION['user_id']`, `$_SESSION['business_id']` (default: primary business)
- **Redirect:** Ke dashboard atau business setup (jika first time)

#### 4.1.3 Multi-Business Switching
- User bisa memiliki multiple business
- Switch business via dropdown menu
- Session `business_id` di-update saat switch

---

### 4.2 Business Profile Management

#### 4.2.1 First-Time Setup (Wajib)
- **Trigger:** Setelah login pertama kali atau jika belum punya business
- **Field:** 
  - Nama Bisnis (required)
  - Kategori Bisnis (dropdown: Kreatif/Desain, Konsultan, Kebersihan, Perbaikan, Lainnya)
  - Deskripsi Singkat
  - Alamat
  - Nomor Telepon Bisnis
  - Email Bisnis
  - Logo (upload, max 2MB, jpg/png)
- **Proses:** Insert ke `businesses`, set sebagai primary business

#### 4.2.2 Edit Business Profile
- Update semua field di atas
- Ganti logo (replace file lama)

---

### 4.3 Service Package Management

#### 4.3.1 Create Service Package
| Field | Type | Validasi |
|-------|------|----------|
| Nama Paket | Varchar(100) | Required, unique per business |
| Deskripsi | Text | Optional |
| Harga | Decimal(15,2) | Required, >= 0 |
| Status | Enum | Active/Inactive, default: Active |

#### 4.3.2 List & Manage
- Tabel dengan pagination (10 per halaman)
- Search by nama
- Sort by harga, nama, status
- Edit & Soft Delete (set is_deleted = 1)

---

### 4.4 Client Management (Basic CRM)

#### 4.4.1 Add Client
| Field | Type | Validasi |
|-------|------|----------|
| Nama Klien | Varchar(100) | Required |
| Perusahaan | Varchar(100) | Optional |
| Email | Varchar(100) | Valid email format |
| Telepon | Varchar(20) | Numeric/spasi only |
| Alamat | Text | Optional |
| Catatan | Text | Optional |
| Sumber | Enum | Referral, Social Media, Direct, Website, Lainnya |

#### 4.4.2 Client List
- Tabel dengan pagination
- Filter: Sumber, Search by nama/perusahaan
- Detail view: Histori deal klien (link ke modul deal)

---

### 4.5 Deal Pipeline Management

#### 4.5.1 Fixed Pipeline Stages
| Stage | Keterangan | Probability |
|-------|------------|-------------|
| 1. Lead | Klien baru inquiry | 10% |
| 2. Qualified | Kebutuhan & budget cocok | 25% |
| 3. Proposal | Penawaran sudah dikirim | 50% |
| 4. Negotiation | Negosiasi harga/terms | 75% |
| 5. Won | Deal sealed! | 100% |
| 5. Lost | Deal gagal | 0% |

#### 4.5.2 Create Deal
| Field | Type | Keterangan |
|-------|------|------------|
| Judul Deal | Varchar(150) | Required |
| Pilih Klien | Dropdown | Dari database client |
| Pilih Service | Dropdown | Dari service package |
| Nilai Deal | Decimal(15,2) | Auto-fill dari harga service, editable |
| Expected Close Date | Date | Optional |
| Catatan | Text | Optional |

#### 4.5.3 Move Stage
- Drag-drop atau button "Move to Next Stage"
- History stage change tersimpan (created_at per stage)
- Stage Won/Lost adalah final (tidak bisa move lagi)

#### 4.5.4 Apply Discount
- Input: Discount percentage (0-100%)
- Kalkulasi: `Final Value = Deal Value - (Deal Value * Discount / 100)`
- Hanya bisa apply di stage Proposal, Negotiation, atau Won

---

### 4.6 Financial Management

#### 4.6.1 Income Tracking
| Field | Type | Keterangan |
|-------|------|------------|
| Judul | Varchar(150) | Required |
| Kategori | Enum | Deal Payment, Lainnya |
| Jumlah | Decimal(15,2) | Required, > 0 |
| Tanggal | Date | Required |
| Metode | Enum | Transfer, Cash, QRIS, Lainnya |
| Catatan | Text | Optional |
| Relasi Deal | Dropdown | Optional, link ke deal yang Won |

#### 4.6.2 Expense Tracking
| Field | Type | Keterangan |
|-------|------|------------|
| Judul | Varchar(150) | Required |
| Kategori | Enum | Operasional, Marketing, Tools, Lainnya |
| Jumlah | Decimal(15,2) | Required, > 0 |
| Tanggal | Date | Required |
| Catatan | Text | Optional |

#### 4.6.3 Payment Tracking untuk Deal
- Status: **Pending** → **Partial** → **Paid** atau **Cancelled**
- Input partial payment: Jumlah yang sudah dibayar
- Sisa otomatis terhitung
- History pembayaran tersimpan

---

### 4.7 Dashboard (Overview Metrics)

#### 4.7.1 Metrics Display
| Metric | Keterangan |
|--------|------------|
| Total Revenue (Bulan Ini) | Sum income current month |
| Total Active Deals | Count deals yang stage != Won/Lost |
| Total Clients | Count clients |
| Deal Conversion Rate | (Won Deals / Total Deals) * 100 |
| Outstanding Payments | Sum deal value yang status != Paid |

#### 4.7.2 Visualisasi
- Card-based metrics (Bootstrap cards)
- Simple bar chart: Revenue vs Expense (6 bulan terakhir) — menggunakan Chart.js atau canvas sederhana

---

## 5. Database Schema (MySQL)

### 5.1 Entity Relationship Diagram (Deskriptif)

```
users (1) ---< (N) businesses (1) ---< (N) service_packages
                     |
                     +---< (N) clients
                     |
                     +---< (N) deals >--- service_packages
                     |       |
                     |       +---< (N) deal_payments
                     |
                     +---< (N) transactions (income/expense)
```

### 5.2 Tabel Detail

#### `users`
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `businesses`
```sql
CREATE TABLE businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_name VARCHAR(150) NOT NULL,
    category ENUM('Kreatif/Desain', 'Konsultan', 'Kebersihan', 'Perbaikan', 'Lainnya'),
    description TEXT,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    logo_path VARCHAR(255),
    is_primary BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### `service_packages`
```sql
CREATE TABLE service_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    package_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    is_deleted BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id)
);
```

#### `clients`
```sql
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    notes TEXT,
    source ENUM('Referral', 'Social Media', 'Direct', 'Website', 'Lainnya'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id)
);
```

#### `deals`
```sql
CREATE TABLE deals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    client_id INT NOT NULL,
    service_package_id INT,
    deal_title VARCHAR(150) NOT NULL,
    deal_value DECIMAL(15,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    final_value DECIMAL(15,2) NOT NULL,
    current_stage ENUM('Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost') DEFAULT 'Lead',
    expected_close_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (service_package_id) REFERENCES service_packages(id)
);
```

#### `deal_payments`
```sql
CREATE TABLE deal_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_date DATE NOT NULL,
    method ENUM('Transfer', 'Cash', 'QRIS', 'Lainnya'),
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id)
);
```

#### `transactions`
```sql
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    type ENUM('Income', 'Expense') NOT NULL,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(50),
    amount DECIMAL(15,2) NOT NULL,
    transaction_date DATE NOT NULL,
    method VARCHAR(50),
    notes TEXT,
    deal_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id),
    FOREIGN KEY (deal_id) REFERENCES deals(id)
);
```

---

## 6. User Interface Requirements

### 6.1 Layout Structure
- **Sidebar Navigation:** Dashboard, Deals, Clients, Service Packages, Finance, Business Profile
- **Topbar:** Business Switcher, User Profile, Logout
- **Main Content:** Fluid container, max-width 1200px

### 6.2 Halaman Utama

| Halaman | Fungsi Utama |
|---------|-------------|
| `login.php` | Form login |
| `register.php` | Form register |
| `setup-business.php` | First-time business setup |
| `dashboard.php` | Overview metrics |
| `deals.php` | List deals + pipeline view |
| `deal-form.php` | Add/edit deal |
| `clients.php` | List clients |
| `client-form.php` | Add/edit client |
| `services.php` | List service packages |
| `service-form.php` | Add/edit service |
| `finance.php` | Income & expense list |
| `transaction-form.php` | Add transaction |
| `business-profile.php` | Edit business settings |

### 6.3 Responsive Design
- Bootstrap 5 grid system
- Sidebar collapsible di mobile (hamburger menu)
- Tables horizontal scroll di mobile

---

## 7. Technical Requirements

### 7.1 Tech Stack
| Layer | Teknologi |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript (jQuery 3.x), Bootstrap 5, Font Awesome 6 |
| Backend | PHP 7.4+ (procedural/basic OOP) |
| Database | MySQL 5.7+ |
| Hosting | Shared Hosting (cPanel compatible) |

### 7.2 Struktur Folder
```
/inijasa
├── /assets
│   ├── /css (custom styles)
│   ├── /js (custom scripts, jQuery AJAX handlers)
│   ├── /images
│   └── /uploads (logo uploads)
├── /includes
│   ├── db.php (koneksi database)
│   ├── functions.php (helper functions)
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── /pages (modul-modul)
│   ├── dashboard.php
│   ├── deals.php
│   └── ...
├── /auth
│   ├── login.php
│   ├── register.php
│   └── logout.php
└── index.php (redirect ke login/dashboard)
```

### 7.3 Security Requirements
- **SQL Injection:** Gunakan prepared statements (`mysqli_prepare` atau PDO)
- **XSS:** Escape output dengan `htmlspecialchars()`
- **CSRF:** Token CSRF untuk form POST (optional untuk MVP, recommended)
- **File Upload:** Validasi type & size, rename file, store outside web root atau restrict access
- **Password:** Min 8 char, hash dengan `password_hash($pass, PASSWORD_DEFAULT)`

### 7.4 Performance
- Pagination untuk semua list (10-20 item per halaman)
- Index pada foreign keys dan field yang sering di-search
- Minimize query N+1 dengan JOIN yang tepat

---

## 8. Non-Functional Requirements

### 8.1 Usability
- Tidak perlu manual book — UI harus self-explanatory
- Tooltips untuk istilah teknis
- Confirmation dialog untuk delete

### 8.2 Reliability
- Backup database harian (via cron job hosting)
- Error handling dengan try-catch, user-friendly error message

### 8.3 Scalability (Future)
- Struktur database support multi-user per business
- Service package design bisa di-extend untuk recurring

---

## 9. Success Metrics (Post-Launch)

| Metric | Target |
|--------|--------|
| User Registration | 100 users dalam 3 bulan |
| Business Setup Completion | >70% registrant complete setup |
| Active Usage | >50% login mingguan |
| Deal Created | Rata-rata 5 deal per bisnis aktif |

---

## 10. Timeline Estimasi (MVP)

| Fase | Durasi | Deliverable |
|------|--------|-------------|
| Setup & Auth | 1 minggu | Login, register, business setup |
| Master Data | 1 minggu | Service packages, clients |
| Deal Pipeline | 1.5 minggu | Deals, stages, discount |
| Finance | 1 minggu | Income, expense, payment tracking |
| Dashboard | 0.5 minggu | Overview metrics |
| Testing & Polish | 1 minggu | Bug fix, UI refinement |
| **Total** | **6 minggu** | MVP Launch |

---

## 11. Risiko & Mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Scope creep | Strict adherence to MVP feature list |
| Shared hosting limitation | Optimize query, implement caching sederhana |
| User tidak paham cara pakai | Buat video tutorial singkat, tooltips ekstensif |
| Data loss | Automated backup, export data feature |

---

## 12. Appendix

### 12.1 Definisi Istilah
- **Deal:** Kesepakatan/jualan dengan satu klien untuk satu paket jasa
- **Pipeline:** Alur proses deal dari lead sampai won/lost
- **Multi-tenant:** Satu aplikasi, multiple bisnis terpisah data