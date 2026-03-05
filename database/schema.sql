-- Jasaku Database Schema
-- Platform Manajemen Bisnis Jasa

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

-- Insert sample data for testing (optional)

-- Sample user
INSERT INTO users (full_name, email, password_hash) VALUES
('Admin User', 'admin@jasaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Sample business
INSERT INTO businesses (user_id, business_name, category, description, phone, email, is_primary) VALUES
(1, 'Jasaku Digital Agency', 'Kreatif/Desain', 'Agensi digital kreatif untuk semua kebutuhan bisnis Anda', '081234567890', 'info@jasakudigital.com', 1);

-- Sample services
INSERT INTO services (business_id, service_name, description, price, status) VALUES
(1, 'Website Development', 'Pembuatan website profesional dengan desain modern', 15000000.00, 'Active'),
(1, 'Social Media Management', 'Pengelolaan konten dan strategi media sosial', 5000000.00, 'Active'),
(1, 'Logo Design', 'Desain logo unik dan memorable untuk brand Anda', 2500000.00, 'Active');

-- Sample clients
INSERT INTO clients (business_id, client_name, company, email, phone, source) VALUES
(1, 'Budi Santoso', 'PT. Maju Bersama', 'budi@majubersama.com', '081234567891', 'Referral'),
(1, 'Siti Aminah', 'CV. Sukses Mandiri', 'siti@suksesmandiri.com', '081234567892', 'Social Media'),
(1, 'Ahmad Yani', 'Toko Elektronik Jaya', 'ahmad@elektronikjaya.com', '081234567893', 'Direct');

-- Sample deals
INSERT INTO deals (business_id, client_id, service_id, deal_title, deal_value, discount_percent, final_value, current_stage, expected_close_date) VALUES
(1, 1, 1, 'Website Company Profile PT. Maju Bersama', 15000000.00, 10.00, 13500000.00, 'Proposal', '2026-04-15'),
(1, 2, 2, 'Social Media Management 6 Bulan', 5000000.00, 0.00, 5000000.00, 'Negotiation', '2026-03-25'),
(1, 3, 3, 'Logo & Brand Identity Toko Elektronik', 2500000.00, 0.00, 2500000.00, 'Won', '2026-03-01');

-- Sample transactions
INSERT INTO transactions (business_id, type, title, category, amount, transaction_date, method, deal_id) VALUES
(1, 'Income', 'Pembayaran DP Website PT. Maju Bersama', 'Deal Payment', 6750000.00, '2026-03-05', 'Transfer', 1),
(1, 'Income', 'Pembayaran Logo Toko Elektronik', 'Deal Payment', 2500000.00, '2026-03-01', 'Transfer', 3),
(1, 'Expense', 'Hosting & Domain 1 Tahun', 'Tools', 2000000.00, '2026-02-15', 'Transfer', NULL),
(1, 'Expense', 'Facebook Ads Budget', 'Marketing', 1500000.00, '2026-02-20', 'Transfer', NULL);

-- Sample deal payment
INSERT INTO deal_payments (deal_id, amount, payment_date, method, notes) VALUES
(3, 2500000.00, '2026-03-01', 'Transfer', 'Lunas');
