<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: dangnhap.php');
    exit;
}

// Get user and patient data
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);

// Fetch login history
$stmt = $conn->prepare("SELECT * FROM dangnhap_logs WHERE user_id = ? ORDER BY thoi_gian DESC LIMIT 50");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$login_history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đăng nhập - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            padding: 40px 0;
        }
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .profile-header {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 15px;
            overflow: hidden;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin: 10px 0 5px;
        }
        .profile-info {
            font-size: 14px;
            color: #6c757d;
        }
        .profile-content {
            padding: 30px;
        }
        .login-history-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        .login-history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .login-history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .login-history-date {
            font-weight: 600;
            font-size: 16px;
        }
        .login-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .login-details {
            margin-top: 10px;
        }
        .login-detail {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .detail-icon {
            min-width: 20px;
            color: #6c757d;
            margin-right: 10px;
        }
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline-item {
            position: relative;
            padding-left: 40px;
            padding-bottom: 20px;
        }
        .timeline-item:before {
            content: "";
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }
        .timeline-item:last-child:before {
            bottom: 50%;
        }
        .timeline-item:after {
            content: "";
            position: absolute;
            left: 4px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #0d6efd;
            border: 3px solid #fff;
            box-shadow: 0 0 0 2px #0d6efd;
        }
        .timeline-date {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .no-history {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        .no-history i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container profile-container">
        <div class="row">
            <div class="col-lg-4">
                <div class="profile-card mb-4">
                    <div class="profile-header">
                        <div class="position-relative d-inline-block">
                            <div class="profile-avatar">
                                <img src="assets/img/user-avatar.png" alt="Ảnh đại diện">
                            </div>
                        </div>
                        <h2 class="profile-name"><?php echo htmlspecialchars($patient['ho_ten']); ?></h2>
                        <p class="profile-info">
                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($patient['email']); ?><br>
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($patient['dien_thoai']); ?>
                        </p>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="user_profile.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Thông tin cá nhân
                        </a>
                        <a href="lichsu_datlich.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-check me-2"></i> Lịch sử đặt khám
                        </a>
                        <a href="lichsu_dangnhap.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-sign-in-alt me-2"></i> Lịch sử đăng nhập
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-bell me-2"></i> Thông báo
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-lock me-2"></i> Đổi mật khẩu
                        </a>
                        <a href="logout.php" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="profile-content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4><i class="fas fa-history me-2"></i>Lịch sử đăng nhập</h4>
                            <span class="badge bg-primary"><?php echo count($login_history); ?> phiên đăng nhập</span>
                        </div>

                        <?php if (empty($login_history)): ?>
                        <div class="no-history">
                            <i class="fas fa-history d-block"></i>
                            <h5>Không có lịch sử đăng nhập</h5>
                            <p>Lịch sử đăng nhập của bạn sẽ được hiển thị tại đây.</p>
                        </div>
                        <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($login_history as $index => $login): ?>
                            <div class="timeline-item">
                                <div class="timeline-date">
                                    <?php echo date('d/m/Y - H:i:s', strtotime($login['thoi_gian'])); ?>
                                    <?php if ($index === 0): ?>
                                    <span class="badge bg-success ms-2">Hiện tại</span>
                                    <?php endif; ?>
                                </div>
                                <div class="login-history-card">
                                    <div class="login-history-header">
                                        <div>
                                            <i class="<?php echo $login['trang_thai'] == 'success' ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger'; ?>"></i>
                                            <span class="ms-2"><?php echo $login['trang_thai'] == 'success' ? 'Đăng nhập thành công' : 'Đăng nhập thất bại'; ?></span>
                                        </div>
                                        <div class="login-status <?php echo $login['trang_thai'] == 'success' ? 'status-success' : 'status-failed'; ?>">
                                            <?php echo $login['trang_thai'] == 'success' ? 'Thành công' : 'Thất bại'; ?>
                                        </div>
                                    </div>
                                    <div class="login-details">
                                        <div class="login-detail">
                                            <div class="detail-icon"><i class="fas fa-desktop"></i></div>
                                            <div>Thiết bị: <?php echo htmlspecialchars($login['thiet_bi'] ?? 'Không xác định'); ?></div>
                                        </div>
                                        <div class="login-detail">
                                            <div class="detail-icon"><i class="fas fa-globe"></i></div>
                                            <div>Trình duyệt: <?php echo htmlspecialchars($login['trinh_duyet'] ?? 'Không xác định'); ?></div>
                                        </div>
                                        <div class="login-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Địa chỉ IP: <?php echo htmlspecialchars($login['ip_address'] ?? 'Không xác định'); ?></div>
                                        </div>
                                        <?php if (isset($login['ghi_chu']) && !empty($login['ghi_chu'])): ?>
                                        <div class="login-detail">
                                            <div class="detail-icon"><i class="fas fa-info-circle"></i></div>
                                            <div>Ghi chú: <?php echo htmlspecialchars($login['ghi_chu']); ?></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Phần thông tin bảo mật -->
                <div class="profile-card mt-4">
                    <div class="profile-content">
                        <h5 class="mb-4"><i class="fas fa-shield-alt me-2"></i>Bảo mật tài khoản</h5>
                        
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Mẹo bảo mật:</strong> Thường xuyên kiểm tra lịch sử đăng nhập để phát hiện các hoạt động đáng ngờ.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-lock me-2"></i>Mật khẩu mạnh</h6>
                                        <p class="card-text small">Sử dụng mật khẩu phức tạp với ít nhất 8 ký tự bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</p>
                                        <a href="#" class="btn btn-sm btn-outline-primary">Đổi mật khẩu</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-bell me-2"></i>Thông báo đăng nhập</h6>
                                        <p class="card-text small">Nhận email thông báo khi có đăng nhập mới vào tài khoản của bạn.</p>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="loginNotifications" checked>
                                            <label class="form-check-label" for="loginNotifications">Bật thông báo</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>