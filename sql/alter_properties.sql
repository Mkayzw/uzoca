ALTER TABLE `properties` 
ADD COLUMN `agent_id` int(11) NOT NULL AFTER `id`,
ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE; 