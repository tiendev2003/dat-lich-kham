<?php
// Start session if not already started
session_start();

// Kiểm tra nếu người dùng đã đăng nhập, chuyển hướng về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Xử lý form gửi email khôi phục mật khẩu
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    
    // Giả lập kiểm tra email trong database
    if (!empty($email)) {
        // Trong ứng dụng thực tế, sẽ kiểm tra xem email có tồn tại trong DB không
        // Sau đó tạo mã reset, lưu vào DB và gửi email
        // Ở đây chỉ giả lập đã gửi thành công
        $message = '<div class="alert alert-success">
                      <i class="fas fa-check-circle me-2"></i> Chúng tôi đã gửi một email tới '.$email.' với hướng dẫn để đặt lại mật khẩu của bạn. Vui lòng kiểm tra hộp thư (bao gồm cả thư rác).
                    </div>';
    } else {
        $message = '<div class="alert alert-danger">
                      <i class="fas fa-exclamation-circle me-2"></i> Vui lòng nhập email của bạn.
                    </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .forgot-password-container {
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .header-image {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-heading {
            text-align: center;
            margin-bottom: 30px;
            color: #0d6efd;
        }
        .form-text {
            text-align: center;
            margin-bottom: 30px;
            color: #6c757d;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            font-weight: 500;
        }
        .links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .link {
            color: #0d6efd;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
        .auth-logo {
            max-width: 120px;
            margin-bottom: 20px;
        }
        .step-container {
            margin: 30px 0;
        }
        .step-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .step-number {
            width: 30px;
            height: 30px;
            background-color: #0d6efd;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .step-content {
            flex: 1;
        }
        .step-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        .step-description {
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="forgot-password-container">
            <div class="header-image">
                <img src="assets/img/logo-hospital.jpg" alt="Logo" class="auth-logo">
                <h1 class="form-heading">Quên mật khẩu</h1>
                <p class="form-text">Nhập email đăng ký của bạn để nhận hướng dẫn đặt lại mật khẩu</p>
            </div>
            
            <!-- Hiển thị thông báo -->
            <?php if (!empty($message)) echo $message; ?>
            
            <!-- Form quên mật khẩu -->
            <form method="post" action="quenmatkhau.php">
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                    <label for="email">Email đăng ký</label>
                </div>
                <button type="submit" class="btn btn-primary btn-submit">Gửi hướng dẫn đặt lại</button>
            </form>
            
            <div class="step-container">
                <h5 class="text-center mb-3">Quy trình đặt lại mật khẩu</h5>
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Nhập email</div>
                        <div class="step-description">Nhập địa chỉ email bạn đã dùng để đăng ký tài khoản.</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Kiểm tra email</div>
                        <div class="step-description">Chúng tôi sẽ gửi một liên kết đặt lại mật khẩu vào email của bạn.</div>
                    </div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <div class="step-title">Đặt mật khẩu mới</div>
                        <div class="step-description">Nhấp vào liên kết trong email và tạo mật khẩu mới an toàn.</div>
                    </div>
                </div>
            </div>
            
            <div class="links">
                <a href="dangnhap.php" class="link"><i class="fas fa-arrow-left me-2"></i> Quay lại đăng nhập</a>
                <a href="dangky.php" class="link">Đăng ký tài khoản mới <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            
            <div class="mt-4 text-center">
                <p class="small text-muted">
                    Bạn gặp vấn đề? <a href="contact.php" class="link">Liên hệ hỗ trợ</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>