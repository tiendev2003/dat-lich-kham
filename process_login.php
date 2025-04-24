<?php
// Bắt đầu session
session_start();

// Include file kết nối database
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Kiểm tra nếu đã đăng nhập thì chuyển hướng
if (is_logged_in()) {
    redirect_by_role($_SESSION['vai_tro']);
}

// Xử lý yêu cầu đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra xem có phải là AJAX request không
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    // Lấy dữ liệu từ form
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate dữ liệu
    $errors = [];
    
    if (empty($email)) {
        $errors[] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    
    if (empty($password)) {
        $errors[] = 'Vui lòng nhập mật khẩu';
    }
    
    // Nếu không có lỗi, thực hiện đăng nhập
    if (empty($errors)) {
        $login_result = login_user($email, $password);
        
        if ($login_result['success']) {
            // Nếu chọn "Ghi nhớ đăng nhập"
            if ($remember) {
                // Thiết lập cookie trong 30 ngày (30 * 24 * 60 * 60 = 2592000 giây)
                setcookie('remember_email', $email, time() + 2592000, '/');
            } else {
                // Xóa cookie nếu tồn tại
                if (isset($_COOKIE['remember_email'])) {
                    setcookie('remember_email', '', time() - 3600, '/');
                }
            }
            
            // Trả kết quả nếu là AJAX request
            if ($isAjax) {
                echo json_encode([
                    'success' => true,
                    'message' => $login_result['message'],
                    'redirect' => get_redirect_url($_SESSION['vai_tro'])
                ]);
                exit;
            }
            
            // Chuyển hướng người dùng tới trang phù hợp
            redirect_by_role($_SESSION['vai_tro']);
        } else {
            // Nếu đăng nhập thất bại
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => $login_result['message']
                ]);
                exit;
            }
            
            $_SESSION['login_error'] = $login_result['message'];
            header('Location: dangnhap.php');
            exit;
        }
    } else {
        // Nếu có lỗi validate
        if ($isAjax) {
            echo json_encode([
                'success' => false,
                'message' => $errors[0]
            ]);
            exit;
        }
        
        $_SESSION['login_error'] = $errors[0];
        header('Location: dangnhap.php');
        exit;
    }
}

// Hàm lấy URL chuyển hướng dựa trên vai trò
function get_redirect_url($role) {
    switch ($role) {
        case 'admin':
            return 'admin/tongquan.php';
        case 'bacsi':
            return 'docter/dashboard.php';
        case 'benhnhan':
        default:
            return 'index.php';
    }
}
?>