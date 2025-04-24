<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db_connect.php';

// Kiểm tra đăng nhập và quyền của người dùng
if (!is_logged_in()) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    $_SESSION['error_message'] = "Vui lòng đăng nhập để truy cập trang này";
    header('Location: ../dangnhap.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Lấy thông tin người dùng
$user = get_logged_in_user();

// Kiểm tra vai trò người dùng
if ($user['vai_tro'] != 'bacsi') {
    // Nếu không phải bác sĩ, chuyển hướng về trang chủ
    $_SESSION['error_message'] = "Bạn không có quyền truy cập trang này";
    header('Location: ../index.php');
    exit;
}

// Kiểm tra xem bác sĩ có tồn tại trong hệ thống không
$stmt = $conn->prepare("SELECT id FROM bacsi WHERE nguoidung_id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Nếu không tìm thấy bác sĩ, đăng xuất và thông báo lỗi
    logout_user();
    $_SESSION['error_message'] = "Tài khoản của bạn không được liên kết với bác sĩ nào trong hệ thống";
    header('Location: ../dangnhap.php');
    exit;
}
?>