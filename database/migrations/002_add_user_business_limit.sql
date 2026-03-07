-- Migration 002: Add business_limit column to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS business_limit INT DEFAULT NULL COMMENT 'NULL = use default/unlimited'
AFTER password_hash;
