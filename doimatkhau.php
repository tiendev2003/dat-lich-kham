<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    header('Location: dangnhap.php?redirect=doimatkhau.php');
    exit;
}

// Lấy thông tin người dùng
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);

// Lấy thông báo
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Thiết lập tiêu đề trang
$GLOBALS['page_title'] = 'Đổi mật khẩu';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <!-- Inline styles for password toggle and requirements -->
    <style>
        .password-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10; color: #6c757d; }
        .form-floating { position: relative; }
        .password-requirements { font-size: 13px; color: #6c757d; margin-top: 10px; }
        .requirement-item { display: flex; align-items: center; margin-bottom: 5px; }
        .requirement-item i { margin-right: 5px; font-size: 12px; }
        .requirement-met { color: #198754; }
        .requirement-not-met { color: #6c757d; }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3">
                <?php include 'includes/user_sidebar.php'; ?>
            </div>
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-lock me-2"></i>Đổi mật khẩu</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                        </div>
                        <?php endif; ?>
                        
                        <form id="changePasswordForm" action="process_changepassword.php" method="POST" class="mt-4">
                            <div class="row justify-content-center">
                                <div class="col-lg-8">
                                    <div class="mb-4 form-floating">
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <label for="current_password">Mật khẩu hiện tại</label>
                                        <div class="password-toggle" onclick="togglePasswordVisibility('current_password', this)">
                                            <i class="far fa-eye"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4 form-floating">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        <label for="new_password">Mật khẩu mới</label>
                                        <div class="password-toggle" onclick="togglePasswordVisibility('new_password', this)">
                                            <i class="far fa-eye"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4 form-floating">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        <label for="confirm_password">Xác nhận mật khẩu mới</label>
                                        <div class="password-toggle" onclick="togglePasswordVisibility('confirm_password', this)">
                                            <i class="far fa-eye"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="password-requirements">
                                        <p class="mb-2 fw-medium">Yêu cầu mật khẩu:</p>
                                        <div class="requirement-item" id="length-req">
                                            <i class="far fa-circle"></i> Ít nhất 8 ký tự
                                        </div>
                                        <div class="requirement-item" id="letter-req">
                                            <i class="far fa-circle"></i> Ít nhất một chữ cái
                                        </div>
                                        <div class="requirement-item" id="number-req">
                                            <i class="far fa-circle"></i> Ít nhất một chữ số
                                        </div>
                                        <div class="requirement-item" id="diff-req">
                                            <i class="far fa-circle"></i> Khác mật khẩu hiện tại
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Cập nhật mật khẩu
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hàm hiện/ẩn mật khẩu
        function togglePasswordVisibility(inputId, toggleElement) {
            const input = document.getElementById(inputId);
            const icon = toggleElement.querySelector('i');

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Kiểm tra độ mạnh của mật khẩu và hiển thị
        document.addEventListener('DOMContentLoaded', function() {
            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            
            const lengthReq = document.getElementById('length-req');
            const letterReq = document.getElementById('letter-req');
            const numberReq = document.getElementById('number-req');
            const diffReq = document.getElementById('diff-req');
            
            // Form validation
            const form = document.getElementById('changePasswordForm');
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
                    event.preventDefault();
                } else {
                    confirmPassword.setCustomValidity('');
                }
                
                form.classList.add('was-validated');
            });
            
            // Live password validation
            newPassword.addEventListener('input', validatePassword);
            currentPassword.addEventListener('input', validatePassword);
            
            confirmPassword.addEventListener('input', function() {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });
            
            function validatePassword() {
                const password = newPassword.value;
                
                // Check length
                if (password.length >= 8) {
                    updateRequirement(lengthReq, true);
                } else {
                    updateRequirement(lengthReq, false);
                }
                
                // Check for letter
                if (/[a-zA-Z]/.test(password)) {
                    updateRequirement(letterReq, true);
                } else {
                    updateRequirement(letterReq, false);
                }
                
                // Check for number
                if (/\d/.test(password)) {
                    updateRequirement(numberReq, true);
                } else {
                    updateRequirement(numberReq, false);
                }
                
                // Check if different from current
                if (password && password !== currentPassword.value && currentPassword.value) {
                    updateRequirement(diffReq, true);
                } else {
                    updateRequirement(diffReq, false);
                }
            }
            
            function updateRequirement(element, isValid) {
                const icon = element.querySelector('i');
                
                if (isValid) {
                    element.classList.add('requirement-met');
                    element.classList.remove('requirement-not-met');
                    icon.classList.remove('far', 'fa-circle');
                    icon.classList.add('fas', 'fa-check-circle');
                } else {
                    element.classList.remove('requirement-met');
                    element.classList.add('requirement-not-met');
                    icon.classList.remove('fas', 'fa-check-circle');
                    icon.classList.add('far', 'fa-circle');
                }
            }
        });
    </script>
</body>

</html>

