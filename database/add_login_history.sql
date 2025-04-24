-- Add login history tracking table
CREATE TABLE IF NOT EXISTS `dangnhap_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `thoi_gian` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) DEFAULT NULL,
  `thiet_bi` varchar(255) DEFAULT NULL,
  `trinh_duyet` varchar(255) DEFAULT NULL,
  `trang_thai` enum('success','failed') NOT NULL DEFAULT 'success',
  `ghi_chu` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_dangnhap_logs_user` FOREIGN KEY (`user_id`) REFERENCES `nguoidung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;