<?php
// Thông tin cache cài đặt hệ thống
// File này được sử dụng để kiểm tra xem settings_data.php có cần được cập nhật không

// Thời gian cập nhật cuối cùng (UNIX timestamp)
$settings_last_updated = 1745549536; // Tương đương với 2025-04-25 04:52:16

// Thời gian cache hết hạn (tính bằng giây)
$settings_cache_expiry = 3600; // 1 giờ

// Kiểm tra xem cache còn hạn hay không
$settings_cache_valid = (time() - $settings_last_updated) < $settings_cache_expiry;

// Đánh dấu cần làm mới nếu cache không còn hạn
$settings_needs_refresh = !$settings_cache_valid;
?>