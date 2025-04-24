<?php
// Bắt đầu session
session_start();

// Kiểm tra nếu người dùng đã đăng nhập
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (is_logged_in()) {
    redirect_by_role($_SESSION['vai_tro']);
}

// Lấy thông báo lỗi/thành công nếu có
$login_error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$register_error = isset($_SESSION['register_error']) ? $_SESSION['register_error'] : '';
$register_success = isset($_SESSION['register_success']) ? $_SESSION['register_success'] : '';

// Xóa thông báo sau khi hiển thị
unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['register_success']);

// Kiểm tra cookie ghi nhớ đăng nhập
$remembered_email = isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký & Đăng nhập</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2a9d8f;
            --secondary-color: #264653;
            --accent-color: #e9c46a;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 500px;
            margin: 1rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .auth-tabs {
            display: flex;
            background: #f1f3f5;
        }

        .auth-tab {
            flex: 1;
            padding: 1.2rem;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
            color: var(--dark-color);
            transition: all 0.3s ease;
        }

        .auth-tab.active {
            background: white;
            color: var(--primary-color);
            font-weight: 600;
            position: relative;
        }

        .auth-tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--primary-color);
        }

        .auth-tab:hover:not(.active) {
            background: #e9ecef;
        }

        .auth-form {
            padding: 2rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .form-control {
            border-radius: 8px;
            padding: 0.8rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(42, 157, 143, 0.25);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #21867a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(42, 157, 143, 0.3);
        }

        .social-login .btn {
            border-radius: 8px;
            padding: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            color: #6c757d;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-top: 1px solid #dee2e6;
        }

        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .floating-label input {
            height: 56px;
            padding-top: 1.5rem;
            font-size: 1rem;
        }

        .floating-label label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #6c757d;
            transition: all 0.2s ease;
            pointer-events: none;
        }

        .floating-label input:focus+label,
        .floating-label input:not(:placeholder-shown)+label {
            top: 10px;
            font-size: 0.75rem;
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: var(--dark-color);
        }

        .gender-container {
            margin-bottom: 1.5rem;
        }

        .gender-options {
            display: flex;
            gap: 1rem;
        }

        .gender-option input[type="radio"] {
            display: none;
        }

        .gender-option label {
            flex: 1;
            padding: 0.8rem;
            text-align: center;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .gender-option input:checked+label {
            border-color: var(--primary-color);
            background: rgba(42, 157, 143, 0.1);
            color: var(--primary-color);
        }

        .invalid-feedback {
            font-size: 0.875rem;
        }

        .alert-success {
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @media (max-width: 576px) {
            .auth-wrapper {
                margin: 0.5rem;
            }

            .auth-form {
                padding: 1.5rem;
            }
        }

        /* Multi-step form styles */
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .form-nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }

        .step-progress {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .step-indicator {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f1f3f5;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 8px;
            position: relative;
            transition: all 0.3s ease;
        }

        .step-indicator.active {
            background: var(--primary-color);
            color: white;
        }

        .step-indicator.completed {
            background: var(--primary-color);
            color: white;
        }

        .step-indicator.completed::after {
            content: '✓';
        }

        .step-connector {
            flex: 1;
            height: 4px;
            background: #e9ecef;
            margin-top: 20px;
        }

        .step-connector.active {
            background: var(--primary-color);
        }

        @media (max-width: 576px) {
            .step-indicator {
                width: 30px;
                height: 30px;
                margin: 0 5px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-tabs">
            <div class="auth-tab active" id="login-tab">Đăng nhập</div>
            <div class="auth-tab" id="register-tab">Đăng ký</div>
        </div>

        <!-- Login Form -->
        <div class="auth-form" id="login-form">
            <?php if (!empty($login_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $login_error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($register_success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $register_success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" class="needs-validation" action="process_login.php" method="POST" novalidate>
                <div class="floating-label">
                    <input type="email" class="form-control" id="login-email" name="email" placeholder=" " value="<?php echo $remembered_email; ?>" required>
                    <label for="login-email">Email</label>
                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                </div>

                <div class="floating-label position-relative">
                    <input type="password" class="form-control" id="login-password" name="password" placeholder=" " required>
                    <label for="login-password">Mật khẩu</label>
                    <span class="password-toggle" onclick="togglePassword('login-password', this)">
                        <i class="far fa-eye"></i>
                    </span>
                    <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember-me" name="remember" <?php echo !empty($remembered_email) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="remember-me">Ghi nhớ đăng nhập</label>
                    </div>
                    <a href="quenmatkhau.php" class="text-primary text-decoration-none">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Đăng nhập</button>
                
                <div id="loginMessages"></div>
            </form>
        </div>

        <!-- Register Form -->
        <div class="auth-form d-none" id="register-form">
            <?php if (!empty($register_error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $register_error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form id="registerForm" class="needs-validation" action="process_register.php" method="POST" novalidate>
                <!-- Step Progress Indicators -->
                <div class="step-progress">
                    <div class="step-indicator active" data-step="1">1</div>
                    <div class="step-connector"></div>
                    <div class="step-indicator" data-step="2">2</div>
                    <div class="step-connector"></div>
                    <div class="step-indicator" data-step="3">3</div>
                    <div class="step-connector"></div>
                    <div class="step-indicator" data-step="4">4</div>
                </div>

                <!-- Step 1: Basic Info -->
                <div class="form-step active" data-step="1">
                    <h5 class="mb-3">Thông tin cơ bản</h5>
                    <div class="floating-label">
                        <input type="text" class="form-control" id="username" name="username" placeholder=" " required>
                        <label for="username">Tên đăng nhập</label>
                        <div class="invalid-feedback">Vui lòng nhập tên đăng nhập</div>
                    </div>

                    <div class="floating-label">
                        <input type="text" class="form-control" id="fullname" name="fullname" placeholder=" " required>
                        <label for="fullname">Họ và tên</label>
                        <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                    </div>

                    <div class="floating-label">
                        <input type="date" class="form-control" id="birthdate" name="birthdate" placeholder=" ">
                        <label for="birthdate" class="date-label">Ngày sinh</label>
                    </div>

                    <div class="form-nav-buttons">
                        <div></div> <!-- Empty div for space -->
                        <button type="button" class="btn btn-primary next-btn">Tiếp tục</button>
                    </div>
                </div>

                <!-- Step 2: Contact Info -->
                <div class="form-step" data-step="2">
                    <h5 class="mb-3">Thông tin liên hệ</h5>
                    <div class="floating-label">
                        <input type="email" class="form-control" id="register-email" name="email" placeholder=" " required>
                        <label for="register-email">Email</label>
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                    </div>

                    <div class="floating-label">
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder=" " required>
                        <label for="phone">Số điện thoại</label>
                        <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ</div>
                    </div>

                    <div class="floating-label">
                        <textarea class="form-control" id="address" name="address" placeholder=" "></textarea>
                        <label for="address">Địa chỉ</label>
                    </div>

                    <div class="form-nav-buttons">
                        <button type="button" class="btn btn-outline-secondary prev-btn">Quay lại</button>
                        <button type="button" class="btn btn-primary next-btn">Tiếp tục</button>
                    </div>
                </div>

                <!-- Step 3: Security -->
                <div class="form-step" data-step="3">
                    <h5 class="mb-3">Bảo mật</h5>
                    <div class="floating-label position-relative">
                        <input type="password" class="form-control" id="register-password" name="password" placeholder=" " required>
                        <label for="register-password">Mật khẩu</label>
                        <span class="password-toggle" onclick="togglePassword('register-password', this)">
                            <i class="far fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                    </div>

                    <div class="floating-label position-relative">
                        <input type="password" class="form-control" id="confirm-password" name="confirm_password" placeholder=" " required>
                        <label for="confirm-password">Nhập lại mật khẩu</label>
                        <span class="password-toggle" onclick="togglePassword('confirm-password', this)">
                            <i class="far fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">Mật khẩu không khớp</div>
                    </div>

                    <div class="form-nav-buttons">
                        <button type="button" class="btn btn-outline-secondary prev-btn">Quay lại</button>
                        <button type="button" class="btn btn-primary next-btn">Tiếp tục</button>
                    </div>
                </div>

                <!-- Step 4: Final Info -->
                <div class="form-step" data-step="4">
                    <h5 class="mb-3">Hoàn tất đăng ký</h5>
                    <div class="gender-container">
                        <label class="form-label">Giới tính</label>
                        <div class="gender-options">
                            <div class="gender-option">
                                <input type="radio" name="gender" id="male" value="Nam" checked required>
                                <label for="male"><i class="fas fa-mars me-1"></i> Nam</label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" name="gender" id="female" value="Nữ">
                                <label for="female"><i class="fas fa-venus me-1"></i> Nữ</label>
                            </div>
                            <div class="gender-option">
                                <input type="radio" name="gender" id="other" value="Khác">
                                <label for="other"><i class="fas fa-genderless me-1"></i> Khác</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            Tôi đồng ý với <a href="#" class="text-decoration-none">Điều khoản dịch vụ</a> và
                            <a href="#" class="text-decoration-none">Chính sách bảo mật</a>
                        </label>
                        <div class="invalid-feedback">Bạn phải đồng ý với điều khoản</div>
                    </div>

                    <div class="form-nav-buttons">
                        <button type="button" class="btn btn-outline-secondary prev-btn">Quay lại</button>
                        <button type="submit" class="btn btn-primary">Đăng ký tài khoản</button>
                    </div>
                </div>
            </form>
            <!-- Thông báo cho form đăng ký - đặt ở ngoài các bước -->
            <div id="registerMessages"></div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Utility functions
        const togglePassword = (inputId, icon) => {
            const input = document.getElementById(inputId);
            const iconElement = icon.querySelector('i');
            input.type = input.type === 'password' ? 'text' : 'password';
            iconElement.classList.toggle('fa-eye');
            iconElement.classList.toggle('fa-eye-slash');
        };

        // Function to display alert messages
        const showAlert = (container, message, type) => {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Clear previous alerts
            container.innerHTML = '';
            container.appendChild(alertDiv);
            
            // Auto dismiss after 5 seconds
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alertDiv);
                bsAlert.close();
            }, 5000);
        };

        // Tab switching
        const tabs = {
            login: { tab: 'login-tab', form: 'login-form' },
            register: { tab: 'register-tab', form: 'register-form' }
        };

        Object.values(tabs).forEach(({ tab, form }) => {
            document.getElementById(tab).addEventListener('click', () => {
                Object.values(tabs).forEach(t => {
                    document.getElementById(t.tab).classList.toggle('active', t.tab === tab);
                    document.getElementById(t.form).classList.toggle('d-none', t.form !== form);
                });
                document.querySelectorAll('.form-control').forEach(input =>
                    input.classList.remove('is-invalid', 'is-valid'));
                document.getElementById('loginForm').reset();
                document.getElementById('registerForm').reset();
                document.getElementById('registerForm').classList.remove('was-validated');

                // Clear any existing alerts
                document.getElementById('loginMessages').innerHTML = '';
                document.getElementById('registerMessages').innerHTML = '';

                // Reset form steps if switching to register tab
                if (tab === 'register-tab') {
                    resetFormSteps();
                }
            });
        });

        // Multi-step form functionality
        const resetFormSteps = () => {
            // Hide all steps and show only the first one
            document.querySelectorAll('.form-step').forEach(step => {
                step.classList.remove('active');
            });
            document.querySelector('.form-step[data-step="1"]').classList.add('active');

            // Reset step indicators
            updateStepIndicators(1);
        };

        const updateStepIndicators = (currentStep) => {
            const indicators = document.querySelectorAll('.step-indicator');
            const connectors = document.querySelectorAll('.step-connector');

            indicators.forEach(indicator => {
                const step = parseInt(indicator.getAttribute('data-step'));
                indicator.classList.remove('active', 'completed');

                if (step === currentStep) {
                    indicator.classList.add('active');
                } else if (step < currentStep) {
                    indicator.classList.add('completed');
                }
            });

            connectors.forEach((connector, index) => {
                connector.classList.toggle('active', index < currentStep - 1);
            });
        };

        // Navigate between steps
        document.querySelectorAll('.next-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const currentStep = parseInt(e.target.closest('.form-step').getAttribute('data-step'));
                const nextStep = currentStep + 1;

                // Validate current step fields
                let isStepValid = validateStep(currentStep);

                if (isStepValid) {
                    // Hide current step
                    document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');

                    // Show next step
                    document.querySelector(`.form-step[data-step="${nextStep}"]`).classList.add('active');

                    // Update indicators
                    updateStepIndicators(nextStep);
                }
            });
        });

        document.querySelectorAll('.prev-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const currentStep = parseInt(e.target.closest('.form-step').getAttribute('data-step'));
                const prevStep = currentStep - 1;

                // Hide current step
                document.querySelector(`.form-step[data-step="${currentStep}"]`).classList.remove('active');

                // Show previous step
                document.querySelector(`.form-step[data-step="${prevStep}"]`).classList.add('active');

                // Update indicators
                updateStepIndicators(prevStep);
            });
        });

        const validateStep = (stepNumber) => {
            const stepElement = document.querySelector(`.form-step[data-step="${stepNumber}"]`);
            const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }

                // Special validation for email
                if (input.type === 'email' && input.value) {
                    if (!input.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    }
                }

                // Special validation for phone
                if (input.id === 'phone' && input.value) {
                    if (!input.value.match(/^[0-9]{10}$/)) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    }
                }
            });

            // Password matching validation in step 3
            if (stepNumber === 3) {
                const password = document.getElementById('register-password');
                const confirmPassword = document.getElementById('confirm-password');

                if (password.value !== confirmPassword.value) {
                    confirmPassword.classList.add('is-invalid');
                    isValid = false;
                }
            }

            return isValid;
        };

        // AJAX form submission for better UX
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const messagesContainer = document.getElementById('loginMessages');
            
            // Validate form
            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }
            
            // Show loading message
            showAlert(messagesContainer, 'Đang xử lý đăng nhập...', 'info');
            
            // Get form data
            const formData = new FormData(form);
            
            // Send AJAX request
            fetch('process_login.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(messagesContainer, data.message || 'Đăng nhập thành công! Đang chuyển hướng...', 'success');
                    setTimeout(() => {
                        window.location.href = data.redirect || 'index.php';
                    }, 1500);
                } else {
                    showAlert(messagesContainer, data.message || 'Đăng nhập thất bại. Vui lòng kiểm tra thông tin đăng nhập.', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert(messagesContainer, 'Có lỗi xảy ra khi xử lý đăng nhập.', 'danger');
            });
        });
        
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const messagesContainer = document.getElementById('registerMessages');
            
            // Final validation
            if (!validateAllSteps()) {
                return;
            }
            
            // Show loading message
            showAlert(messagesContainer, 'Đang xử lý đăng ký...', 'info');
            
            // Get form data
            const formData = new FormData(form);
            
            // Send AJAX request
            fetch('process_register.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(messagesContainer, data.message || 'Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.', 'success');
                    setTimeout(() => {
                        // Switch to login tab after successful registration
                        document.getElementById('login-tab').click();
                        // Auto-fill email field in login form
                        document.getElementById('login-email').value = document.getElementById('register-email').value;
                        form.reset();
                    }, 1500);
                } else {
                    showAlert(messagesContainer, data.message || 'Đăng ký thất bại. Vui lòng kiểm tra thông tin.', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert(messagesContainer, 'Có lỗi xảy ra khi xử lý đăng ký.', 'danger');
            });
        });
        
        function validateAllSteps() {
            let isValid = true;
            
            // Validate each step
            for (let i = 1; i <= 4; i++) {
                if (!validateStep(i)) {
                    // Go to the first invalid step
                    if (isValid) {
                        document.querySelectorAll('.form-step').forEach(step => {
                            step.classList.remove('active');
                        });
                        document.querySelector(`.form-step[data-step="${i}"]`).classList.add('active');
                        updateStepIndicators(i);
                    }
                    isValid = false;
                }
            }
            
            return isValid;
        }
    </script>
</body>

</html>