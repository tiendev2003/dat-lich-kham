<?php
// Bắt đầu session
session_start();

// Include file kết nối database
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    // Chuyển hướng về trang đăng nhập
    header('Location: dangnhap.php');
    exit;
}

// Xử lý form đổi mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin người dùng hiện tại
    $user = get_logged_in_user();
    
    // Lấy dữ liệu từ form
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Kiểm tra dữ liệu
    $errors = [];
    
    // Kiểm tra mật khẩu hiện tại
    if (empty($current_password)) {
        $errors[] = "Vui lòng nhập mật khẩu hiện tại";
    } else {
        // Xác thực mật khẩu hiện tại
        if (!password_verify($current_password, $user['mat_khau'])) {
            $errors[] = "Mật khẩu hiện tại không chính xác";
        }
    }
    
    // Kiểm tra mật khẩu mới
    if (empty($new_password)) {
        $errors[] = "Vui lòng nhập mật khẩu mới";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Mật khẩu mới phải có ít nhất 8 ký tự";
    } elseif (!preg_match("/[a-zA-Z]/", $new_password)) {
        $errors[] = "Mật khẩu mới phải chứa ít nhất một chữ cái";
    } elseif (!preg_match("/\d/", $new_password)) {
        $errors[] = "Mật khẩu mới phải chứa ít nhất một chữ số";
    } elseif ($new_password === $current_password) {
        $errors[] = "Mật khẩu mới không được trùng với mật khẩu hiện tại";
    }
    
    // Kiểm tra xác nhận mật khẩu
    if (empty($confirm_password)) {
        $errors[] = "Vui lòng xác nhận mật khẩu mới";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Mật khẩu xác nhận không khớp với mật khẩu mới";
    }
    
    // Nếu không có lỗi, thực hiện đổi mật khẩu
    if (empty($errors)) {
        // Mã hóa mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu trong cơ sở dữ liệu
        $stmt = $conn->prepare("UPDATE nguoidung SET mat_khau = ?, ngay_capnhat = NOW() WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $user['id']);
        
        if ($stmt->execute()) {
            // Tạo log thay đổi mật khẩu nếu cần
            
            // Thông báo thành công
            $_SESSION['success_message'] = "Mật khẩu của bạn đã được thay đổi thành công!";
            
            // Chuyển hướng về trang đổi mật khẩu
            header('Location: doimatkhau.php');
            exit;
        } else {
            $errors[] = "Đã xảy ra lỗi khi cập nhật mật khẩu. Vui lòng thử lại sau.";
        }
    }
    
    // Nếu có lỗi, lưu vào session và chuyển hướng về trang đổi mật khẩu
    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
        header('Location: doimatkhau.php');
        exit;
    }
} else {
    // Nếu không phải là phương thức POST, chuyển hướng về trang đổi mật khẩu
    header('Location: doimatkhau.php');
    exit;
}
?>

