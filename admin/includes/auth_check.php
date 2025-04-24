<?php
/**
 * File kiểm tra quyền truy cập vào trang admin
 * Nếu không phải admin, sẽ chuyển hướng về trang đăng nhập
 */

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include file functions.php nếu chưa được include
$functions_path = __DIR__ . '/../../includes/functions.php';
if (file_exists($functions_path)) {
    require_once $functions_path;
}

// Kiểm tra quyền truy cập
function checkAdminAccess() {
    // Nếu đã có hàm is_logged_in() trong functions.php, sử dụng nó
    if (function_exists('is_logged_in')) {
        if (!is_logged_in()) {
            return false;
        }
    } else {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            return false;
        }
    }

    // Kiểm tra xem người dùng có quyền admin không
    if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'admin') {
        return false;
    }

    return true;
}

// Kiểm tra quyền và chuyển hướng nếu không phải admin
if (!checkAdminAccess()) {
    // Lưu URL hiện tại để sau khi đăng nhập có thể quay lại
    $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $_SESSION['redirect_after_login'] = $current_url;
    
    // Thêm thông báo lỗi
    $_SESSION['error_message'] = "Bạn cần đăng nhập với quyền quản trị để truy cập trang này.";
    
    // Chuyển hướng về trang đăng nhập
    header("Location: ../dangnhap.php");
    exit;
}
?>