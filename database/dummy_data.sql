-- ============================================================
-- Jasaku Dummy Data - Simulasi Full Website Flow
-- ============================================================
-- Skenario:
--   User 1 (Faris)   → 2 bisnis: Digital Agency + Konsultan
--   User 2 (Rina)    → 1 bisnis: Kebersihan Rumah
--   User 3 (Dito)    → 1 bisnis: Perbaikan Elektronik
-- Password semua: password
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- ============================================================

USE jasaku_db;

-- ============================================================
-- TRUNCATE (urutan penting karena FK)
-- ============================================================
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE deal_payments;
TRUNCATE TABLE transactions;
TRUNCATE TABLE deals;
TRUNCATE TABLE clients;
TRUNCATE TABLE services;
TRUNCATE TABLE businesses;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- USERS
-- ============================================================
INSERT INTO users (id, full_name, email, password_hash) VALUES
(1, 'Faris Adillah',    'faris@jasaku.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(2, 'Rina Kusuma',      'rina@jasaku.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'Dito Prasetyo',    'dito@jasaku.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================================
-- BUSINESSES
-- ============================================================
INSERT INTO businesses (id, user_id, business_name, category, description, address, phone, email, is_primary) VALUES
-- Faris: bisnis 1 (primary)
(1, 1, 'Nexora Digital Studio',   'Kreatif/Desain',
    'Studio desain digital spesialis branding, web, dan konten visual.',
    'Jl. Raya Darmo No. 12, Surabaya', '081311223344', 'hello@nexoradigital.id', 1),

-- Faris: bisnis 2
(2, 1, 'Faris Consulting',        'Konsultan',
    'Konsultasi bisnis dan transformasi digital untuk UKM.',
    'Jl. Raya Darmo No. 12, Surabaya', '081311223344', 'faris@farisconsulting.id', 0),

-- Rina: bisnis 1 (primary)
(3, 2, 'SparkClean Surabaya',     'Kebersihan',
    'Jasa kebersihan rumah dan kantor profesional, cepat, dan terpercaya.',
    'Jl. Gubeng Kertajaya No. 5, Surabaya', '082233445566', 'info@sparkclean.id', 1),

-- Dito: bisnis 1 (primary)
(4, 3, 'FixIt Elektronik',        'Perbaikan',
    'Servis dan perbaikan elektronik rumah tangga: AC, kulkas, mesin cuci.',
    'Jl. Kenjeran No. 78, Surabaya', '083344556677', 'service@fixitelektronik.id', 1);

-- ============================================================
-- SERVICES
-- ============================================================
INSERT INTO services (id, business_id, service_name, description, price, status) VALUES
-- Nexora Digital Studio
(1,  1, 'Website Company Profile',    'Website 5-10 halaman, responsive, SEO-ready',              12000000.00, 'Active'),
(2,  1, 'Website E-Commerce',         'Toko online lengkap dengan payment gateway',                25000000.00, 'Active'),
(3,  1, 'Logo & Brand Identity',      'Logo + panduan merek (warna, tipografi, aplikasi)',          3500000.00, 'Active'),
(4,  1, 'Social Media Management',    'Kelola konten IG/FB: 20 konten/bulan + laporan',             4500000.00, 'Active'),
(5,  1, 'UI/UX Design',               'Desain antarmuka aplikasi mobile/web (Figma)',               8000000.00, 'Active'),
(6,  1, 'Video Motion Graphic',       'Animasi explainer video 60-90 detik',                        6000000.00, 'Inactive'),

-- Faris Consulting
(7,  2, 'Konsultasi Bisnis 3 Sesi',   'Audit & strategi bisnis, 3x pertemuan 2 jam',               4500000.00, 'Active'),
(8,  2, 'Digital Marketing Strategy', 'Penyusunan roadmap pemasaran digital 6 bulan',               7500000.00, 'Active'),
(9,  2, 'Business Plan Writing',      'Penulisan business plan lengkap untuk investor',              5000000.00, 'Active'),

-- SparkClean
(10, 3, 'Deep Cleaning Rumah',        'Pembersihan menyeluruh unit 1 lantai hingga 100m2',          1500000.00, 'Active'),
(11, 3, 'Regular Cleaning Bulanan',   'Kunjungan 4x/bulan, 3 jam per kunjungan',                   1200000.00, 'Active'),
(12, 3, 'Cleaning Pasca Renovasi',    'Pembersihan debu dan sisa material bangunan',                2000000.00, 'Active'),
(13, 3, 'Office Cleaning Harian',     'Kebersihan kantor setiap hari kerja',                        3500000.00, 'Active'),

-- FixIt Elektronik
(14, 4, 'Service AC (Cuci + Freon)',  'Cuci unit, isi freon, pengecekan komponen',                   350000.00, 'Active'),
(15, 4, 'Service Kulkas',             'Diagnosa, penggantian sparepart, isi freon',                  400000.00, 'Active'),
(16, 4, 'Service Mesin Cuci',         'Perbaikan motor, pompa, atau drum',                           300000.00, 'Active'),
(17, 4, 'Instalasi AC Baru',          'Pemasangan unit AC baru termasuk pipa dan kabel',             600000.00, 'Active');

-- ============================================================
-- CLIENTS
-- ============================================================
INSERT INTO clients (id, business_id, client_name, company, email, phone, address, notes, source) VALUES
-- Nexora Digital Studio clients
(1,  1, 'Bambang Hartono',   'PT. Anugerah Niaga',       'bambang@anugerahniaga.com',   '081200001111', 'Jl. Pemuda No. 30, Surabaya',  'Klien potensial besar, kenal dari pameran',                    'Referral'),
(2,  1, 'Dian Pratiwi',      'CV. Sumber Rejeki',        'dian@sumberrejeki.co.id',     '081200002222', 'Jl. Diponegoro No. 7, Malang', 'Butuh website dan social media sekaligus',                      'Social Media'),
(3,  1, 'Reza Firmansyah',   'Startup Kopi Nusantara',   'reza@kopinusantara.id',       '081200003333', 'Jl. Tunjungan No. 15, Surabaya','Founder muda, anggaran terbatas tapi potensi besar',            'Website'),
(4,  1, 'Mega Lestari',      'Salon Mega Beauty',        'mega@megabeauty.id',          '081200004444', 'Jl. Rungkut Industri No. 4',   'Minta revisi logo lama yang sudah 10 tahun',                    'Direct'),
(5,  1, 'Irfan Maulana',     'PT. Karya Mandiri Teknik', 'irfan@kmteknik.com',          '081200005555', 'Jl. HR Muhammad No. 100',      'Project besar, perlu proposal resmi',                           'Referral'),

-- Faris Consulting clients
(6,  2, 'Hendri Susanto',    'UD. Bintang Jaya',         'hendri@bintangjaya.com',      '081200006666', 'Jl. Wonokromo No. 22, Surabaya','UMKM makanan, mau ekspansi ke online',                          'Direct'),
(7,  2, 'Sri Wahyuni',       'Klinik Sehat Bersama',     'sri@kliniksehat.id',          '081200007777', 'Jl. Dharmawangsa No. 45',      'Klinik mau digitalisasi sistem antrian',                        'Referral'),
(8,  2, 'Taufik Hidayat',    NULL,                       'taufik.h@gmail.com',          '081200008888', NULL,                           'Individu, mau buka usaha baru, butuh business plan',            'Social Media'),

-- SparkClean clients
(9,  3, 'Ibu Santi Dewi',    NULL,                       'santi.dewi@gmail.com',        '081200009999', 'Perum. Citra Land Blok C5',    'Pelanggan tetap sejak 3 bulan lalu',                            'Direct'),
(10, 3, 'Pak Hendra',        'PT. Global Shipping',      'hendra@globalshipping.co.id', '081200010101', 'Jl. Tanjung Perak Barat No. 3','Kantor 2 lantai, butuh cleaning harian',                         'Website'),
(11, 3, 'Ibu Ratna',         'Ruko Ratna Interior',      'ratna@ratnaint.com',           '081200011011', 'Jl. Darmo Permai No. 8',       'Pasca renovasi showroom, butuh sekali cleaning besar',           'Social Media'),
(12, 3, 'Mas Bimo',          NULL,                       'bimo.sby@gmail.com',           '081200012012', 'Jl. Gayungan No. 17',          'Apartemen 2BR, minta deep clean sebelum pindah',                 'Referral'),

-- FixIt Elektronik clients
(13, 4, 'Pak Soewarno',      NULL,                       NULL,                           '081200013013', 'Jl. Ngagel No. 55',            'Pelanggan lama, selalu service AC tiap tahun',                   'Direct'),
(14, 4, 'Bu Endang',         NULL,                       'endang.sry@gmail.com',         '081200014014', 'Komplek Wisma Indah Blok A2',  'Kulkas 2 pintu, kompresor bermasalah',                           'Referral'),
(15, 4, 'Cafe Kopi Tetes',   'Cafe Kopi Tetes',          'admin@kopitetes.id',           '081200015015', 'Jl. Gubeng Masjid No. 33',     'Butuh servis rutin AC 4 unit setiap 3 bulan',                    'Social Media'),
(16, 4, 'Pak Darmawan',      'Kontrakan Darmawan',       NULL,                           '081200016016', 'Jl. Rungkut Asri No. 12',      'Punya 6 unit kontrakan, tiap unit 1 AC',                         'Direct');

-- ============================================================
-- DEALS
-- ============================================================
INSERT INTO deals (id, business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes, closed_at) VALUES

-- ==================== NEXORA DIGITAL STUDIO ====================

-- Won (sudah selesai, bulan lalu)
(1,  1, 4, 3,  'Logo & Rebranding Salon Mega Beauty',
    3500000.00, 0.00,  3500000.00, 'Won',         '2026-02-10', 'Revisi 2x, final approved klien.', '2026-02-10 14:00:00'),

(2,  1, 3, 4,  'Social Media Management - Kopi Nusantara Feb-Apr',
    4500000.00, 10.00, 4050000.00, 'Won',         '2026-02-28', 'Kontrak 3 bulan, DP 50% di awal.',  '2026-02-28 10:00:00'),

-- Won (bulan ini)
(3,  1, 1, 1,  'Website Company Profile PT. Anugerah Niaga',
    12000000.00, 0.00, 12000000.00, 'Won',        '2026-03-05', 'Full payment, website sudah live.', '2026-03-05 16:30:00'),

-- Proposal / Negotiation (sedang berjalan)
(4,  1, 2, 2,  'Website E-Commerce CV. Sumber Rejeki',
    25000000.00, 5.00, 23750000.00, 'Negotiation','2026-04-30', 'Negosiasi scope dan timeline.',     NULL),

(5,  1, 5, 5,  'UI/UX Design App Internal PT. Karya Mandiri',
    8000000.00, 0.00,  8000000.00, 'Proposal',   '2026-04-15', 'Proposal dikirim, menunggu feedback.', NULL),

-- Lead / Qualified
(6,  1, 3, 1,  'Website E-Commerce Kopi Nusantara',
    25000000.00, 15.00,21250000.00, 'Qualified',  '2026-05-30', 'Anggaran terbatas, sedang negosiasi harga.', NULL),

-- Lost
(7,  1, 1, 6,  'Video Motion Graphic Produk Anugerah Niaga',
    6000000.00, 0.00,  6000000.00, 'Lost',        '2026-02-20', 'Klien pilih vendor lain yang lebih murah.', '2026-02-20 09:00:00'),

-- ==================== FARIS CONSULTING ====================

-- Won
(8,  2, 6, 7,  'Konsultasi Bisnis UD. Bintang Jaya',
    4500000.00, 0.00,  4500000.00, 'Won',         '2026-02-20', '3 sesi selesai, klien puas.', '2026-02-20 15:00:00'),

-- Negotiation
(9,  2, 7, 8,  'Digital Marketing Strategy Klinik Sehat',
    7500000.00, 0.00,  7500000.00, 'Negotiation', '2026-04-01', 'Masih mempertimbangkan anggaran.', NULL),

-- Proposal
(10, 2, 8, 9,  'Business Plan Usaha Kuliner Taufik',
    5000000.00, 10.00, 4500000.00, 'Proposal',    '2026-03-30', 'Proposal dikirim via email.', NULL),

-- Lead
(11, 2, 6, 8,  'Digital Strategy UD. Bintang Jaya (Follow-up)',
    7500000.00, 0.00,  7500000.00, 'Lead',        '2026-06-01', 'Tindak lanjut dari konsultasi sebelumnya.', NULL),

-- ==================== SPARKCLEAN ====================

-- Won (bulan lalu)
(12, 3, 11, 12, 'Deep Clean Pasca Renovasi Ruko Ratna',
    2000000.00, 0.00,  2000000.00, 'Won',         '2026-02-25', 'Selesai dalam 1 hari.', '2026-02-25 17:00:00'),

-- Won (berulang - kontrak bulanan)
(13, 3, 9,  11, 'Regular Cleaning Rumah Bu Santi - Maret',
    1200000.00, 0.00,  1200000.00, 'Won',         '2026-03-31', 'Kontrak bulan Maret.', NULL),

(14, 3, 10, 13, 'Office Cleaning Harian PT. Global Shipping',
    3500000.00, 0.00,  3500000.00, 'Won',         '2026-03-31', 'Kontrak bulan Maret, bayar di akhir bulan.', NULL),

-- Proposal
(15, 3, 12, 10, 'Deep Cleaning Apartemen Mas Bimo',
    1500000.00, 0.00,  1500000.00, 'Proposal',    '2026-03-20', 'Survey sudah dilakukan, menunggu konfirmasi.', NULL),

-- Lead
(16, 3, 10, 13, 'Office Cleaning PT. Global Shipping - April',
    3500000.00, 0.00,  3500000.00, 'Lead',        '2026-04-30', 'Perpanjangan kontrak bulan April.', NULL),

-- ==================== FIXIT ELEKTRONIK ====================

-- Won (bulan lalu)
(17, 4, 13, 14, 'Service AC 2 Unit - Pak Soewarno',
    700000.00, 0.00,   700000.00, 'Won',          '2026-02-15', '2 unit AC 1PK, selesai 1 hari.', '2026-02-15 13:00:00'),

(18, 4, 14, 15, 'Service Kulkas Bu Endang',
    400000.00, 0.00,   400000.00, 'Won',          '2026-02-20', 'Ganti kompresor + freon.', '2026-02-20 11:00:00'),

-- Won (bulan ini)
(19, 4, 15, 14, 'Service AC 4 Unit - Cafe Kopi Tetes',
    1400000.00, 0.00, 1400000.00, 'Won',          '2026-03-03', '4 unit cuci + freon, selesai 2 hari.', '2026-03-03 15:00:00'),

(20, 4, 13, 14, 'Service AC 2 Unit - Pak Soewarno (Maret)',
    700000.00, 0.00,   700000.00, 'Won',          '2026-03-06', 'Service berkala 6 bulan sekali.', '2026-03-06 14:00:00'),

-- Proposal
(21, 4, 16, 14, 'Service AC 6 Unit Kontrakan Pak Darmawan',
    2100000.00, 10.00,1890000.00, 'Proposal',     '2026-03-25', 'Diskon borongan 10%.', NULL),

-- Negotiation
(22, 4, 15, 17, 'Instalasi AC Baru Ruang VIP Cafe Kopi Tetes',
    600000.00, 0.00,   600000.00, 'Negotiation',  '2026-03-30', 'Sedang cek ketersediaan unit AC.', NULL);

-- ============================================================
-- DEAL PAYMENTS
-- ============================================================
INSERT INTO deal_payments (deal_id, amount, payment_date, method, notes) VALUES

-- Deal 1: Logo Mega Beauty (full)
(1,  3500000.00, '2026-02-10', 'Transfer', 'Lunas setelah final approval'),

-- Deal 2: Social Media Kopi Nusantara (DP 50% + pelunasan)
(2,  2025000.00, '2026-02-01', 'Transfer', 'DP 50%'),
(2,  2025000.00, '2026-02-28', 'Transfer', 'Pelunasan'),

-- Deal 3: Website Anugerah Niaga (full)
(3, 12000000.00, '2026-03-05', 'Transfer', 'Full payment - website live'),

-- Deal 8: Konsultasi Bintang Jaya (full)
(8,  4500000.00, '2026-02-20', 'Cash',     'Dibayar tunai saat sesi terakhir'),

-- Deal 12: Deep Clean Ruko Ratna (full)
(12, 2000000.00, '2026-02-25', 'QRIS',     'Bayar via QRIS di lokasi'),

-- Deal 13: Regular Cleaning Bu Santi (DP)
(13,  600000.00, '2026-03-01', 'Transfer', 'DP 50%, sisanya akhir bulan'),

-- Deal 14: Office Cleaning Global Shipping (belum dibayar - bayar akhir bulan)

-- Deal 17: Service AC Pak Soewarno Feb (cash)
(17,  700000.00, '2026-02-15', 'Cash',     'Lunas tunai'),

-- Deal 18: Service Kulkas Bu Endang (cash)
(18,  400000.00, '2026-02-20', 'Cash',     'Lunas tunai'),

-- Deal 19: Service AC Cafe Kopi Tetes (transfer)
(19, 1400000.00, '2026-03-03', 'Transfer', 'Transfer setelah selesai'),

-- Deal 20: Service AC Pak Soewarno Maret (cash)
(20,  700000.00, '2026-03-06', 'Cash',     'Lunas tunai');

-- ============================================================
-- TRANSACTIONS
-- ============================================================
INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id) VALUES

-- ==================== NEXORA DIGITAL STUDIO ====================

-- Income dari deal
(1, 'Income', 'Pembayaran Logo Mega Beauty',                   'Deal Payment',  3500000.00, '2026-02-10', 'Transfer', NULL, 1),
(1, 'Income', 'DP Social Media Kopi Nusantara',                'Deal Payment',  2025000.00, '2026-02-01', 'Transfer', NULL, 2),
(1, 'Income', 'Pelunasan Social Media Kopi Nusantara',         'Deal Payment',  2025000.00, '2026-02-28', 'Transfer', NULL, 2),
(1, 'Income', 'Full Payment Website Anugerah Niaga',           'Deal Payment', 12000000.00, '2026-03-05', 'Transfer', NULL, 3),

-- Expense operasional
(1, 'Expense', 'Hosting & Domain Tahunan (Vercel + Namecheap)','Tools',         2400000.00, '2026-01-15', 'Transfer', 'Bayar 1 tahun di muka', NULL),
(1, 'Expense', 'Langganan Figma Professional',                 'Tools',          250000.00, '2026-01-01', 'Transfer', 'Subscription bulanan', NULL),
(1, 'Expense', 'Langganan Figma Professional',                 'Tools',          250000.00, '2026-02-01', 'Transfer', 'Subscription bulanan', NULL),
(1, 'Expense', 'Langganan Figma Professional',                 'Tools',          250000.00, '2026-03-01', 'Transfer', 'Subscription bulanan', NULL),
(1, 'Expense', 'Adobe Creative Cloud',                         'Tools',          750000.00, '2026-01-05', 'Transfer', 'Subscription bulanan', NULL),
(1, 'Expense', 'Adobe Creative Cloud',                         'Tools',          750000.00, '2026-02-05', 'Transfer', 'Subscription bulanan', NULL),
(1, 'Expense', 'Adobe Creative Cloud',                         'Tools',          750000.00, '2026-03-05', 'Transfer', 'Subscription bulanan', NULL),
(1, 'Expense', 'Freelancer Copywriter - Konten Kopi Nusantara','Subcon',         800000.00, '2026-02-05', 'Transfer', 'Fee copywriter lepas', NULL),
(1, 'Expense', 'Instagram Ads - Promosi Studio',               'Marketing',      500000.00, '2026-01-20', 'Transfer', NULL, NULL),
(1, 'Expense', 'Instagram Ads - Promosi Studio',               'Marketing',      500000.00, '2026-02-20', 'Transfer', NULL, NULL),
(1, 'Expense', 'Cetak Proposal & Portfolio',                   'Operasional',    300000.00, '2026-02-10', 'Cash',     NULL, NULL),

-- ==================== FARIS CONSULTING ====================

-- Income dari deal
(2, 'Income', 'Pembayaran Konsultasi Bintang Jaya',            'Deal Payment',  4500000.00, '2026-02-20', 'Cash',     NULL, 8),

-- Expense
(2, 'Expense', 'Buku & Materi Riset',                          'Operasional',    350000.00, '2026-01-10', 'Transfer', NULL, NULL),
(2, 'Expense', 'Zoom Pro Subscription',                        'Tools',          230000.00, '2026-01-01', 'Transfer', NULL, NULL),
(2, 'Expense', 'Zoom Pro Subscription',                        'Tools',          230000.00, '2026-02-01', 'Transfer', NULL, NULL),
(2, 'Expense', 'Zoom Pro Subscription',                        'Tools',          230000.00, '2026-03-01', 'Transfer', NULL, NULL),
(2, 'Expense', 'Transport & Akomodasi Kunjungan Klien',        'Operasional',    200000.00, '2026-02-18', 'Cash',     NULL, NULL),

-- ==================== SPARKCLEAN ====================

-- Income dari deal
(3, 'Income', 'Pembayaran Deep Clean Ruko Ratna',              'Deal Payment',  2000000.00, '2026-02-25', 'QRIS',     NULL, 12),
(3, 'Income', 'DP Regular Cleaning Bu Santi Maret',            'Deal Payment',   600000.00, '2026-03-01', 'Transfer', NULL, 13),

-- Expense
(3, 'Expense', 'Pembelian Bahan Kimia Pembersih',              'Operasional',    850000.00, '2026-01-20', 'Transfer', 'Stok 3 bulan', NULL),
(3, 'Expense', 'Servis Mesin Vacuum Cleaner',                  'Operasional',    300000.00, '2026-02-12', 'Cash',     NULL, NULL),
(3, 'Expense', 'Gaji Karyawan - Februari',                     'Gaji',          3000000.00, '2026-02-28', 'Transfer', '2 karyawan x Rp 1.500.000', NULL),
(3, 'Expense', 'Gaji Karyawan - Maret',                        'Gaji',          3000000.00, '2026-03-01', 'Transfer', '2 karyawan x Rp 1.500.000', NULL),
(3, 'Expense', 'Instagram & TikTok Ads',                       'Marketing',      400000.00, '2026-02-15', 'Transfer', NULL, NULL),
(3, 'Expense', 'Seragam Karyawan Baru',                        'Operasional',    450000.00, '2026-01-25', 'Cash',     NULL, NULL),

-- ==================== FIXIT ELEKTRONIK ====================

-- Income dari deal
(4, 'Income', 'Service AC 2 Unit - Pak Soewarno (Feb)',        'Deal Payment',   700000.00, '2026-02-15', 'Cash',     NULL, 17),
(4, 'Income', 'Service Kulkas Bu Endang',                      'Deal Payment',   400000.00, '2026-02-20', 'Cash',     NULL, 18),
(4, 'Income', 'Service AC 4 Unit - Cafe Kopi Tetes',           'Deal Payment',  1400000.00, '2026-03-03', 'Transfer', NULL, 19),
(4, 'Income', 'Service AC 2 Unit - Pak Soewarno (Mar)',        'Deal Payment',   700000.00, '2026-03-06', 'Cash',     NULL, 20),

-- Expense
(4, 'Expense', 'Beli Freon R32 Stok',                          'Operasional',    600000.00, '2026-01-10', 'Transfer', '5 kg stok freon', NULL),
(4, 'Expense', 'Beli Freon R32 Stok',                          'Operasional',    600000.00, '2026-02-10', 'Transfer', '5 kg restok', NULL),
(4, 'Expense', 'Sparepart Kompresor Kulkas',                    'Operasional',    250000.00, '2026-02-18', 'Cash',     'Untuk job Bu Endang', NULL),
(4, 'Expense', 'BBM Operasional Bulan Januari',                'Operasional',    400000.00, '2026-01-31', 'Cash',     NULL, NULL),
(4, 'Expense', 'BBM Operasional Bulan Februari',               'Operasional',    450000.00, '2026-02-28', 'Cash',     NULL, NULL),
(4, 'Expense', 'Perpanjang Domain Website FixIt',              'Marketing',      150000.00, '2026-01-15', 'Transfer', NULL, NULL),
(4, 'Expense', 'Google Ads - Maret',                           'Marketing',      300000.00, '2026-03-01', 'Transfer', NULL, NULL);

-- ============================================================
-- ============================================================
-- EXTENDED DUMMY DATA - TAMBAHAN
-- ============================================================
-- Skenario tambahan:
--   User 4 (Nadia)   → 1 bisnis: Nadia Beauty Studio
--   + Data historis Q4 2025 (Okt–Des) untuk semua bisnis
--   + Lebih banyak klien, services, dan pipeline deals
-- ============================================================

-- ============================================================
-- NEW USER
-- ============================================================
INSERT INTO users (id, full_name, email, password_hash) VALUES
(4, 'Nadia Safitri', 'nadia@jasaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ============================================================
-- NEW BUSINESS (user 4)
-- ============================================================
INSERT INTO businesses (id, user_id, business_name, category, description, address, phone, email, is_primary) VALUES
(5, 4, 'Nadia Beauty Studio', 'Lainnya',
    'Studio kecantikan spesialis make-up pengantin, pelatihan, dan konsultasi skincare.',
    'Jl. Manyar Kertoarjo No. 20, Surabaya', '085566778899', 'nadia@nadiabeatustudio.id', 1);

-- ============================================================
-- NEW SERVICES
-- ============================================================
INSERT INTO services (id, business_id, service_name, description, price, status) VALUES
-- Nexora Digital Studio (tambahan)
(18, 1, 'SEO Optimization',         'Optimasi mesin pencari: audit, on-page, backlink building',  5000000.00, 'Active'),
(19, 1, 'Foto Produk & Konten',     'Sesi foto produk 20-30 item, editing, siap upload',          3500000.00, 'Active'),
(20, 1, 'Landing Page + Ads Setup', 'Landing page conversion + setup Google/Meta Ads',            7500000.00, 'Active'),

-- SparkClean (tambahan)
(21, 3, 'Cuci Sofa & Karpet',       'Cuci kering sofa (per seat) dan karpet (per m2)',             800000.00, 'Active'),
(22, 3, 'Window Cleaning',          'Pembersihan kaca gedung/rumah, dalam dan luar',               600000.00, 'Active'),

-- FixIt Elektronik (tambahan)
(23, 4, 'Service TV LED',           'Diagnosa dan perbaikan TV LED, garansi 30 hari',              300000.00, 'Active'),
(24, 4, 'Service Water Heater',     'Perbaikan & penggantian elemen pemanas air',                  350000.00, 'Active'),

-- Nadia Beauty Studio
(25, 5, 'Pelatihan Make-Up Dasar',  'Kursus make-up 1 hari (8 jam), termasuk bahan praktik',     1500000.00, 'Active'),
(26, 5, 'Paket Bridal Make-Up',     'Make-up pengantin full day, termasuk touch-up',              3000000.00, 'Active'),
(27, 5, 'Konsultasi Skincare',      'Analisis kulit & rekomendasi rutinitas perawatan (1 jam)',    500000.00, 'Active'),
(28, 5, 'Workshop Beauty Business', 'Workshop 1 hari: cara memulai usaha kecantikan',            2500000.00, 'Active');

-- ============================================================
-- NEW CLIENTS
-- ============================================================
INSERT INTO clients (id, business_id, client_name, company, email, phone, address, notes, source) VALUES
-- Nexora Digital Studio (tambahan)
(17, 1, 'Laila Nurmawati',  'Batik Laila Nusantara',   'laila@batiklailanusantara.com', '081300011001', 'Jl. Ampel No. 15, Surabaya',      'Butuh rebranding total termasuk logo baru',        'Direct'),
(18, 1, 'Arif Wibowo',      'Warung Makan Pak Arif',   'arif.wibowo88@gmail.com',       '081300022002', 'Jl. Kedungdoro No. 3',             'Resto baru, mau bangun presence di IG & TikTok',   'Social Media'),
(19, 1, 'Putri Hapsari',    'Kantor Hukum Hapsari',    'putri@hapsarilaw.com',          '081300033003', 'Jl. Basuki Rahmat No. 40',         'Website formal, tidak mau yang terlalu colourful', 'Referral'),
(20, 1, 'Kevin Tandiono',   'Kevin Fashion Store',     'kevin@kevinfashion.id',         '081300044004', 'Jl. Raya Darmo Permai No. 2',      'E-commerce fashion, target launch Hari Raya',      'Website'),
(21, 1, 'Yenny Widjaja',    'YW Skincare',             'yenny@ywskincare.id',           '081300055005', 'Jl. Mayjen Sungkono No. 7',        'Brand skincare, butuh foto produk profesional',    'Referral'),

-- Faris Consulting (tambahan)
(22, 2, 'Gunawan Chandra',  'CV. Putra Makmur',        'gunawan@putramakmur.co.id',     '081300066006', 'Jl. Manukan Kulon No. 11',         'Usaha distribusi, mau ekspansi ke luar kota',      'Referral'),
(23, 2, 'Lestari Dewi',     NULL,                      'lestari.d@gmail.com',           '081300077007', NULL,                               'Mau buka laundry, butuh business plan lengkap',    'Social Media'),
(24, 2, 'Rahmad Fauzi',     'PT. Mitra Sukses',        'rahmad@mitrasukses.com',        '081300088008', 'Jl. Wiyung No. 5',                 'Perusahaan konstruksi, tertarik digital marketing', 'Direct'),
(25, 2, 'Clara Situmorang', 'Klinik Gigi Clara',       'clara@klinikgigi.id',           '081300099009', 'Jl. Pucang Anom No. 12',           'Klinik gigi baru, butuh konsultasi strategi',      'Website'),

-- SparkClean (tambahan)
(26, 3, 'Pak Wahyu',        NULL,                      NULL,                            '082200010001', 'Jl. Lontar No. 44, Surabaya',      'Rumah 2 lantai, panggil berkala tiap 2 bulan',     'Direct'),
(27, 3, 'CV. Kreasi Muda',  'CV. Kreasi Muda',         'admin@kreasimuda.com',          '082200020002', 'Jl. HR Muhammad No. 55',           'Kantor startup 30 orang, butuh cleaning mingguan', 'Social Media'),
(28, 3, 'Bu Wulandari',     NULL,                      'wulan.sby@gmail.com',           '082200030003', 'Puri Indah Blok E7',               'Pasca renovasi dapur + kamar mandi',               'Referral'),
(29, 3, 'Pak Eko',          NULL,                      NULL,                            '082200040004', 'Jl. Nginden Semolo No. 9',         'Punya kos 10 kamar, minta penawaran rutin',        'Direct'),

-- FixIt Elektronik (tambahan)
(30, 4, 'Bu Harni',         NULL,                      'harni.sby@gmail.com',           '083300010001', 'Jl. Pacar Keling No. 5',           'AC 2 unit butuh service rutin',                    'Direct'),
(31, 4, 'Pak Slamet',       NULL,                      NULL,                            '083300020002', 'Jl. Sidotopo No. 17',              'Kulkas 2 pintu tidak dingin',                      'Referral'),
(32, 4, 'Bu Yani',          NULL,                      'yani.home@gmail.com',           '083300030003', 'Komplek Puri Widya Blok B3',       'TV LED 43 inch tidak bisa nyala',                  'Social Media'),
(33, 4, 'Pak Sugeng',       NULL,                      NULL,                            '083300040004', 'Jl. Karang Asem No. 22',           'Mesin cuci front loading error kode E3',           'Direct'),

-- Nadia Beauty Studio
(34, 5, 'Anggi Permatasari',NULL,                      'anggi.bride@gmail.com',         '087700010001', 'Jl. Jagir Sidorejo No. 8',         'Pengantin Oktober 2025, paket full day',           'Social Media'),
(35, 5, 'Kom. Kartini Sby', 'Komunitas Kartini SBY',   'kartini.sby@gmail.com',         '087700020002', NULL,                               'Komunitas 15 orang, minta workshop make-up',       'Social Media'),
(36, 5, 'Bella Anggraini',  NULL,                      'bella.bridal@gmail.com',        '087700030003', 'Jl. Manyar Sabrangan No. 3',       'Pengantin Maret 2026, budget fleksibel',           'Referral'),
(37, 5, 'Ika Sundari',      NULL,                      'ika.sndari@gmail.com',          '087700040004', 'Komplek Graha Famili C12',         'Kulit sensitif, minta konsultasi rutin',           'Social Media'),
(38, 5, 'Reni Agustina',    'Salon Reni',              'reni@salonreni.id',             '087700050005', 'Jl. Kertajaya No. 78',             'Mau latih 2 karyawannya, tertarik pelatihan',     'Direct');

-- ============================================================
-- HISTORICAL DEALS Q4 2025 (IDs 23–37, semua Won)
-- ============================================================
INSERT INTO deals (id, business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes, closed_at) VALUES

-- NEXORA Q4 2025
(23, 1, 17, 3,  'Logo & Rebranding Batik Laila Nusantara',
    3500000.00,  0.00,  3500000.00, 'Won', '2025-10-20', 'Revisi 3x, klien puas.',                       '2025-10-20 15:00:00'),
(24, 1, 18, 4,  'Social Media Management Warung Pak Arif',
    4500000.00, 10.00,  4050000.00, 'Won', '2025-11-30', 'Kontrak 3 bulan IG + FB.',                     '2025-11-01 09:00:00'),
(25, 1, 19, 1,  'Website Company Profile Kantor Hukum Hapsari',
   12000000.00,  0.00, 12000000.00, 'Won', '2025-12-10', 'Website formal, DP 50% + pelunasan.',          '2025-12-10 14:00:00'),
(26, 1, 20, 2,  'Website E-Commerce Kevin Fashion Store',
   25000000.00,  5.00, 23750000.00, 'Won', '2025-12-20', 'DP 50% + pelunasan saat website live.',        '2025-12-20 11:00:00'),

-- FARIS CONSULTING Q4 2025
(27, 2, 22, 7,  'Konsultasi Bisnis CV. Putra Makmur',
    4500000.00,  0.00,  4500000.00, 'Won', '2025-10-30', '3 sesi selesai, rekomendasi ekspansi Jatim.',  '2025-10-30 16:00:00'),
(28, 2, 23, 9,  'Business Plan Usaha Laundry Lestari',
    5000000.00, 10.00,  4500000.00, 'Won', '2025-11-25', 'Business plan 30 halaman, sudah diserahkan.',  '2025-11-25 13:00:00'),

-- SPARKCLEAN Q4 2025
(29, 3, 26, 10, 'Deep Cleaning Rumah Pak Wahyu',
    1500000.00,  0.00,  1500000.00, 'Won', '2025-10-12', 'Rumah 2 lantai, 6 jam selesai.',               '2025-10-12 17:00:00'),
(30, 3, 27, 13, 'Office Cleaning CV. Kreasi Muda - November',
    3500000.00,  0.00,  3500000.00, 'Won', '2025-11-30', 'Kontrak 1 bulan harian.',                      '2025-11-30 18:00:00'),
(31, 3, 26, 11, 'Regular Cleaning Pak Wahyu - Desember',
    1200000.00,  0.00,  1200000.00, 'Won', '2025-12-31', 'Kontrak bulanan Desember.',                    '2025-12-31 17:00:00'),

-- FIXIT Q4 2025
(32, 4, 30, 14, 'Service AC 2 Unit - Bu Harni',
     700000.00,  0.00,   700000.00, 'Won', '2025-10-08', '2 unit 1PK cuci + freon.',                     '2025-10-08 13:00:00'),
(33, 4, 31, 15, 'Service Kulkas - Pak Slamet',
     400000.00,  0.00,   400000.00, 'Won', '2025-11-14', 'Ganti termostat + freon.',                     '2025-11-14 12:00:00'),
(34, 4, 32, 23, 'Service TV LED - Bu Yani',
     300000.00,  0.00,   300000.00, 'Won', '2025-11-28', 'Ganti kapasitor main board.',                  '2025-11-28 15:00:00'),
(35, 4, 33, 24, 'Service Water Heater - Pak Sugeng',
     350000.00,  0.00,   350000.00, 'Won', '2025-12-05', 'Ganti elemen pemanas.',                        '2025-12-05 11:00:00'),

-- NADIA BEAUTY Q4 2025
(36, 5, 34, 26, 'Bridal Make-Up - Anggi Permatasari',
    3000000.00,  0.00,  3000000.00, 'Won', '2025-10-18', 'Wedding di Gedung Graha Pena.',                '2025-10-18 07:00:00'),
(37, 5, 35, 28, 'Workshop Beauty Business - Kom. Kartini',
    5000000.00,  0.00,  5000000.00, 'Won', '2025-12-14', 'Workshop 15 peserta, 1 hari penuh.',           '2025-12-14 17:00:00');

-- ============================================================
-- NEW PIPELINE DEALS 2026 (IDs 38–50)
-- ============================================================
INSERT INTO deals (id, business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes, closed_at) VALUES

-- NEXORA pipeline 2026
(38, 1, 17, 18, 'SEO Optimization Batik Laila Nusantara',
    5000000.00, 0.00, 5000000.00, 'Qualified',  '2026-04-30', 'Follow-up dari project logo sebelumnya.',   NULL),
(39, 1, 21, 19, 'Foto Produk YW Skincare',
    3500000.00, 0.00, 3500000.00, 'Lead',        '2026-04-15', 'Masih survey vendor lain.',                NULL),
(40, 1, 18, 20, 'Landing Page + Ads Setup Warung Pak Arif',
    7500000.00, 0.00, 7500000.00, 'Negotiation', '2026-04-10', 'Klien tawar 6 juta, sedang negosiasi.',   NULL),

-- FARIS CONSULTING pipeline 2026
(41, 2, 24, 8,  'Digital Marketing Strategy PT. Mitra Sukses',
    7500000.00, 0.00, 7500000.00, 'Proposal',    '2026-04-20', 'Proposal 12 halaman sudah dikirim.',       NULL),
(42, 2, 25, 7,  'Konsultasi Bisnis Klinik Gigi Clara',
    4500000.00, 0.00, 4500000.00, 'Lead',        '2026-05-01', 'Prospek dari Instagram, masih cold lead.', NULL),

-- SPARKCLEAN pipeline 2026
(43, 3, 29, 11, 'Regular Cleaning Kos Pak Eko',
    1200000.00, 0.00, 1200000.00, 'Negotiation', '2026-03-25', 'Minta diskon untuk kontrak 6 bulan.',      NULL),
(44, 3, 12, 21, 'Cuci Sofa & Karpet - Mas Bimo',
     800000.00, 0.00,  800000.00, 'Proposal',    '2026-03-22', 'Add-on dari rencana deep cleaning.',       NULL),
(45, 3, 10, 22, 'Window Cleaning PT. Global Shipping',
     600000.00, 0.00,  600000.00, 'Lead',        '2026-04-15', 'Diusulkan saat kunjungan bulanan.',        NULL),

-- FIXIT pipeline 2026
(46, 4, 13, 16, 'Service Mesin Cuci - Pak Soewarno',
     300000.00, 0.00,  300000.00, 'Proposal',    '2026-03-20', 'Mesin cuci error, diagnosa dulu.',         NULL),
(47, 4, 30, 17, 'Instalasi 2 AC Baru - Bu Harni',
    1200000.00, 0.00, 1200000.00, 'Qualified',   '2026-04-05', 'Tambah 2 unit AC kamar anak.',             NULL),

-- NADIA BEAUTY 2026
(48, 5, 36, 26, 'Bridal Make-Up - Bella Anggraini',
    3500000.00, 0.00, 3500000.00, 'Won',         '2026-03-04', 'Wedding 4 Maret 2026 di Surabaya.',        '2026-03-04 11:00:00'),
(49, 5, 37, 27, 'Konsultasi Skincare - Ika Sundari',
     500000.00, 0.00,  500000.00, 'Proposal',    '2026-03-25', 'Minta jadwal konsultasi akhir Maret.',     NULL),
(50, 5, 38, 25, 'Pelatihan Make-Up - Salon Reni (2 orang)',
    3000000.00, 0.00, 3000000.00, 'Negotiation', '2026-04-10', 'Minta pelatihan untuk 2 karyawannya.',     NULL);

-- ============================================================
-- DEAL PAYMENTS (historical Q4 2025 + deal 48)
-- ============================================================
INSERT INTO deal_payments (deal_id, amount, payment_date, method, notes) VALUES

-- Nexora Q4 2025
(23, 3500000.00, '2025-10-20', 'Transfer', 'Lunas'),
(24, 4050000.00, '2025-11-01', 'Transfer', 'Lunas di awal kontrak'),
(25, 6000000.00, '2025-11-20', 'Transfer', 'DP 50%'),
(25, 6000000.00, '2025-12-10', 'Transfer', 'Pelunasan'),
(26,11875000.00, '2025-11-25', 'Transfer', 'DP 50%'),
(26,11875000.00, '2025-12-20', 'Transfer', 'Pelunasan'),

-- Faris Consulting Q4 2025
(27, 4500000.00, '2025-10-30', 'Cash',     'Lunas sesi terakhir'),
(28, 4500000.00, '2025-11-25', 'Transfer', 'Lunas setelah dokumen selesai'),

-- SparkClean Q4 2025
(29, 1500000.00, '2025-10-12', 'QRIS',     'Lunas di lokasi'),
(30, 3500000.00, '2025-11-30', 'Transfer', 'Bayar akhir bulan'),
(31, 1200000.00, '2025-12-31', 'Transfer', 'Bayar akhir bulan'),

-- FixIt Q4 2025
(32,  700000.00, '2025-10-08', 'Cash',     'Lunas tunai'),
(33,  400000.00, '2025-11-14', 'Cash',     'Lunas tunai'),
(34,  300000.00, '2025-11-28', 'Cash',     'Lunas tunai'),
(35,  350000.00, '2025-12-05', 'Cash',     'Lunas tunai'),

-- Nadia Beauty Q4 2025
(36, 1500000.00, '2025-10-10', 'Transfer', 'DP 50%'),
(36, 1500000.00, '2025-10-18', 'Transfer', 'Pelunasan hari H'),
(37, 5000000.00, '2025-12-14', 'Transfer', 'Lunas setelah event'),

-- Nadia Beauty Maret 2026
(48, 3500000.00, '2026-03-04', 'Transfer', 'Lunas hari H');

-- ============================================================
-- HISTORICAL TRANSACTIONS Q4 2025
-- ============================================================
INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id) VALUES

-- ==================== NEXORA Q4 2025 ====================
(1, 'Income', 'Pembayaran Logo Batik Laila Nusantara',           'Deal Payment',  3500000.00, '2025-10-20', 'Transfer', NULL, 23),
(1, 'Income', 'Pembayaran Social Media Warung Pak Arif',         'Deal Payment',  4050000.00, '2025-11-01', 'Transfer', NULL, 24),
(1, 'Income', 'DP Website Kantor Hukum Hapsari',                 'Deal Payment',  6000000.00, '2025-11-20', 'Transfer', NULL, 25),
(1, 'Income', 'Pelunasan Website Kantor Hukum Hapsari',          'Deal Payment',  6000000.00, '2025-12-10', 'Transfer', NULL, 25),
(1, 'Income', 'DP Website E-Commerce Kevin Fashion',             'Deal Payment', 11875000.00, '2025-11-25', 'Transfer', NULL, 26),
(1, 'Income', 'Pelunasan Website E-Commerce Kevin Fashion',      'Deal Payment', 11875000.00, '2025-12-20', 'Transfer', NULL, 26),

(1, 'Expense', 'Figma Professional - Oktober',                   'Tools',          250000.00, '2025-10-01', 'Transfer', NULL, NULL),
(1, 'Expense', 'Figma Professional - November',                  'Tools',          250000.00, '2025-11-01', 'Transfer', NULL, NULL),
(1, 'Expense', 'Figma Professional - Desember',                  'Tools',          250000.00, '2025-12-01', 'Transfer', NULL, NULL),
(1, 'Expense', 'Adobe Creative Cloud - Oktober',                 'Tools',          750000.00, '2025-10-05', 'Transfer', NULL, NULL),
(1, 'Expense', 'Adobe Creative Cloud - November',                'Tools',          750000.00, '2025-11-05', 'Transfer', NULL, NULL),
(1, 'Expense', 'Adobe Creative Cloud - Desember',                'Tools',          750000.00, '2025-12-05', 'Transfer', NULL, NULL),
(1, 'Expense', 'Freelancer Copywriter - Konten Warung Pak Arif', 'Subcon',         600000.00, '2025-11-05', 'Transfer', 'Fee content writer lepas', NULL),
(1, 'Expense', 'Instagram Ads - Oktober',                        'Marketing',      500000.00, '2025-10-20', 'Transfer', NULL, NULL),
(1, 'Expense', 'Instagram Ads - November',                       'Marketing',      500000.00, '2025-11-20', 'Transfer', NULL, NULL),
(1, 'Expense', 'Instagram Ads - Desember',                       'Marketing',      500000.00, '2025-12-20', 'Transfer', NULL, NULL),
(1, 'Expense', 'Domain & SSL Kevin Fashion Store',               'Tools',          300000.00, '2025-12-15', 'Transfer', 'Untuk project e-commerce', NULL),

-- ==================== FARIS CONSULTING Q4 2025 ====================
(2, 'Income', 'Pembayaran Konsultasi CV. Putra Makmur',          'Deal Payment',  4500000.00, '2025-10-30', 'Cash',     NULL, 27),
(2, 'Income', 'Pembayaran Business Plan Lestari Dewi',           'Deal Payment',  4500000.00, '2025-11-25', 'Transfer', NULL, 28),

(2, 'Expense', 'Zoom Pro - Oktober',                             'Tools',          230000.00, '2025-10-01', 'Transfer', NULL, NULL),
(2, 'Expense', 'Zoom Pro - November',                            'Tools',          230000.00, '2025-11-01', 'Transfer', NULL, NULL),
(2, 'Expense', 'Zoom Pro - Desember',                            'Tools',          230000.00, '2025-12-01', 'Transfer', NULL, NULL),
(2, 'Expense', 'Transport Kunjungan Klien Oktober',              'Operasional',    150000.00, '2025-10-28', 'Cash',     NULL, NULL),
(2, 'Expense', 'Buku Referensi Bisnis & Marketing',              'Operasional',    280000.00, '2025-11-10', 'Transfer', NULL, NULL),

-- ==================== SPARKCLEAN Q4 2025 ====================
(3, 'Income', 'Pembayaran Deep Clean Pak Wahyu',                 'Deal Payment',  1500000.00, '2025-10-12', 'QRIS',     NULL, 29),
(3, 'Income', 'Pembayaran Office Cleaning CV. Kreasi Muda',      'Deal Payment',  3500000.00, '2025-11-30', 'Transfer', NULL, 30),
(3, 'Income', 'Pembayaran Regular Cleaning Pak Wahyu Des',       'Deal Payment',  1200000.00, '2025-12-31', 'Transfer', NULL, 31),

(3, 'Expense', 'Gaji Karyawan - Oktober',                        'Gaji',          3000000.00, '2025-10-31', 'Transfer', '2 karyawan', NULL),
(3, 'Expense', 'Gaji Karyawan - November',                       'Gaji',          3000000.00, '2025-11-30', 'Transfer', '2 karyawan', NULL),
(3, 'Expense', 'Gaji Karyawan - Desember',                       'Gaji',          3200000.00, '2025-12-31', 'Transfer', '2 karyawan + bonus akhir tahun', NULL),
(3, 'Expense', 'Pembelian Bahan Kimia Pembersih - Oktober',      'Operasional',    700000.00, '2025-10-05', 'Transfer', NULL, NULL),
(3, 'Expense', 'Pembelian Bahan Kimia Pembersih - Desember',     'Operasional',    700000.00, '2025-12-05', 'Transfer', NULL, NULL),
(3, 'Expense', 'Perbaikan Mesin Polisher',                       'Operasional',    500000.00, '2025-11-20', 'Cash',     NULL, NULL),
(3, 'Expense', 'Tas & Perlengkapan Kerja Karyawan',              'Operasional',    350000.00, '2025-10-15', 'Cash',     NULL, NULL),

-- ==================== FIXIT Q4 2025 ====================
(4, 'Income', 'Service AC 2 Unit Bu Harni',                      'Deal Payment',   700000.00, '2025-10-08', 'Cash',     NULL, 32),
(4, 'Income', 'Service Kulkas Pak Slamet',                       'Deal Payment',   400000.00, '2025-11-14', 'Cash',     NULL, 33),
(4, 'Income', 'Service TV LED Bu Yani',                          'Deal Payment',   300000.00, '2025-11-28', 'Cash',     NULL, 34),
(4, 'Income', 'Service Water Heater Pak Sugeng',                 'Deal Payment',   350000.00, '2025-12-05', 'Cash',     NULL, 35),

(4, 'Expense', 'Beli Freon R32 Stok - Oktober',                  'Operasional',    600000.00, '2025-10-01', 'Transfer', '5 kg', NULL),
(4, 'Expense', 'Beli Freon R32 Stok - Desember',                 'Operasional',    600000.00, '2025-12-01', 'Transfer', '5 kg restok', NULL),
(4, 'Expense', 'Sparepart Kapasitor & IC Board TV',              'Operasional',     85000.00, '2025-11-26', 'Cash',     'Untuk job Bu Yani', NULL),
(4, 'Expense', 'Sparepart Elemen Water Heater',                  'Operasional',     95000.00, '2025-12-03', 'Cash',     'Untuk job Pak Sugeng', NULL),
(4, 'Expense', 'BBM Operasional - Oktober',                      'Operasional',    400000.00, '2025-10-31', 'Cash',     NULL, NULL),
(4, 'Expense', 'BBM Operasional - November',                     'Operasional',    420000.00, '2025-11-30', 'Cash',     NULL, NULL),
(4, 'Expense', 'BBM Operasional - Desember',                     'Operasional',    400000.00, '2025-12-31', 'Cash',     NULL, NULL),

-- ==================== NADIA BEAUTY Q4 2025 ====================
(5, 'Income', 'DP Bridal Make-Up Anggi Permatasari',             'Deal Payment',  1500000.00, '2025-10-10', 'Transfer', NULL, 36),
(5, 'Income', 'Pelunasan Bridal Make-Up Anggi Permatasari',      'Deal Payment',  1500000.00, '2025-10-18', 'Transfer', NULL, 36),
(5, 'Income', 'Pembayaran Workshop Komunitas Kartini',           'Deal Payment',  5000000.00, '2025-12-14', 'Transfer', NULL, 37),

(5, 'Expense', 'Pembelian Makeup & Beauty Tools',                'Operasional',   2500000.00, '2025-10-01', 'Transfer', 'Brush, palet, tools set baru', NULL),
(5, 'Expense', 'Sewa Aula Workshop - Desember',                  'Operasional',    500000.00, '2025-12-10', 'Transfer', 'Tempat workshop Komunitas Kartini', NULL),
(5, 'Expense', 'Instagram Ads Nadia Beauty - Oktober',           'Marketing',      300000.00, '2025-10-15', 'Transfer', NULL, NULL),
(5, 'Expense', 'Instagram Ads Nadia Beauty - November',          'Marketing',      300000.00, '2025-11-15', 'Transfer', NULL, NULL),
(5, 'Expense', 'Instagram Ads Nadia Beauty - Desember',          'Marketing',      300000.00, '2025-12-15', 'Transfer', NULL, NULL),
(5, 'Expense', 'Foto Portfolio untuk IG & Website',              'Marketing',      750000.00, '2025-11-05', 'Transfer', 'Foto hasil kerja untuk konten promosi', NULL),

-- ==================== NADIA BEAUTY Q1 2026 ====================
(5, 'Income', 'Pelunasan Bridal Make-Up Bella Anggraini',        'Deal Payment',  3500000.00, '2026-03-04', 'Transfer', NULL, 48),

(5, 'Expense', 'Restok Produk Make-Up - Januari',                'Operasional',    800000.00, '2026-01-10', 'Transfer', NULL, NULL),
(5, 'Expense', 'Restok Produk Make-Up - Februari',               'Operasional',    600000.00, '2026-02-10', 'Transfer', NULL, NULL),
(5, 'Expense', 'Instagram Ads Nadia Beauty - Januari',           'Marketing',      300000.00, '2026-01-15', 'Transfer', NULL, NULL),
(5, 'Expense', 'Instagram Ads Nadia Beauty - Februari',          'Marketing',      300000.00, '2026-02-15', 'Transfer', NULL, NULL),
(5, 'Expense', 'Instagram Ads Nadia Beauty - Maret',             'Marketing',      300000.00, '2026-03-01', 'Transfer', NULL, NULL),
(5, 'Expense', 'Sewa Studio Foto Update Portfolio',              'Marketing',      400000.00, '2026-02-20', 'Transfer', 'Update konten IG Q1 2026', NULL);
