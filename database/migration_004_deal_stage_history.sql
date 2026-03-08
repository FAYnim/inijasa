-- Migration 004: Deal Stage History
-- Tabel untuk mencatat riwayat perpindahan stage deal

CREATE TABLE IF NOT EXISTS deal_stage_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    from_stage ENUM('Lead','Qualified','Proposal','Negotiation','Won','Lost') NULL,
    to_stage ENUM('Lead','Qualified','Proposal','Negotiation','Won','Lost') NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id) ON DELETE CASCADE,
    INDEX idx_deal_id (deal_id),
    INDEX idx_changed_at (changed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
