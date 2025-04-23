<?php
// Thông tin kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Tên đăng nhập mặc định của XAMPP
$password = ""; // Mật khẩu mặc định của XAMPP (trống)
$dbname = "dat_lich_kham_db"; // Tên database của bạn

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Đặt charset là utf8 để hiển thị tiếng Việt
$conn->set_charset("utf8");
?>
