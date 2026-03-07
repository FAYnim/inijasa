-- Migration 001: Add system configuration table
CREATE TABLE IF NOT EXISTS system_config (
    config_key VARCHAR(50) PRIMARY KEY,
    config_value VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default business limit configuration (NULL = unlimited)
INSERT INTO system_config (config_key, config_value, description) VALUES
('default_business_limit', NULL, 'Default limit bisnis per user. NULL = unlimited')
ON DUPLICATE KEY UPDATE config_value = VALUES(config_value);
