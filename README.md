# 🚀 IniJasa

> Platform Manajemen Bisnis Jasa untuk Agensi dan UMKM Indonesia

**IniJasa** adalah platform SaaS yang dirancang khusus untuk membantu pemilik bisnis jasa di Indonesia mengelola operasional bisnis mereka secara terintegrasi — mulai dari database klien, penawaran proyek, pipeline sales, hingga pencatatan keuangan — dalam satu command center yang sederhana namun powerful.

---

## 📋 Daftar Isi

- [Overview](#-overview)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Struktur Projek](#-struktur-projek)
- [Modul & Fungsionalitas](#-modul--fungsionalitas)
- [Database Schema](#-database-schema)
- [Development Notes](#-development-notes)
- [Roadmap](#-roadmap)

---

## 🎯 Overview

### Problem Statement

Banyak pemilik bisnis jasa (agensi, konsultan, UMKM) di Indonesia masih kesulitan mengelola operasional mereka:
- Data klien tersebar di WhatsApp, notes, atau Excel
- Sulit tracking deal mana yang masih prospek, sudah closing, atau gagal
- Keuangan tidak tercatat dengan baik
- Tidak tahu performa bisnis secara real-time

### Solution

IniJasa menyediakan platform all-in-one yang menyatukan:
- **CRM sederhana** untuk database klien
- **Deal Pipeline** untuk tracking sales funnel
- **Service Management** untuk kelola paket jasa
- **Financial Tracking** untuk catat pemasukan & pengeluaran
- **Dashboard Analytics** untuk monitor performa bisnis

### Target Pengguna

| Segmen | Karakteristik | Use Case |
|--------|---------------|----------|
| **Agensi Jasa** | Tim kecil (2-10 orang), project-based | Pipeline management, multi-client tracking |
| **UMKM Jasa** | Owner-operator, bisnis lokal | Pencatatan keuangan, database klien, promo |
| **Konsultan** | Freelancer/small firm | Service packages, deal tracking |

### Value Proposition

> *"Kelola bisnis jasamu tanpa ribet — dari pertama klien hubungi sampai uang masuk rekening, semua tercatat rapi di satu tempat."*

---

## ✨ Fitur Utama

### 1. 🏢 Multi-Business Management
- Satu user bisa kelola beberapa bisnis
- Switch business dengan mudah via dropdown
- Setiap bisnis punya data terpisah (klien, deals, keuangan)

### 2. 📦 Service Package Management
- Buat dan kelola paket jasa (one-time service)
- Set harga, deskripsi, status aktif/nonaktif
- Quick-select saat create deal

### 3. 👥 Client CRM
- Database klien lengkap dengan company, kontak, alamat
- Tracking sumber klien (Referral, Social Media, Direct, etc.)
- History deals per klien
- Notes untuk catatan penting

### 4. 💼 Deal Pipeline Management
Pipeline 5-stage yang fixed untuk konsistensi:

```
Lead → Qualified → Proposal → Negotiation → Won/Lost
10%     25%         50%         75%          100%/0%
```

**Fitur Deal:**
- Assign client & service package
- Nilai deal otomatis dari harga service
- Apply discount (percentage)
- Expected close date
- Track stage progression
- Deal history

### 5. 💰 Financial Management

#### Income Tracking
- Kategori: Deal Payment, Lainnya
- Metode: Transfer, Cash, QRIS, Lainnya
- Link langsung ke deal (optional)
- Filter by date range

#### Expense Tracking
- Kategori: Operasional, Marketing, Tools, Lainnya
- Pencatatan pengeluaran bisnis
- Analisis profitabilitas

#### Payment Status
- **Pending**: Belum ada pembayaran
- **Partial**: Sebagian sudah dibayar
- **Paid**: Lunas
- **Cancelled**: Deal dibatalkan

### 6. 📄 Invoicing & Billing
- Buat invoice profesional secara mandiri atau dari deal yang sudah ada
- Download invoice dalam format PDF
- Lacak status tagihan (Draft, Sent, Paid, Overdue)
- Kustomisasi pajak dan item secara dinamis

### 7. 📊 Dashboard & Analytics

**Metrics Display:**
- 💵 Total Revenue (bulan ini) + percentage change
- 📈 Active Deals (deals yang belum Won/Lost)
- 👥 Total Clients + growth rate
- 📉 Deal Conversion Rate
- ⏳ Outstanding Payments

**Visualisasi:**
- Revenue vs Expense chart (6 bulan terakhir)
- Deal pipeline overview
- Recent activities

### 8. 🎨 Professional Branding
- Upload logo bisnis
- Business profile lengkap (kategori, deskripsi, kontak)
- Professional look untuk kredibilitas

### 9. 📈 Laporan & Export Data
- **Laporan Laba-Rugi (Profit & Loss)**: Analisis komprehensif pendapatan bulanan dikurangi pengeluaran.
- **Laporan Pipeline Sales**: Ringkasan performa deal di tiap stage (total, nilai, conversion rate).
- **Export to CSV**: Kemudahan satu klik untuk men-download data Klien, Deals, Transaksi Keuangan, dan Laporan ke format CSV/Excel.

---

## 🛠 Tech Stack

### Backend
- **PHP 7.4+** — Server-side logic
- **MySQL 8.0** — Database dengan InnoDB engine
- **MySQLi** — Database interface (prepared statements untuk security)

### Frontend
- **HTML5/CSS3** — Semantic markup
- **Bootstrap 5.3** — Responsive UI framework
- **JavaScript (Vanilla)** — Client-side interactivity
- **Font Awesome 6** — Icon library
- **Chart.js** (planned) — Data visualization

### Development Tools
- **XAMPP** — Local development environment
- **Git** — Version control
- **VS Code** — Code editor

### Security Features
- Password hashing dengan `password_hash()` (bcrypt)
- Prepared statements untuk SQL injection prevention
- CSRF token untuk form submissions
- Session management untuk authentication
- Input validation & sanitization

---

## 📁 Struktur Projek

```
inijasa/
├── assets/
│   ├── css/
│   │   └── style.css              # Custom styling
│   ├── js/
│   │   └── main.js                # Frontend interactivity
│   └── uploads/
│       └── logos/                 # Business logos storage
│
├── auth/
│   ├── login.php                  # Login page
│   ├── register.php               # Registration page
│   └── logout.php                 # Logout handler
│
├── database/
│   ├── schema.sql                 # Database structure
│   └── schema_with_rich_data.sql  # Schema + sample data
│
├── includes/
│   ├── db.php                     # Database connection
│   ├── functions.php              # Helper functions
│   ├── header.php                 # Page header component
│   ├── sidebar.php                # Navigation sidebar
│   └── footer.php                 # Page footer component
│
├── index.php                      # Landing page
├── dashboard.php                  # Dashboard (overview metrics)
│
├── setup-business.php             # First-time business setup
├── business-profile.php           # Edit business profile
│
├── services.php                   # Service packages list
├── service-form.php               # Add/Edit service
│
├── clients.php                    # Client list & CRM
├── client-form.php                # Add/Edit client
│
├── deals.php                      # Deal pipeline view
├── deal-form.php                  # Add/Edit deal
├── deal-detail.php                # View deal details, history & payments
│
├── invoices.php                   # Invoice list
├── invoice-form.php               # Create/Edit invoice
├── invoice-detail.php             # Invoice details view
├── invoice-pdf.php                # Generate PDF invoice
│
├── finance.php                    # Financial tracking (income/expense)
├── transaction-form.php           # Add/Edit transaction
│
├── reports.php                    # Laporan Laba-Rugi & Pipeline
├── export-csv.php                 # Handler export data ke format CSV
│
├── PRD.md                         # Product Requirements Document
└── README.md                      # Dokumentasi projek (you are here)
```

### File Organization Pattern

#### Page Structure
Setiap halaman utama mengikuti pola:
1. **Require dependencies** (`db.php`, `functions.php`)
2. **Authentication check** (`requireLogin()`)
3. **Business validation** (redirect ke setup jika belum punya bisnis)
4. **Data fetching** (queries)
5. **Include header**
6. **Page content**
7. **Include footer**

#### Form Pattern
Setiap form mengikuti pola:
- CSRF token generation (`generateCSRFToken()`)
- POST handler dengan validation
- Success: Flash message + redirect
- Error: Show error message

---

## 🔧 Modul & Fungsionalitas

### 1. Authentication Module (`auth/`)

**Register Flow:**
```
Input: Name, Email, Password → 
Validation → 
Hash Password → 
Insert to DB → 
Redirect to Login
```

**Login Flow:**
```
Input: Email, Password → 
Verify Credentials → 
Set Session (user_id, business_id) → 
Check if has business? 
  → Yes: Dashboard
  → No: Setup Business
```

### 2. Business Module

**First-Time Setup:**
- Wajib sebelum bisa akses fitur lain
- Input: Business name, category, description, contact info, logo
- Set as primary business
- Session `business_id` di-set

**Multi-Business:**
- User bisa create multiple businesses
- Switch business via dropdown di sidebar
- Filter semua data by `business_id`

### 3. Service Module

**CRUD Operations:**
- Create/Update service package
- Soft delete (`is_deleted = 1`)
- Status: Active/Inactive
- Pagination & search

**Business Logic:**
- Service harga auto-populate deal value
- Service bisa di-link ke multiple deals

### 4. Client Module (CRM)

**Features:**
- Complete contact information
- Source tracking (marketing analytics)
- Notes field untuk catatan internal
- Deal history per client

**Filter & Search:**
- Filter by source
- Search by name/company/email
- Sort by date, name

### 5. Deal Module (Sales Pipeline)

**Pipeline Stages:**

| Stage | Deskripsi | Probability | Actions |
|-------|-----------|-------------|---------|
| **Lead** | Klien baru inquiry | 10% | Qualify → |
| **Qualified** | Budget & kebutuhan cocok | 25% | Send proposal → |
| **Proposal** | Penawaran dikirim | 50% | Apply discount, negotiate → |
| **Negotiation** | Negosiasi terms | 75% | Finalize → |
| **Won** | Deal closed! 🎉 | 100% | Create invoice, payment tracking |
| **Lost** | Deal gagal | 0% | Record reason, follow-up later |

**Deal Logic:**
- `final_value = deal_value - (deal_value * discount_percent / 100)`
- Discount hanya bisa apply di stage Proposal keatas
- Stage Won/Lost adalah final state
- Track stage changes secara mendetail dengan timestamp di tabel `deal_stage_history`

### 6. Financial Module

**Income Categories:**
- Deal Payment (linked to specific deal)
- Lainnya

**Expense Categories:**
- Operasional
- Marketing
- Tools
- Lainnya

**Payment Tracking:**
```
Deal Created (Pending) → 
Partial Payment → 
Full Payment (Paid) or Cancelled
```

**Reports:**
- Monthly revenue vs expense
- Profit margin
- Outstanding payments
- Payment methods breakdown

### 7. Dashboard Module

**Metrics Calculation:**

```php
// Revenue (current month)
SELECT SUM(amount) FROM transactions 
WHERE type='Income' AND MONTH(transaction_date) = CURRENT_MONTH

// Active Deals
SELECT COUNT(*) FROM deals 
WHERE current_stage NOT IN ('Won', 'Lost')

// Conversion Rate
(Won Deals / Total Deals) * 100

// Outstanding Payments
SELECT SUM(final_value) FROM deals 
WHERE payment_status != 'Paid'
```

### 8. Invoice Module

**Features:**
- Generate invoice untuk deal tertentu / klien tanpa deal
- Automatic invoice number generation
- Flexible invoice line items & taxes
- Status tracking (Draft, Sent, Paid, Overdue)
- Export to PDF untuk client

---

## 🗄 Database Schema

### Entity Relationship

```
┌─────────┐
│  users  │
└────┬────┘
     │ 1:N
     ↓
┌──────────┐       ┌──────────────┐
│businesses│ 1:N   │   services   │
└────┬─────┘←──────┤              │
     │             └──────┬───────┘
     │ 1:N                │
     ↓                    │
┌─────────┐               │
│ clients │               │
└────┬────┘               │
     │ 1:N                │ N:1
     ↓                    ↓
┌────────────┐     ┌──────┴───────┐
│   deals    │────→│    deals     │
└─────┬──────┘     └──────────────┘
      │ 1:N
      ↓
┌───────────────┐
│ deal_payments │
└───────────────┘

┌───────────────┐
│stage_history  │
└───────────────┘

┌──────────────┐       ┌───────────────┐
│   invoices   │──────→│ invoice_items │
└──────────────┘ 1:N   └───────────────┘
  (linked to deals & clients)

┌──────────────┐
│ transactions │ (linked to business & optionally to deal)
└──────────────┘
```

### Key Tables

#### `users`
- Primary authentication entity
- Can own multiple businesses

#### `businesses`
- Multi-tenancy dengan `user_id`
- Semua data scoped by `business_id`
- Primary business flag

#### `services`
- Service packages per business
- Soft delete support
- Active/Inactive status

#### `clients`
- CRM database
- Source tracking
- Linked to deals

#### `deals`
- Core sales pipeline
- Links client + service
- Discount & final value calculation
- Stage progression

#### `deal_payments`
- Payment installments
- Multiple payments per deal
- Payment methods tracking

#### `deal_stage_history`
- Melacak log perpindahan stage sebuah deal

#### `transactions`
- Income & Expense tracking
- Optional link to deals
- Category & method

#### `invoices`
- Invoice details (number, status, due dates, tax)
- Linked to deal and client
- PDF generation ready

#### `invoice_items`
- Rincian item per invoice (deskripsi, quantity, harga)

---

## 💻 Development Notes

### Code Conventions

**Naming:**
- Files: `kebab-case.php`
- Database: `snake_case`
- Functions: `camelCase()`
- Constants: `UPPER_SNAKE_CASE`

**Security:**
- ✅ Always use prepared statements
- ✅ Sanitize input with `htmlspecialchars()` (via `e()` helper)
- ✅ Validate file uploads (type, size)
- ✅ CSRF protection on forms
- ✅ Session-based authentication

**Database:**
- Use transactions untuk operasi yang multi-table
- Index pada foreign keys dan frequent query fields
- Soft delete untuk data yang perlu audit trail

### Helper Functions (`includes/functions.php`)

```php
isLoggedIn()              // Check if user authenticated
getCurrentBusinessId()    // Get active business ID from session
requireLogin()            // Redirect if not authenticated
formatCurrency($amount)   // Format to Rupiah
formatDate($date)         // Format to Indonesian locale
setFlashMessage()         // Set session flash message
getFlashMessage()         // Retrieve & clear flash message
generateCSRFToken()       // Create CSRF token
verifyCSRFToken()         // Validate CSRF token
```

### Session Variables

```php
$_SESSION['user_id']      // Current logged in user
$_SESSION['business_id']  // Current active business
$_SESSION['flash']        // Flash messages
$_SESSION['csrf_token']   // CSRF protection
```

---

## 🚀 Roadmap

### Phase 1 - MVP (Current)
- ✅ Authentication (Login/Register)
- ✅ Business Profile Management
- ✅ Service Package CRUD
- ✅ Client CRM
- ✅ Deal Pipeline (5 stages)
- ✅ Deal Detail & Stage History Track
- ✅ Financial Tracking (Income/Expense)
- ✅ Dashboard Metrics
- ✅ Payment Tracking
- ✅ Invoice Generation & PDF Export
- ✅ Laporan Laba-Rugi & Export CSV

### Phase 2 - Enhancements (Q2 2026)
- [ ] Email Notifications (deal updates, payment reminders)
- [ ] Advanced filters & search
- [ ] Bulk actions
- [x] Data export (CSV/Excel)
- [ ] Activity log & audit trail

### Phase 3 - Advanced Features (Q3 2026)
- [ ] Recurring/Subscription Services
- [ ] Team collaboration (multi-user per business)
- [ ] Role-based access control
- [ ] Client Portal (klien bisa login, lihat invoice, bayar)
- [ ] Payment Gateway Integration (Midtrans, Xendit)
- [ ] WhatsApp Integration
- [ ] Advanced analytics & forecasting

### Phase 4 - Scale (Q4 2026)
- [ ] Mobile App (React Native)
- [ ] API untuk integrasi
- [ ] Automated workflows
- [ ] AI-powered insights
- [ ] Multi-currency support
- [ ] Multi-language (English)

---

## 📝 License & Credits

**IniJasa** © 2026 — Platform Manajemen Bisnis Jasa

Dibuat dengan ❤️ untuk UMKM dan Agensi Jasa Indonesia 🇮🇩

---

## 📞 Support

Untuk pertanyaan development atau issue, silakan hubungi tim development.

---

**Version:** 1.5 MVP  
**Last Updated:** Maret 2026  
**Status:** Active Development
