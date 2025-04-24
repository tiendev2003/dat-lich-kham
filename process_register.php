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

// Xử lý yêu cầu đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra xem có phải là AJAX request không
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    // Lấy dữ liệu từ form
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $birthdate = $_POST['birthdate'];
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validate dữ liệu
    $errors = [];
    
    // Validate username
    if (empty($username)) {
        $errors[] = 'Vui lòng nhập tên đăng nhập';
    }
    
    // Validate fullname
    if (empty($fullname)) {
        $errors[] = 'Vui lòng nhập họ và tên';
    }
    
    // Validate email
    if (empty($email)) {
        $errors[] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    
    // Validate số điện thoại
    if (empty($phone)) {
        $errors[] = 'Vui lòng nhập số điện thoại';
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Số điện thoại phải có 10 chữ số';
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = 'Vui lòng nhập mật khẩu';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    }
    
    // Validate confirm password
    if ($password !== $confirm_password) {
        $errors[] = 'Xác nhận mật khẩu không khớp';
    }
    
    // Validate terms
    if (!$terms) {
        $errors[] = 'Bạn phải đồng ý với điều khoản dịch vụ';
    }
    
    // Lấy năm sinh từ ngày sinh
    $birth_year = !empty($birthdate) ? date('Y', strtotime($birthdate)) : date('Y');
    
    // Nếu không có lỗi, thực hiện đăng ký
    if (empty($errors)) {
        $user_data = [
            'email' => $email,
            'password' => $password,
            'username' => $username
        ];
        
        $patient_data = [
            'fullname' => $fullname,
            'birth_year' => $birth_year,
            'gender' => $gender,
            'phone' => $phone,
            'address' => $address
        ];
        
        $register_result = register_user($user_data, $patient_data);
        
        if ($register_result['success']) {
            // Nếu đăng ký thành công
            if ($isAjax) {
                echo json_encode([
                    'success' => true,
                    'message' => $register_result['message']
                ]);
                exit;
            }
            
            $_SESSION['register_success'] = $register_result['message'];
            header('Location: dangnhap.php');
            exit;
        } else {
            // Nếu đăng ký thất bại
            if ($isAjax) {
                echo json_encode([
                    'success' => false,
                    'message' => $register_result['message']
                ]);
                exit;
            }
            
            $_SESSION['register_error'] = $register_result['message'];
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
        
        $_SESSION['register_error'] = $errors[0];
        header('Location: dangnhap.php');
        exit;
    }
}
?>