-- Migration 007: Notifications Table
-- Menambahkan tabel notifications untuk fitur In-App Reminder

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_id INT NOT NULL,
    type ENUM('deal_stale', 'overdue_payment', 'close_date_passed') NOT NULL,
    message TEXT NOT NULL,
    related_id INT, -- deal_id atau invoice_id
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    INDEX idx_business_id (business_id),
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
