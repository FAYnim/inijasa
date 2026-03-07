-- Migration 003: Optimize business query indexes
ALTER TABLE businesses 
ADD INDEX IF NOT EXISTS idx_user_is_primary (user_id, is_primary);

ALTER TABLE businesses 
ADD INDEX IF NOT EXISTS idx_business_name (business_name);
