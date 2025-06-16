-- Add image column to properties table if it doesn't exist
ALTER TABLE properties ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL; 