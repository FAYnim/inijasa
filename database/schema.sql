-- Jasaku Database Schema
-- Platform Manajemen Bisnis Jasa
-- Updated: includes migrations 001, 002, 003

-- Create database
CREATE DATABASE IF NOT EXISTS jasaku_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jasaku_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    business_limit INT DEFAULT NULL COMMENT 'NULL = use default/unlimited',
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
    INDEX idx_user_id (user_id),
    INDEX idx_user_is_primary (user_id, is_primary),
    INDEX idx_business_name (business_name)
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

-- Deal Stage History table (migration 004)
CREATE TABLE deal_stage_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    from_stage ENUM('Lead','Qualified','Proposal','Negotiation','Won','Lost') NULL,
    to_stage ENUM('Lead','Qualified','Proposal','Negotiation','Won','Lost') NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE CASCADE,
    INDEX idx_deal_id (deal_id),
    INDEX idx_changed_at (changed_at)
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

-- System configuration table (migration 001)
CREATE TABLE system_config (
    config_key VARCHAR(50) PRIMARY KEY,
    config_value VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default system config
INSERT INTO system_config (config_key, config_value, description) VALUES
('default_business_limit', NULL, 'Default limit bisnis per user. NULL = unlimited');

-- Admin user
INSERT INTO users (full_name, email, password_hash) VALUES
('Admin User', 'admin@jasaku.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password
