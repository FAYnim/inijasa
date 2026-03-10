-- Migration 008: Add image_path column to services table
-- Jasaku - Platform Manajemen Bisnis Jasa

ALTER TABLE services 
ADD COLUMN image_path VARCHAR(255) NULL AFTER description;
