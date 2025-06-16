CREATE TABLE IF NOT EXISTS `agent_payment_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL,
  `ecocash_number` varchar(20) DEFAULT NULL,
  `ecocash_name` varchar(100) DEFAULT NULL,
  `mukuru_number` varchar(20) DEFAULT NULL,
  `mukuru_name` varchar(100) DEFAULT NULL,
  `innbucks_number` varchar(20) DEFAULT NULL,
  `innbucks_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_id` (`agent_id`),
  CONSTRAINT `fk_agent_payment_settings_agent_id` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 