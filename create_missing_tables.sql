CREATE TABLE IF NOT EXISTS `leads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lead_code` varchar(50) NOT NULL,
  `lead_name` varchar(255) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `source` enum('website','referral','cold_call','social_media','exhibition','other') NOT NULL DEFAULT 'other',
  `status` enum('new','contacted','qualified','proposal_sent','negotiation','won','lost') NOT NULL DEFAULT 'new',
  `assigned_to` int(11) DEFAULT NULL,
  `expected_value` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lead_code` (`lead_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `gst_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 18.00,
  `hsn_code_default` varchar(20) DEFAULT NULL,
  `cgst_rate` decimal(5,2) NOT NULL DEFAULT 9.00,
  `sgst_rate` decimal(5,2) NOT NULL DEFAULT 9.00,
  `igst_rate` decimal(5,2) NOT NULL DEFAULT 18.00,
  `effective_from` date NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
