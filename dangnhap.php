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
    <link rel="stylesheet" href="assets/css/pages/dangnhap.css">
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-tabs">
            <div class="auth-tab active" id="login-tab">Đăng nhập</div>
            <div class="auth-tab" id="register-tab">Đăng ký</div>
        </div>

        <!-- Login Form -->
        <div class="auth-form" id="login-form">
            <form id="loginForm" class="needs-validation" novalidate>
                <div class="floating-label">
                    <input type="email" class="form-control" id="login-email" placeholder=" " required>
                    <label for="login-email">Email</label>
                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                </div>

                <div class="floating-label position-relative">
                    <input type="password" class="form-control" id="login-password" placeholder=" " required>
                    <label for="login-password">Mật khẩu</label>
                    <span class="password-toggle" onclick="togglePassword('login-password', this)">
                        <i class="far fa-eye"></i>
                    </span>
                    <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                </div>

                <div class="d-flex justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember-me">
                        <label class="form-check-label" for="remember-me">Ghi nhớ đăng nhập</label>
                    </div>
                    <a href="#" class="text-primary text-decoration-none">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">Đăng nhập</button>
            </form>
        </div>

        <!-- Register Form -->
        <div class="auth-form d-none" id="register-form">
            <form id="registerForm" class="needs-validation" novalidate>
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
                        <input type="text" class="form-control" id="username" placeholder=" " required>
                        <label for="username">Tên đăng nhập</label>
                        <div class="invalid-feedback">Vui lòng nhập tên đăng nhập</div>
                    </div>

                    <div class="floating-label">
                        <input type="text" class="form-control" id="fullname" placeholder=" " required>
                        <label for="fullname">Họ và tên</label>
                        <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                    </div>

                    <div class="floating-label">
                        <input type="date" class="form-control" id="birthdate" placeholder=" ">
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
                        <input type="email" class="form-control" id="register-email" placeholder=" " required>
                        <label for="register-email">Email</label>
                        <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                    </div>

                    <div class="floating-label">
                        <input type="tel" class="form-control" id="phone" placeholder=" " required>
                        <label for="phone">Số điện thoại</label>
                        <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ</div>
                    </div>

                    <div class="floating-label">
                        <textarea class="form-control" id="address" placeholder=" "></textarea>
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
                        <input type="password" class="form-control" id="register-password" placeholder=" " required>
                        <label for="register-password">Mật khẩu</label>
                        <span class="password-toggle" onclick="togglePassword('register-password', this)">
                            <i class="far fa-eye"></i>
                        </span>
                        <div class="invalid-feedback">Vui lòng nhập mật khẩu</div>
                    </div>

                    <div class="floating-label position-relative">
                        <input type="password" class="form-control" id="confirm-password" placeholder=" " required>
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
                        <input class="form-check-input" type="checkbox" id="terms" required>
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

        const showSuccessMessage = (form, message, callback) => {
            const alert = document.createElement('div');
            alert.classList.add('alert', 'alert-success', 'mt-3');
            alert.textContent = message;
            form.appendChild(alert);
            setTimeout(() => {
                alert.remove();
                if (callback) callback();
            }, 2000);
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

        // Login form validation
        document.getElementById('loginForm').addEventListener('submit', e => {
            e.preventDefault();
            const form = e.target;
            const email = document.getElementById('login-email');
            const password = document.getElementById('login-password');
            let isValid = true;

            if (!email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                email.classList.add('is-invalid');
                isValid = false;
            } else {
                email.classList.remove('is-invalid');
                email.classList.add('is-valid');
            }

            if (!password.value) {
                password.classList.add('is-invalid');
                isValid = false;
            } else {
                password.classList.remove('is-invalid');
                password.classList.add('is-valid');
            }

            if (!isValid) return;

            console.log('Login:', { email: email.value, password: password.value });
            showSuccessMessage(form, 'Đăng nhập thành công!', () => {
                // window.location.href = 'dashboard.php';
            });
        });

        // Register form validation
        document.getElementById('registerForm').addEventListener('submit', e => {
            e.preventDefault();
            const form = e.target;
            form.classList.add('was-validated');

            const fields = {
                username: document.getElementById('username'),
                fullname: document.getElementById('fullname'),
                email: document.getElementById('register-email'),
                password: document.getElementById('register-password'),
                confirmPassword: document.getElementById('confirm-password'),
                phone: document.getElementById('phone'),
                birthdate: document.getElementById('birthdate'),
                address: document.getElementById('address'),
                terms: document.getElementById('terms')
            };

            let isValid = true;

            if (fields.password.value !== fields.confirmPassword.value) {
                fields.confirmPassword.classList.add('is-invalid');
                isValid = false;
            } else {
                fields.confirmPassword.classList.remove('is-invalid');
            }

            if (!fields.phone.value.match(/^[0-9]{10}$/)) {
                fields.phone.classList.add('is-invalid');
                isValid = false;
            } else {
                fields.phone.classList.remove('is-invalid');
            }

            if (!form.checkValidity() || !isValid) return;

            const gender = document.querySelector('input[name="gender"]:checked').value;
            console.log('Register:', {
                username: fields.username.value,
                fullname: fields.fullname.value,
                email: fields.email.value,
                password: fields.password.value,
                phone: fields.phone.value,
                birthdate: fields.birthdate.value,
                address: fields.address.value,
                gender,
                terms: fields.terms.checked
            });

            showSuccessMessage(form, 'Đăng ký thành công! Vui lòng kiểm tra email.', () => {
                document.getElementById('login-tab').click();
                document.getElementById('login-email').value = fields.email.value;
                document.getElementById('login-password').value = fields.password.value;
                form.classList.remove('was-validated');
                form.reset();
            });
        });
    </script>
</body>

</html>