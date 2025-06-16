-- Add agent_id column to properties table
ALTER TABLE `properties`
ADD COLUMN `agent_id` int(11) DEFAULT NULL AFTER `owner_id`,
ADD KEY `agent_id` (`agent_id`),
ADD CONSTRAINT `property_agent_id` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- Add new columns to properties table
ALTER TABLE `properties`
ADD COLUMN `summary` TEXT AFTER `description`,
ADD COLUMN `category` VARCHAR(50) AFTER `price`,
ADD COLUMN `status` VARCHAR(50) DEFAULT 'active' AFTER `category`,
ADD COLUMN `main_image` VARCHAR(255) AFTER `status`,
ADD COLUMN `additional_images` TEXT AFTER `main_image`;

-- Update existing records to have default values
UPDATE `properties` 
SET `category` = 'For Rent',
    `status` = 'active'
WHERE `category` IS NULL; 