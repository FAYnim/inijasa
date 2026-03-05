-- Jasaku Database Schema with Rich Sample Data
-- Platform Manajemen Bisnis Jasa
-- This file contains extensive dummy data for testing and demonstration

-- Create database
CREATE DATABASE IF NOT EXISTS jasaku_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jasaku_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Businesses table
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    is_deleted BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_business_id (business_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Clients table
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_business_id (business_id),
    INDEX idx_client_name (client_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Deals table
CREATE TABLE deals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    client_id INT NOT NULL,
    service_id INT,
    deal_title VARCHAR(150) NOT NULL,
    deal_value DECIMAL(15,2) NOT NULL,
    discount_percent DECIMAL(5,2) DEFAULT 0,
    final_value DECIMAL(15,2) NOT NULL,
    current_stage ENUM('Lead', 'Qualified', 'Proposal', 'Negotiation', 'Won', 'Lost') DEFAULT 'Lead',
    expected_close_date DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_client_id (client_id),
    INDEX idx_current_stage (current_stage),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Deal payments table
CREATE TABLE deal_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_date DATE NOT NULL,
    method ENUM('Transfer', 'Cash', 'QRIS', 'Lainnya'),
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE CASCADE,
    INDEX idx_deal_id (deal_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transactions table (Income & Expense)
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE SET NULL,
    INDEX idx_business_id (business_id),
    INDEX idx_type (type),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_deal_id (deal_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- RICH SAMPLE DATA FOR TESTING
-- ========================================

-- Sample users (password for all: "password")
INSERT INTO users (full_name, email, password_hash) VALUES
('Admin User', 'admin@jasaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Budi Prasetyo', 'budi@jasaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Siti Nurhaliza', 'siti@jasaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample businesses
INSERT INTO businesses (user_id, business_name, category, description, address, phone, email, is_primary) VALUES
(1, 'Jasaku Digital Agency', 'Kreatif/Desain', 'Agensi digital kreatif yang menyediakan solusi lengkap untuk kebutuhan digital marketing, web development, dan branding bisnis Anda', 'Jl. Sudirman No. 123, Jakarta Selatan', '081234567890', 'info@jasakudigital.com', 1),
(2, 'Konsultan Bisnis Prima', 'Konsultan', 'Menyediakan jasa konsultasi bisnis, manajemen, dan pengembangan usaha untuk UMKM dan perusahaan', 'Jl. Gatot Subroto No. 45, Jakarta Pusat', '081234567891', 'contact@bisnispri.com', 1),
(3, 'CleanPro Services', 'Kebersihan', 'Jasa kebersihan profesional untuk kantor, rumah, dan gedung komersial', 'Jl. HR Rasuna Said No. 78, Jakarta Selatan', '081234567892', 'info@cleanpro.com', 1);

-- Sample services for Business 1 (Jasaku Digital Agency)
INSERT INTO services (business_id, service_name, description, price, status, created_at) VALUES
(1, 'Website Development Professional', 'Pembuatan website profesional dengan desain modern, responsive, SEO-friendly, include domain dan hosting 1 tahun', 15000000.00, 'Active', '2025-12-01 10:00:00'),
(1, 'Website Development Basic', 'Pembuatan website sederhana untuk company profile atau landing page, responsive dan mobile-friendly', 8000000.00, 'Active', '2025-12-01 10:15:00'),
(1, 'Social Media Management Premium', 'Pengelolaan 5 platform media sosial, konten harian, analisis performa, dan strategi engagement (6 bulan)', 12000000.00, 'Active', '2025-12-01 10:30:00'),
(1, 'Social Media Management Basic', 'Pengelolaan 3 platform media sosial dengan 15 konten per bulan (3 bulan)', 5000000.00, 'Active', '2025-12-01 10:45:00'),
(1, 'Logo & Brand Identity Premium', 'Desain logo, brand guideline lengkap, stationery design (business card, letterhead, envelope)', 5000000.00, 'Active', '2025-12-01 11:00:00'),
(1, 'Logo Design Basic', 'Desain logo unik dan memorable dengan 3 konsep desain, 2x revisi, file format lengkap', 2500000.00, 'Active', '2025-12-01 11:15:00'),
(1, 'SEO Optimization Package', 'Optimasi SEO website untuk 20 keyword utama, include technical SEO, on-page, dan off-page (3 bulan)', 10000000.00, 'Active', '2025-12-15 09:00:00'),
(1, 'Google Ads Campaign Management', 'Setup dan pengelolaan kampanye Google Ads, termasuk research keyword, copywriting, dan optimasi (1 bulan)', 7500000.00, 'Active', '2025-12-20 14:00:00'),
(1, 'Content Writing Package', 'Pembuatan konten artikel SEO-friendly 10 artikel @1000 kata untuk blog atau website', 3000000.00, 'Active', '2026-01-05 11:00:00'),
(1, 'E-commerce Development', 'Pembuatan toko online lengkap dengan payment gateway, inventory management, dan shipping integration', 25000000.00, 'Active', '2026-01-10 10:00:00'),
(1, 'Mobile App Development', 'Pengembangan aplikasi mobile Android & iOS dengan fitur custom sesuai kebutuhan bisnis', 45000000.00, 'Inactive', '2026-01-15 09:00:00'),
(1, 'Video Production Services', 'Produksi video promosi profesional durasi 2-3 menit, include konsep, shooting, dan editing', 8500000.00, 'Active', '2026-02-01 13:00:00');

-- Sample services for Business 2 (Konsultan Bisnis Prima)
INSERT INTO services (business_id, service_name, description, price, status, created_at) VALUES
(2, 'Konsultasi Strategi Bisnis', 'Konsultasi mendalam untuk pengembangan strategi bisnis jangka panjang, analisis kompetitor, dan roadmap implementasi', 15000000.00, 'Active', '2025-12-01 08:00:00'),
(2, 'Business Plan Development', 'Penyusunan business plan lengkap untuk proposal investor atau ekspansi bisnis', 10000000.00, 'Active', '2025-12-05 09:00:00'),
(2, 'Financial Analysis & Planning', 'Analisis keuangan bisnis dan perencanaan finansial untuk optimasi profit', 8000000.00, 'Active', '2025-12-10 10:00:00'),
(2, 'HR Management Consulting', 'Konsultasi sistem manajemen SDM, recruitment strategy, dan employee development', 12000000.00, 'Active', '2026-01-01 11:00:00');

-- Sample services for Business 3 (CleanPro Services)
INSERT INTO services (business_id, service_name, description, price, status, created_at) VALUES
(3, 'Office Cleaning Monthly', 'Layanan kebersihan kantor rutin bulanan untuk area hingga 500m2', 5000000.00, 'Active', '2025-12-01 07:00:00'),
(3, 'Deep Cleaning Service', 'Pembersihan menyeluruh termasuk karpet, sofa, dan area sulit dijangkau', 3500000.00, 'Active', '2025-12-05 08:00:00'),
(3, 'Post-Construction Cleaning', 'Pembersihan pasca renovasi atau konstruksi bangunan', 8000000.00, 'Active', '2026-01-01 09:00:00');

-- Sample clients for Business 1 (Jasaku Digital Agency) - 25 clients
INSERT INTO clients (business_id, client_name, company, email, phone, address, source, notes, created_at) VALUES
(1, 'Budi Santoso', 'PT. Maju Bersama Teknologi', 'budi@majubersama.com', '081234567801', 'Jl. Thamrin No. 45, Jakarta Pusat', 'Referral', 'Klien potensial untuk website development', '2025-11-01 09:00:00'),
(1, 'Siti Aminah', 'CV. Sukses Mandiri Indonesia', 'siti@suksesmandiri.com', '081234567802', 'Jl. Kemang Raya No. 88, Jakarta Selatan', 'Social Media', 'Tertarik dengan social media management', '2025-11-05 10:30:00'),
(1, 'Ahmad Yani', 'Toko Elektronik Jaya', 'ahmad@elektronikjaya.com', '081234567803', 'Jl. Mangga Besar No. 123, Jakarta Barat', 'Direct', 'Butuh logo dan branding', '2025-11-10 14:15:00'),
(1, 'Dewi Lestari', 'Butik Fashion Dewi', 'dewi@butikfashion.com', '081234567804', 'Jl. Senopati No. 67, Jakarta Selatan', 'Website', 'Ingin membuat e-commerce', '2025-11-15 11:20:00'),
(1, 'Rizki Pratama', 'PT. Digital Nusantara', 'rizki@digitalnusantara.co.id', '081234567805', 'Jl. Sudirman Kav. 52, Jakarta Selatan', 'Referral', 'Referral dari Budi Santoso', '2025-11-20 15:45:00'),
(1, 'Fitri Handayani', 'Klinik Sehat Sentosa', 'fitri@kliniksehat.com', '081234567806', 'Jl. Cikini Raya No. 34, Jakarta Pusat', 'Social Media', 'Butuh website untuk klinik', '2025-11-25 09:30:00'),
(1, 'Andi Wijaya', 'Restoran Nusantara', 'andi@restoranusantara.com', '081234567807', 'Jl. Panjang No. 123, Jakarta Barat', 'Direct', 'Ingin promosi di social media', '2025-12-01 13:00:00'),
(1, 'Linda Sari', 'Salon Cantik Linda', 'linda@saloncantik.com', '081234567808', 'Jl. Radio Dalam No. 45, Jakarta Selatan', 'Website', 'Butuh branding dan social media', '2025-12-05 10:15:00'),
(1, 'Hendra Gunawan', 'PT. Konstruksi Megah', 'hendra@konstruksimegah.co.id', '081234567809', 'Jl. MT Haryono No. 88, Jakarta Timur', 'Referral', 'Klien korporat besar', '2025-12-10 14:30:00'),
(1, 'Yuni Astuti', 'Toko Kue Manis Yuni', 'yuni@tokokuemanis.com', '081234567810', 'Jl. Cibubur No. 56, Jakarta Timur', 'Social Media', 'Usaha kecil butuh branding', '2025-12-15 11:45:00'),
(1, 'Bambang Suryanto', 'CV. Import Eksport Global', 'bambang@importglobal.com', '081234567811', 'Jl. Pluit Raya No. 234, Jakarta Utara', 'Website', 'Butuh company profile website', '2025-12-20 09:20:00'),
(1, 'Rina Marlina', 'Sekolah Cerdas Indonesia', 'rina@sekolahcerdas.sch.id', '081234567812', 'Jl. Cipete Raya No. 78, Jakarta Selatan', 'Direct', 'Website untuk sekolah', '2025-12-25 15:10:00'),
(1, 'Doni Setiawan', 'Gym Fitness Pro', 'doni@fitnesspro.com', '081234567813', 'Jl. Tebet Raya No. 90, Jakarta Selatan', 'Social Media', 'Promosi membership gym', '2026-01-02 10:30:00'),
(1, 'Maya Sari', 'Property Prima Realty', 'maya@propertyprima.com', '081234567814', 'Jl. Kuningan Barat No. 45, Jakarta Selatan', 'Referral', 'Klien dari Rizki Pratama', '2026-01-05 14:00:00'),
(1, 'Agus Salim', 'Bengkel Mobil Jaya', 'agus@bengkeljaya.com', '081234567815', 'Jl. Daan Mogot No. 567, Jakarta Barat', 'Website', 'Butuh landing page promo', '2026-01-08 11:15:00'),
(1, 'Novi Wulandari', 'Travel Wisata Nusantara', 'novi@travelwisata.com', '081234567816', 'Jl. Menteng Raya No. 123, Jakarta Pusat', 'Social Media', 'Promosi paket wisata', '2026-01-12 09:45:00'),
(1, 'Faisal Rahman', 'Laundry Express 24', 'faisal@laundryexpress.com', '081234567817', 'Jl. Fatmawati No. 89, Jakarta Selatan', 'Direct', 'Startup laundry online', '2026-01-15 13:30:00'),
(1, 'Ratna Dewi', 'Catering Lezat Ratna', 'ratna@cateringlezat.com', '081234567818', 'Jl. Kelapa Gading No. 234, Jakarta Utara', 'Website', 'E-commerce untuk catering', '2026-01-18 10:00:00'),
(1, 'Irfan Hakim', 'Konsultan Pajak Profesional', 'irfan@konsultanpajak.com', '081234567819', 'Jl. Rasuna Said No. 67, Jakarta Selatan', 'Referral', 'Butuh website profesional', '2026-01-22 15:20:00'),
(1, 'Sinta Maharani', 'Wedding Organizer Impian', 'sinta@weddingimpian.com', '081234567820', 'Jl. Pondok Indah No. 45, Jakarta Selatan', 'Social Media', 'Promosi jasa wedding', '2026-01-25 11:30:00'),
(1, 'Benny Kurniawan', 'PT. Logistik Cepat Sampai', 'benny@logistikcepat.co.id', '081234567821', 'Jl. Cakung No. 789, Jakarta Timur', 'Website', 'Tracking system website', '2026-01-28 14:45:00'),
(1, 'Diana Putri', 'Kursus Bahasa Inggris Diana', 'diana@kursusbahasa.com', '081234567822', 'Jl. Menteng Dalam No. 34, Jakarta Pusat', 'Direct', 'Platform kursus online', '2026-02-01 09:15:00'),
(1, 'Eko Prasetyo', 'Apotek Sehat 24 Jam', 'eko@apoteksehat.com', '081234567823', 'Jl. Cilandak No. 56, Jakarta Selatan', 'Social Media', 'E-commerce apotek', '2026-02-05 13:00:00'),
(1, 'Lina Hermawan', 'Fashion Store Online', 'lina@fashionstore.com', '081234567824', 'Jl. Blok M No. 123, Jakarta Selatan', 'Referral', 'Upgrade website existing', '2026-02-08 10:30:00'),
(1, 'Tono Sugiarto', 'Pet Shop Animal Care', 'tono@petshopcare.com', '081234567825', 'Jl. Kebayoran Baru No. 78, Jakarta Selatan', 'Website', 'Website dan social media', '2026-02-12 15:45:00');

-- Sample clients for Business 2 (Konsultan Bisnis Prima) - 10 clients
INSERT INTO clients (business_id, client_name, company, email, phone, address, source, created_at) VALUES
(2, 'Hasan Basri', 'PT. Manufaktur Indonesia', 'hasan@manufaktur.co.id', '082134567801', 'Kawasan Industri Jababeka, Bekasi', 'Direct', '2025-11-10 09:00:00'),
(2, 'Wati Suryani', 'CV. Retail Modern', 'wati@retailmodern.com', '082134567802', 'Mall Taman Anggrek, Jakarta Barat', 'Referral', '2025-11-20 10:30:00'),
(2, 'Joko Widodo', 'Startup Tech Innovate', 'joko@techinnovate.id', '082134567803', 'Jl. Fatmawati No. 123, Jakarta Selatan', 'Website', '2025-12-01 11:45:00'),
(2, 'Sri Mulyani', 'Hotel Bintang Lima', 'sri@hotelbintanglima.com', '082134567804', 'Jl. HR Rasuna Said, Jakarta Selatan', 'Direct', '2025-12-10 14:00:00'),
(2, 'Anton Setiawan', 'Pabrik Tekstil Sejahtera', 'anton@tekstilsejahtera.co.id', 'Jl. Raya Bekasi KM 28, Bekasi', '082134567805', 'Referral', '2025-12-20 09:30:00'),
(2, 'Mega Sari', 'Franchise F&B Group', 'mega@franchisefnb.com', '082134567806', 'Jl. Senayan No. 45, Jakarta Pusat', 'Social Media', '2026-01-05 13:15:00'),
(2, 'Rusdi Harahap', 'Developer Properti Megah', 'rusdi@propertimegah.co.id', '082134567807', 'Jl. Kuningan Mulia, Jakarta Selatan', 'Direct', '2026-01-15 10:00:00'),
(2, 'Anita Wijaya', 'Klinik Kecantikan Chain', 'anita@klinikkecantikan.com', '082134567808', 'Jl. Kemang No. 89, Jakarta Selatan', 'Referral', '2026-01-25 15:30:00'),
(2, 'Budiman Sutrisno', 'Importir Elektronik', 'budiman@importirelektronik.com', '082134567809', 'Jl. Mangga Dua, Jakarta Pusat', 'Website', '2026-02-01 11:00:00'),
(2, 'Ratih Kusuma', 'E-commerce Platform', 'ratih@ecommerceplatform.id', '082134567810', 'Jl. Sudirman Kav. 88, Jakarta Selatan', 'Social Media', '2026-02-10 14:45:00');

-- Sample clients for Business 3 (CleanPro Services) - 8 clients
INSERT INTO clients (business_id, client_name, company, email, phone, address, source, created_at) VALUES
(3, 'Fajar Nugroho', 'Gedung Perkantoran Megah Tower', 'fajar@megahtower.com', '083134567801', 'Jl. Sudirman, Jakarta Selatan', 'Direct', '2025-11-15 08:00:00'),
(3, 'Indah Permata', 'Rumah Sakit Harapan Sehat', 'indah@rsharapan.co.id', '083134567802', 'Jl. Mampang Prapatan, Jakarta Selatan', 'Referral', '2025-12-01 09:30:00'),
(3, 'Wahyu Hidayat', 'Hotel Grand Luxury', 'wahyu@hotelgrand.com', '083134567803', 'Jl. Gatot Subroto, Jakarta Selatan', 'Website', '2025-12-15 10:45:00'),
(3, 'Nur Azizah', 'Mall Shopping Center', 'nur@mallshopping.com', '083134567804', 'Jl. Thamrin, Jakarta Pusat', 'Direct', '2026-01-01 11:15:00'),
(3, 'Hari Susanto', 'Apartemen Residence Elite', 'hari@apartemenelite.com', '083134567805', 'Jl. Casablanca, Jakarta Selatan', 'Social Media', '2026-01-10 13:30:00'),
(3, 'Vina Melati', 'Universitas Swasta Terkemuka', 'vina@universitasswasta.ac.id', '083134567806', 'Jl. Kalibata, Jakarta Selatan', 'Referral', '2026-01-20 09:00:00'),
(3, 'Rudi Hartono', 'Pabrik Manufaktur PT. Sejahtera', 'rudi@pabriksejahtera.co.id', '083134567807', 'Kawasan Industri Cikande, Banten', 'Website', '2026-02-01 14:15:00'),
(3, 'Sari Wulandari', 'Kompleks Perumahan Hijau Asri', 'sari@perumahanhijau.com', '083134567808', 'Jl. Bintaro, Tangerang Selatan', 'Direct', '2026-02-08 10:30:00');

-- Sample deals for Business 1 (Jasaku Digital Agency) - 30 deals with various stages
INSERT INTO deals (business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes, created_at) VALUES
-- Won deals (10)
(1, 1, 1, 'Website Company Profile PT. Maju Bersama', 15000000.00, 10.00, 13500000.00, 'Won', '2026-01-15', 'Deal closed, payment completed', '2025-11-01 09:30:00'),
(1, 3, 6, 'Logo & Brand Identity Toko Elektronik', 2500000.00, 0.00, 2500000.00, 'Won', '2026-01-10', 'Client sangat puas dengan hasil', '2025-11-10 10:15:00'),
(1, 5, 7, 'SEO Optimization Digital Nusantara', 10000000.00, 5.00, 9500000.00, 'Won', '2026-01-20', 'Project 3 bulan dimulai', '2025-11-20 11:00:00'),
(1, 7, 4, 'Social Media Management Restoran', 5000000.00, 10.00, 4500000.00, 'Won', '2026-01-25', 'Kontrak 3 bulan', '2025-12-01 13:30:00'),
(1, 10, 6, 'Logo Design Toko Kue Manis', 2500000.00, 15.00, 2125000.00, 'Won', '2026-01-18', 'Client UMKM, kasih diskon', '2025-12-15 09:45:00'),
(1, 12, 2, 'Website Sekolah Cerdas Indonesia', 8000000.00, 0.00, 8000000.00, 'Won', '2026-02-01', 'Website pendidikan', '2025-12-25 14:20:00'),
(1, 14, 8, 'Google Ads Campaign Property Prima', 7500000.00, 0.00, 7500000.00, 'Won', '2026-02-05', 'Budget ads terpisah', '2026-01-05 10:30:00'),
(1, 16, 4, 'Social Media Travel Wisata', 5000000.00, 5.00, 4750000.00, 'Won', '2026-02-10', 'Fokus Instagram & TikTok', '2026-01-12 11:15:00'),
(1, 18, 10, 'E-commerce Catering Lezat', 25000000.00, 8.00, 23000000.00, 'Won', '2026-02-15', 'Project besar, sistem kompleks', '2026-01-18 15:00:00'),
(1, 20, 3, 'Social Media Management Wedding', 12000000.00, 10.00, 10800000.00, 'Won', '2026-02-20', 'Paket premium 6 bulan', '2026-01-25 09:30:00'),

-- Negotiation stage (5)
(1, 2, 3, 'Social Media Management 6 Bulan CV. Sukses Mandiri', 12000000.00, 5.00, 11400000.00, 'Negotiation', '2026-03-15', 'Nego diskon, minta 10%', '2025-11-05 10:45:00'),
(1, 4, 10, 'E-commerce Butik Fashion Dewi', 25000000.00, 0.00, 25000000.00, 'Negotiation', '2026-03-20', 'Nego payment terms 3x cicilan', '2025-11-15 13:00:00'),
(1, 15, 2, 'Landing Page Bengkel Jaya', 8000000.00, 10.00, 7200000.00, 'Negotiation', '2026-03-10', 'Client minta custom feature', '2026-01-08 14:30:00'),
(1, 19, 1, 'Website Konsultan Pajak Pro', 15000000.00, 5.00, 14250000.00, 'Negotiation', '2026-03-25', 'Diskusi fitur tambahan', '2026-01-22 11:45:00'),
(1, 23, 10, 'E-commerce Apotek Sehat', 25000000.00, 3.00, 24250000.00, 'Negotiation', '2026-03-30', 'Nego integrasi payment', '2026-02-05 10:15:00'),

-- Proposal stage (6)
(1, 6, 2, 'Website Klinik Sehat Sentosa', 8000000.00, 0.00, 8000000.00, 'Proposal', '2026-03-05', 'Menunggu approval proposal', '2025-11-25 09:45:00'),
(1, 8, 5, 'Branding Salon Cantik Linda', 5000000.00, 0.00, 5000000.00, 'Proposal', '2026-03-12', 'Kirim 3 konsep desain', '2025-12-05 10:30:00'),
(1, 11, 1, 'Website CV. Import Eksport', 15000000.00, 5.00, 14250000.00, 'Proposal', '2026-03-18', 'Proposal detail sudah dikirim', '2025-12-20 11:00:00'),
(1, 13, 3, 'Social Media Gym Fitness Pro', 12000000.00, 10.00, 10800000.00, 'Proposal', '2026-03-22', 'Tawaran paket 6 bulan', '2026-01-02 13:15:00'),
(1, 21, 1, 'Website Tracking Logistik', 15000000.00, 0.00, 15000000.00, 'Proposal', '2026-04-01', 'Custom development', '2026-01-28 15:30:00'),
(1, 25, 4, 'Social Media Pet Shop', 5000000.00, 5.00, 4750000.00, 'Proposal', '2026-04-05', 'Proposal paket 3 bulan', '2026-02-12 09:00:00'),

-- Qualified stage (4)
(1, 9, 1, 'Website PT. Konstruksi Megah', 15000000.00, 0.00, 15000000.00, 'Qualified', '2026-04-10', 'Budget approved, lanjut proposal', '2025-12-10 14:45:00'),
(1, 17, 9, 'Content Writing Laundry Express', 3000000.00, 0.00, 3000000.00, 'Qualified', '2026-03-28', 'Diskusi content strategy', '2026-01-15 10:00:00'),
(1, 22, 1, 'Platform Kursus Online', 15000000.00, 5.00, 14250000.00, 'Qualified', '2026-04-15', 'Meeting dengan stakeholder', '2026-02-01 11:30:00'),
(1, 24, 2, 'Website Fashion Store', 8000000.00, 0.00, 8000000.00, 'Qualified', '2026-04-08', 'Evaluasi existing website', '2026-02-08 13:45:00'),

-- Lead stage (4)
(1, 1, 7, 'SEO Optimization PT. Maju Bersama (Lanjutan)', 10000000.00, 0.00, 10000000.00, 'Lead', '2026-04-20', 'Inquiry baru dari existing client', '2026-02-10 09:15:00'),
(1, 5, 12, 'Video Production Digital Nusantara', 8500000.00, 0.00, 8500000.00, 'Lead', '2026-04-25', 'Initial contact', '2026-02-12 14:00:00'),
(1, 7, 8, 'Google Ads Restoran Nusantara', 7500000.00, 0.00, 7500000.00, 'Lead', '2026-04-18', 'Client tanya-tanya', '2026-02-15 10:45:00'),
(1, 10, 3, 'Social Media Premium Toko Kue', 12000000.00, 10.00, 10800000.00, 'Lead', '2026-05-01', 'Mau upgrade dari basic', '2026-02-18 15:20:00'),

-- Lost deal (1)
(1, 11, 10, 'E-commerce Import Eksport', 25000000.00, 5.00, 23750000.00, 'Lost', NULL, 'Budget client tidak cukup', '2026-01-10 11:00:00');

-- Sample deals for Business 2 (Konsultan Bisnis Prima) - 12 deals
INSERT INTO deals (business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes, created_at) VALUES
(2, 26, 13, 'Konsultasi Strategi PT. Manufaktur', 15000000.00, 0.00, 15000000.00, 'Won', '2026-01-30', 'Project selesai', '2025-11-10 09:00:00'),
(2, 28, 14, 'Business Plan Startup Tech', 10000000.00, 10.00, 9000000.00, 'Won', '2026-02-10', 'Diskon untuk startup', '2025-12-01 10:30:00'),
(2, 29, 15, 'Financial Analysis Hotel', 8000000.00, 0.00, 8000000.00, 'Won', '2026-02-15', 'Analisis keuangan 3 tahun', '2025-12-10 11:45:00'),
(2, 31, 16, 'HR Management Franchise', 12000000.00, 5.00, 11400000.00, 'Negotiation', '2026-03-20', 'Nego scope pekerjaan', '2026-01-05 13:00:00'),
(2, 27, 13, 'Strategi Bisnis CV. Retail', 15000000.00, 0.00, 15000000.00, 'Negotiation', '2026-03-25', 'Diskusi timeline', '2025-11-20 14:30:00'),
(2, 32, 14, 'Business Plan Developer Properti', 10000000.00, 0.00, 10000000.00, 'Proposal', '2026-03-30', 'Menunggu approval', '2026-01-15 09:15:00'),
(2, 30, 15, 'Financial Planning Pabrik Tekstil', 8000000.00, 0.00, 8000000.00, 'Proposal', '2026-04-05', 'Proposal dikirim', '2025-12-20 10:45:00'),
(2, 33, 16, 'HR System Klinik Kecantikan', 12000000.00, 10.00, 10800000.00, 'Qualified', '2026-04-10', 'Meeting dengan HRD', '2026-01-25 15:00:00'),
(2, 34, 13, 'Konsultasi Strategi Importir', 15000000.00, 0.00, 15000000.00, 'Qualified', '2026-04-15', 'Budget review', '2026-02-01 11:30:00'),
(2, 35, 14, 'Business Plan E-commerce Platform', 10000000.00, 5.00, 9500000.00, 'Lead', '2026-04-20', 'Initial discussion', '2026-02-10 13:45:00'),
(2, 26, 16, 'HR Consulting PT. Manufaktur', 12000000.00, 0.00, 12000000.00, 'Lead', '2026-04-25', 'Inquiry dari existing client', '2026-02-15 09:00:00'),
(2, 28, 13, 'Strategi Ekspansi Startup Tech', 15000000.00, 10.00, 13500000.00, 'Lead', '2026-05-01', 'Rencana scale up', '2026-02-18 14:15:00');

-- Sample deals for Business 3 (CleanPro Services) - 10 deals
INSERT INTO deals (business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date, notes, created_at) VALUES
(3, 36, 17, 'Office Cleaning Gedung Megah Tower', 5000000.00, 0.00, 5000000.00, 'Won', '2026-01-31', 'Kontrak bulanan aktif', '2025-11-15 08:30:00'),
(3, 37, 18, 'Deep Cleaning Rumah Sakit', 3500000.00, 0.00, 3500000.00, 'Won', '2026-02-05', 'Cleaning menyeluruh', '2025-12-01 09:45:00'),
(3, 38, 17, 'Office Cleaning Hotel Grand', 5000000.00, 5.00, 4750000.00, 'Won', '2026-02-10', 'Diskon kontrak 6 bulan', '2025-12-15 10:00:00'),
(3, 40, 17, 'Office Cleaning Apartemen Elite', 5000000.00, 0.00, 5000000.00, 'Negotiation', '2026-03-15', 'Nego harga bulanan', '2026-01-10 11:15:00'),
(3, 39, 19, 'Post-Construction Mall Shopping', 8000000.00, 0.00, 8000000.00, 'Negotiation', '2026-03-20', 'Pembersihan renovasi', '2026-01-01 13:30:00'),
(3, 41, 18, 'Deep Cleaning Universitas', 3500000.00, 10.00, 3150000.00, 'Proposal', '2026-03-25', 'Diskon institusi pendidikan', '2026-01-20 09:00:00'),
(3, 42, 19, 'Post-Construction Pabrik', 8000000.00, 5.00, 7600000.00, 'Proposal', '2026-04-01', 'Cleaning pasca konstruksi', '2026-02-01 14:45:00'),
(3, 43, 17, 'Office Cleaning Kompleks Perumahan', 5000000.00, 0.00, 5000000.00, 'Qualified', '2026-04-05', 'Survey lokasi done', '2026-02-08 10:30:00'),
(3, 36, 18, 'Deep Cleaning Gedung Megah', 3500000.00, 0.00, 3500000.00, 'Lead', '2026-04-10', 'Request dari existing client', '2026-02-12 11:45:00'),
(3, 38, 19, 'Post-Construction Hotel Grand', 8000000.00, 5.00, 7600000.00, 'Lead', '2026-04-15', 'Renovasi lantai 5', '2026-02-15 15:00:00');

-- Sample transactions for Business 1 (Jasaku Digital Agency) - Income & Expense
INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id, created_at) VALUES
-- Income from Won deals
(1, 'Income', 'Pembayaran DP 50% Website PT. Maju Bersama', 'Deal Payment', 6750000.00, '2026-01-16', 'Transfer', 'DP 50% dari total Rp 13.500.000', 1, '2026-01-16 14:00:00'),
(1, 'Income', 'Pelunasan Website PT. Maju Bersama', 'Deal Payment', 6750000.00, '2026-02-20', 'Transfer', 'Pelunasan 50%', 1, '2026-02-20 10:30:00'),
(1, 'Income', 'Pembayaran Logo Toko Elektronik', 'Deal Payment', 2500000.00, '2026-01-11', 'Transfer', 'Lunas', 3, '2026-01-11 09:15:00'),
(1, 'Income', 'Pembayaran DP SEO Digital Nusantara', 'Deal Payment', 4750000.00, '2026-01-21', 'Transfer', 'DP 50%', 5, '2026-01-21 11:00:00'),
(1, 'Income', 'Pembayaran Bulan 1 Social Media Restoran', 'Deal Payment', 1500000.00, '2026-01-26', 'Transfer', 'Cicilan 1/3', 7, '2026-01-26 13:45:00'),
(1, 'Income', 'Pembayaran Bulan 2 Social Media Restoran', 'Deal Payment', 1500000.00, '2026-02-26', 'Transfer', 'Cicilan 2/3', 7, '2026-02-26 14:00:00'),
(1, 'Income', 'Pembayaran Logo Toko Kue Manis', 'Deal Payment', 2125000.00, '2026-01-19', 'QRIS', 'Lunas via QRIS', 10, '2026-01-19 10:30:00'),
(1, 'Income', 'Pembayaran DP Website Sekolah', 'Deal Payment', 4000000.00, '2026-02-02', 'Transfer', 'DP 50%', 12, '2026-02-02 09:00:00'),
(1, 'Income', 'Pembayaran Google Ads Property', 'Deal Payment', 7500000.00, '2026-02-06', 'Transfer', 'Lunas', 14, '2026-02-06 11:15:00'),
(1, 'Income', 'Pembayaran Social Media Travel', 'Deal Payment', 4750000.00, '2026-02-11', 'Transfer', 'Lunas', 16, '2026-02-11 13:30:00'),
(1, 'Income', 'Pembayaran DP 40% E-commerce Catering', 'Deal Payment', 9200000.00, '2026-02-16', 'Transfer', 'DP 40%', 18, '2026-02-16 10:00:00'),
(1, 'Income', 'Pembayaran Bulan 1-3 Social Media Wedding', 'Deal Payment', 5400000.00, '2026-02-21', 'Transfer', 'Pembayaran 3 bulan pertama', 20, '2026-02-21 14:45:00'),

-- Other income
(1, 'Income', 'Bonus Referral dari Client', 'Lainnya', 1000000.00, '2026-01-15', 'Transfer', 'Bonus dari Rizki Pratama', NULL, '2026-01-15 15:00:00'),
(1, 'Income', 'Komisi Affiliate Marketing', 'Lainnya', 500000.00, '2026-02-01', 'Transfer', 'Komisi dari partnership', NULL, '2026-02-01 10:30:00'),

-- Expenses - Operational
(1, 'Expense', 'Sewa Kantor Bulan Januari', 'Operasional', 5000000.00, '2026-01-01', 'Transfer', 'Bayar sewa bulanan', NULL, '2026-01-01 09:00:00'),
(1, 'Expense', 'Sewa Kantor Bulan Februari', 'Operasional', 5000000.00, '2026-02-01', 'Transfer', 'Bayar sewa bulanan', NULL, '2026-02-01 09:00:00'),
(1, 'Expense', 'Listrik & Internet Januari', 'Operasional', 1500000.00, '2026-01-10', 'Transfer', 'Utility bulanan', NULL, '2026-01-10 10:00:00'),
(1, 'Expense', 'Listrik & Internet Februari', 'Operasional', 1500000.00, '2026-02-10', 'Transfer', 'Utility bulanan', NULL, '2026-02-10 10:00:00'),
(1, 'Expense', 'Gaji Tim Januari (5 orang)', 'Operasional', 25000000.00, '2026-01-25', 'Transfer', 'Payroll bulanan', NULL, '2026-01-25 14:00:00'),
(1, 'Expense', 'Gaji Tim Februari (5 orang)', 'Operasional', 25000000.00, '2026-02-25', 'Transfer', 'Payroll bulanan', NULL, '2026-02-25 14:00:00'),

-- Expenses - Marketing
(1, 'Expense', 'Facebook Ads Campaign Januari', 'Marketing', 2000000.00, '2026-01-05', 'Transfer', 'Budget iklan FB & IG', NULL, '2026-01-05 11:00:00'),
(1, 'Expense', 'Google Ads Campaign Januari', 'Marketing', 1500000.00, '2026-01-08', 'Transfer', 'Budget iklan Google', NULL, '2026-01-08 13:00:00'),
(1, 'Expense', 'Facebook Ads Campaign Februari', 'Marketing', 2000000.00, '2026-02-05', 'Transfer', 'Budget iklan FB & IG', NULL, '2026-02-05 11:00:00'),
(1, 'Expense', 'Content Creator Fee', 'Marketing', 3000000.00, '2026-02-15', 'Transfer', 'Fee influencer promo', NULL, '2026-02-15 14:30:00'),

-- Expenses - Tools
(1, 'Expense', 'Adobe Creative Cloud Team', 'Tools', 2500000.00, '2026-01-01', 'Transfer', 'Subscription 5 user/tahun', NULL, '2026-01-01 10:00:00'),
(1, 'Expense', 'Hosting & Domain Renewal', 'Tools', 3000000.00, '2026-01-15', 'Transfer', 'Renewal 10 domain', NULL, '2026-01-15 11:30:00'),
(1, 'Expense', 'SEMrush Subscription', 'Tools', 2000000.00, '2026-01-20', 'Transfer', 'SEO tools annual', NULL, '2026-01-20 09:45:00'),
(1, 'Expense', 'Figma Professional Plan', 'Tools', 1500000.00, '2026-02-01', 'Transfer', 'Design collaboration tool', NULL, '2026-02-01 10:15:00'),
(1, 'Expense', 'Server & Cloud Storage', 'Tools', 2000000.00, '2026-02-10', 'Transfer', 'AWS monthly bill', NULL, '2026-02-10 11:00:00'),

-- Expenses - Other
(1, 'Expense', 'Pembelian Laptop untuk Designer', 'Lainnya', 15000000.00, '2026-01-12', 'Transfer', 'MacBook Pro M3', NULL, '2026-01-12 14:00:00'),
(1, 'Expense', 'Training Team: Digital Marketing', 'Lainnya', 5000000.00, '2026-02-08', 'Transfer', 'Workshop 2 hari', NULL, '2026-02-08 09:30:00');

-- Sample transactions for Business 2 (Konsultan Bisnis Prima)
INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id, created_at) VALUES
-- Income
(2, 'Income', 'Pembayaran Konsultasi PT. Manufaktur', 'Deal Payment', 15000000.00, '2026-02-01', 'Transfer', 'Lunas', 31, '2026-02-01 10:00:00'),
(2, 'Income', 'Pembayaran Business Plan Startup', 'Deal Payment', 9000000.00, '2026-02-12', 'Transfer', 'Lunas', 32, '2026-02-12 11:30:00'),
(2, 'Income', 'Pembayaran Financial Analysis Hotel', 'Deal Payment', 8000000.00, '2026-02-16', 'Transfer', 'Lunas', 33, '2026-02-16 14:00:00'),

-- Expenses
(2, 'Expense', 'Sewa Office Space Januari', 'Operasional', 8000000.00, '2026-01-01', 'Transfer', 'Sewa bulanan', NULL, '2026-01-01 09:00:00'),
(2, 'Expense', 'Sewa Office Space Februari', 'Operasional', 8000000.00, '2026-02-01', 'Transfer', 'Sewa bulanan', NULL, '2026-02-01 09:00:00'),
(2, 'Expense', 'Gaji Konsultan Senior (3 orang)', 'Operasional', 30000000.00, '2026-01-25', 'Transfer', 'Payroll Januari', NULL, '2026-01-25 14:00:00'),
(2, 'Expense', 'Gaji Konsultan Senior (3 orang)', 'Operasional', 30000000.00, '2026-02-25', 'Transfer', 'Payroll Februari', NULL, '2026-02-25 14:00:00'),
(2, 'Expense', 'LinkedIn Premium Business', 'Marketing', 1500000.00, '2026-01-05', 'Transfer', 'Marketing subscription', NULL, '2026-01-05 10:00:00'),
(2, 'Expense', 'Business Analysis Software', 'Tools', 5000000.00, '2026-01-10', 'Transfer', 'Annual license', NULL, '2026-01-10 11:00:00');

-- Sample transactions for Business 3 (CleanPro Services)
INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, notes, deal_id, created_at) VALUES
-- Income
(3, 'Income', 'Pembayaran Cleaning Gedung Megah', 'Deal Payment', 5000000.00, '2026-02-01', 'Transfer', 'Pembayaran bulan pertama', 46, '2026-02-01 09:30:00'),
(3, 'Income', 'Pembayaran Deep Cleaning RS', 'Deal Payment', 3500000.00, '2026-02-06', 'Transfer', 'Lunas', 47, '2026-02-06 10:45:00'),
(3, 'Income', 'Pembayaran Cleaning Hotel', 'Deal Payment', 4750000.00, '2026-02-11', 'Transfer', 'Pembayaran bulan 1', 48, '2026-02-11 11:00:00'),

-- Expenses
(3, 'Expense', 'Gaji Staff Cleaning (15 orang)', 'Operasional', 30000000.00, '2026-01-25', 'Transfer', 'Payroll Januari', NULL, '2026-01-25 14:00:00'),
(3, 'Expense', 'Gaji Staff Cleaning (15 orang)', 'Operasional', 30000000.00, '2026-02-25', 'Transfer', 'Payroll Februari', NULL, '2026-02-25 14:00:00'),
(3, 'Expense', 'Pembelian Alat Cleaning', 'Tools', 5000000.00, '2026-01-10', 'Transfer', 'Vacuum, mop, dll', NULL, '2026-01-10 10:00:00'),
(3, 'Expense', 'Bahan Kimia Pembersih', 'Tools', 3000000.00, '2026-01-15', 'Transfer', 'Stock bulanan', NULL, '2026-01-15 11:00:00'),
(3, 'Expense', 'Bahan Kimia Pembersih', 'Tools', 3000000.00, '2026-02-15', 'Transfer', 'Stock bulanan', NULL, '2026-02-15 11:00:00'),
(3, 'Expense', 'Bensin Mobil Operasional', 'Operasional', 2000000.00, '2026-01-20', 'Cash', 'BBM bulanan', NULL, '2026-01-20 09:00:00'),
(3, 'Expense', 'Bensin Mobil Operasional', 'Operasional', 2000000.00, '2026-02-20', 'Cash', 'BBM bulanan', NULL, '2026-02-20 09:00:00'),
(3, 'Expense', 'Iklan Google Maps & Facebook', 'Marketing', 1500000.00, '2026-01-05', 'Transfer', 'Budget iklan lokal', NULL, '2026-01-05 10:30:00'),
(3, 'Expense', 'Iklan Google Maps & Facebook', 'Marketing', 1500000.00, '2026-02-05', 'Transfer', 'Budget iklan lokal', NULL, '2026-02-05 10:30:00');

-- Sample deal payments (for tracking partial payments)
INSERT INTO deal_payments (deal_id, amount, payment_date, method, notes, created_at) VALUES
-- Business 1 payments
(1, 6750000.00, '2026-01-16', 'Transfer', 'DP 50%', '2026-01-16 14:05:00'),
(1, 6750000.00, '2026-02-20', 'Transfer', 'Pelunasan', '2026-02-20 10:35:00'),
(3, 2500000.00, '2026-01-11', 'Transfer', 'Lunas', '2026-01-11 09:20:00'),
(5, 4750000.00, '2026-01-21', 'Transfer', 'DP 50%', '2026-01-21 11:05:00'),
(7, 1500000.00, '2026-01-26', 'Transfer', 'Cicilan 1/3', '2026-01-26 13:50:00'),
(7, 1500000.00, '2026-02-26', 'Transfer', 'Cicilan 2/3', '2026-02-26 14:05:00'),
(10, 2125000.00, '2026-01-19', 'QRIS', 'Lunas', '2026-01-19 10:35:00'),
(12, 4000000.00, '2026-02-02', 'Transfer', 'DP 50%', '2026-02-02 09:05:00'),
(14, 7500000.00, '2026-02-06', 'Transfer', 'Lunas', '2026-02-06 11:20:00'),
(16, 4750000.00, '2026-02-11', 'Transfer', 'Lunas', '2026-02-11 13:35:00'),
(18, 9200000.00, '2026-02-16', 'Transfer', 'DP 40%', '2026-02-16 10:05:00'),
(20, 5400000.00, '2026-02-21', 'Transfer', 'Payment 3 bulan', '2026-02-21 14:50:00'),

-- Business 2 payments
(31, 15000000.00, '2026-02-01', 'Transfer', 'Lunas', '2026-02-01 10:05:00'),
(32, 9000000.00, '2026-02-12', 'Transfer', 'Lunas', '2026-02-12 11:35:00'),
(33, 8000000.00, '2026-02-16', 'Transfer', 'Lunas', '2026-02-16 14:05:00'),

-- Business 3 payments
(46, 5000000.00, '2026-02-01', 'Transfer', 'Bulan 1', '2026-02-01 09:35:00'),
(47, 3500000.00, '2026-02-06', 'Transfer', 'Lunas', '2026-02-06 10:50:00'),
(48, 4750000.00, '2026-02-11', 'Transfer', 'Bulan 1', '2026-02-11 11:05:00');
