-- Jasaku Database Schema
-- Product: Jasaku - Platform Manajemen Bisnis Jasa

CREATE DATABASE IF NOT EXISTS jasaku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jasaku;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Businesses Table
CREATE TABLE IF NOT EXISTS businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    business_name VARCHAR(150) NOT NULL,
    category ENUM('Kreatif/Desain', 'Konsultan', 'Kebersihan', 'Perbaikan', 'Lainnya') DEFAULT 'Lainnya',
    description TEXT,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    logo_path VARCHAR(255),
    is_primary BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Service Packages Table
CREATE TABLE IF NOT EXISTS service_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    package_name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(15,2) NOT NULL,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    is_deleted BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

-- Clients Table
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    company VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    notes TEXT,
    source ENUM('Referral', 'Social Media', 'Direct', 'Website', 'Lainnya') DEFAULT 'Direct',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE
);

-- Deals Table
CREATE TABLE IF NOT EXISTS deals (
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
    payment_status ENUM('Pending', 'Partial', 'Paid', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (service_package_id) REFERENCES service_packages(id) ON DELETE SET NULL
);

-- Deal Payments Table
CREATE TABLE IF NOT EXISTS deal_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_date DATE NOT NULL,
    method ENUM('Transfer', 'Cash', 'QRIS', 'Lainnya') DEFAULT 'Transfer',
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE CASCADE
);

-- Transactions Table (Income/Expense)
CREATE TABLE IF NOT EXISTS transactions (
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
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX idx_businesses_user ON businesses(user_id);
CREATE INDEX idx_service_packages_business ON service_packages(business_id);
CREATE INDEX idx_clients_business ON clients(business_id);
CREATE INDEX idx_deals_business ON deals(business_id);
CREATE INDEX idx_deals_client ON deals(client_id);
CREATE INDEX idx_deal_payments_deal ON deal_payments(deal_id);
CREATE INDEX idx_transactions_business ON transactions(business_id);
CREATE INDEX idx_transactions_deal ON transactions(deal_id);
