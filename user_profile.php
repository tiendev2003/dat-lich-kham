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
// Fetch upcoming appointments
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT l.*, b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty FROM lichhen l JOIN bacsi b ON l.bacsi_id=b.id JOIN chuyenkhoa c ON b.chuyenkhoa_id=c.id WHERE l.benhnhan_id=? AND l.trang_thai IN ('pending','confirmed') AND l.ngay_hen>=? ORDER BY l.ngay_hen, l.gio_hen");
$stmt->bind_param('is', $patient['id'], $today);
$stmt->execute();
$appointments_upcoming = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// Fetch medical records
$stmt = $conn->prepare("SELECT k.*, l.ngay_hen, b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty FROM ketqua_kham k JOIN lichhen l ON k.lichhen_id=l.id JOIN bacsi b ON l.bacsi_id=b.id JOIN chuyenkhoa c ON b.chuyenkhoa_id=c.id WHERE l.benhnhan_id=? ORDER BY l.ngay_hen DESC");
$stmt->bind_param('i', $patient['id']);
$stmt->execute();
$medical_records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Hệ thống đặt lịch khám bệnh</title>
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
        .profile-tabs {
            margin-bottom: 25px;
        }
        .profile-nav {
            border-bottom: 1px solid #dee2e6;
        }
        .profile-nav .nav-link {
            border: none;
            color: #6c757d;
            padding: 10px 20px;
            font-weight: 500;
        }
        .profile-nav .nav-link.active {
            color: #0d6efd;
            background: transparent;
            border-bottom: 2px solid #0d6efd;
        }
        .info-group {
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
        }
        .appointment-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .appointment-doctor {
            font-weight: 600;
            font-size: 16px;
        }
        .appointment-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-confirmed {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .appointment-details {
            margin-top: 10px;
        }
        .appointment-detail {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .detail-icon {
            min-width: 20px;
            color: #6c757d;
            margin-right: 10px;
        }
        .appointment-actions {
            margin-top: 15px;
        }
        .change-avatar-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #0d6efd;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
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
                            <div class="change-avatar-btn">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <h2 class="profile-name"><?php echo htmlspecialchars($patient['ho_ten']); ?></h2>
                        <p class="profile-info">
                            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($patient['email']); ?><br>
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($patient['dien_thoai']); ?>
                        </p>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action active">
                            <i class="fas fa-user me-2"></i> Thông tin cá nhân
                        </a>
                        <a href="lichsu_datlich.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-check me-2"></i> Lịch sử đặt khám
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-bell me-2"></i> Thông báo
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-lock me-2"></i> Đổi mật khẩu
                        </a>
                        <a href="#" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="profile-content">
                        <ul class="nav nav-tabs profile-nav" id="profileTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                                    Thông tin cá nhân
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="false">
                                    Lịch hẹn sắp tới
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical" type="button" role="tab" aria-controls="medical" aria-selected="false">
                                    Hồ sơ bệnh án
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="profileTabContent">
                            <!-- Thông tin cá nhân -->
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                                    <h4>Thông tin cá nhân</h4>
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i> Chỉnh sửa
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Họ và tên</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['ho_ten']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Ngày sinh</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['nam_sinh']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Giới tính</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['gioi_tinh']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Số CMND/CCCD</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['cmnd_cccd']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Email</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['email']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Số điện thoại</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['dien_thoai']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="info-group">
                                            <div class="info-label">Địa chỉ</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['dia_chi']); ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <h5>Thông tin sức khỏe cơ bản</h5>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <div class="info-label">Nhóm máu</div>
                                                <div class="info-value"><?php echo htmlspecialchars($patient['nhom_mau']); ?></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <div class="info-label">Dị ứng</div>
                                                <div class="info-value"><?php echo htmlspecialchars($patient['di_ung']); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lịch hẹn sắp tới -->
                            <div class="tab-pane fade" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                                <h4 class="mt-3 mb-4">Lịch hẹn sắp tới</h4>
                                
                                <?php if (empty($appointments_upcoming)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Bạn chưa có lịch hẹn nào sắp tới. <a href="datlich.php" class="alert-link">Đặt lịch khám</a> ngay!
                                </div>
                                <?php else: ?>
                                <?php foreach ($appointments_upcoming as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. <?php echo htmlspecialchars($appointment['doctor_name']); ?> - <?php echo htmlspecialchars($appointment['specialty']); ?></div>
                                        <div class="appointment-status status-<?php echo htmlspecialchars($appointment['trang_thai']); ?>">
                                            <?php 
                                            $status_text = '';
                                            switch($appointment['trang_thai']) {
                                                case 'confirmed': $status_text = 'Đã xác nhận'; break;
                                                case 'pending': $status_text = 'Chờ xác nhận'; break;
                                                case 'rescheduled': $status_text = 'Đã đổi lịch'; break;
                                                default: $status_text = $appointment['trang_thai'];
                                            }
                                            echo htmlspecialchars($status_text); 
                                            ?>
                                        </div>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="far fa-calendar-alt"></i></div>
                                            <div>Ngày khám: <?php echo htmlspecialchars(date('d/m/Y', strtotime($appointment['ngay_hen']))); ?></div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="far fa-clock"></i></div>
                                            <div>Giờ khám: <?php echo htmlspecialchars($appointment['gio_hen']); ?></div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám: <?php echo htmlspecialchars($appointment['phong_kham'] ?? get_setting('site_address')); ?></div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                                            <div>Phí khám: <?php echo number_format($appointment['phi_kham'] ?? 0, 0, ',', '.'); ?> VNĐ</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Lý do khám: <?php echo htmlspecialchars($appointment['ly_do']); ?></div>
                                        </div>
                                        <?php if (!empty($appointment['ma_lichhen'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                            <div>Mã lịch hẹn: <?php echo htmlspecialchars($appointment['ma_lichhen']); ?></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary mb-2">
                                            <i class="fas fa-eye"></i> Xem chi tiết
                                        </a>
                                        <?php if ($appointment['trang_thai'] != 'completed'): ?>
                                        <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=reschedule" class="btn btn-sm btn-outline-warning mb-2">
                                            <i class="fas fa-calendar-alt"></i> Thay đổi lịch
                                        </a>
                                        <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=cancel" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times-circle"></i> Hủy lịch hẹn
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>

                                <div class="text-center mt-4">
                                    <a href="datlich.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Đặt lịch khám mới
                                    </a>
                                </div>
                            </div>

                            <!-- Hồ sơ bệnh án -->
                            <div class="tab-pane fade" id="medical" role="tabpanel" aria-labelledby="medical-tab">
                                <h4 class="mt-3 mb-4">Hồ sơ bệnh án</h4>
                                
                                <?php if (empty($medical_records)): ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Hồ sơ bệnh án của bạn sẽ được hiển thị tại đây sau khi bạn đã có ít nhất một lần khám bệnh tại cơ sở y tế.
                                </div>
                                <?php else: ?>
                                <?php foreach ($medical_records as $record): ?>
                                <div class="card mb-3">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($record['ly_do']); ?></strong>
                                            <div class="text-muted small">BS. <?php echo htmlspecialchars($record['doctor_name']); ?> - <?php echo htmlspecialchars(date('d/m/Y', strtotime($record['ngay_hen']))); ?></div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-download"></i> Tải xuống
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong>Chẩn đoán:</strong> <?php echo htmlspecialchars($record['chan_doan']); ?>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Điều trị:</strong> <?php echo htmlspecialchars($record['phuong_phap_dieu_tri']); ?>
                                        </div>
                                        <div>
                                            <strong>Lời dặn:</strong> <?php echo htmlspecialchars($record['loi_dan']); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chỉnh sửa thông tin -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Chỉnh sửa thông tin cá nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($patient['ho_ten']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="birthdate" class="form-label">Ngày sinh</label>
                                <input type="date" class="form-control" id="birthdate" value="<?php echo htmlspecialchars($patient['nam_sinh']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Giới tính</label>
                                <select class="form-select" id="gender">
                                    <option value="nam" <?php echo $patient['gioi_tinh'] === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                                    <option value="nu" <?php echo $patient['gioi_tinh'] === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                                    <option value="khac" <?php echo $patient['gioi_tinh'] === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="id_number" class="form-label">Số CMND/CCCD</label>
                                <input type="text" class="form-control" id="id_number" value="<?php echo htmlspecialchars($patient['cmnd_cccd']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($patient['email']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="phone" value="<?php echo htmlspecialchars($patient['dien_thoai']); ?>">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="address" value="<?php echo htmlspecialchars($patient['dia_chi']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="blood_type" class="form-label">Nhóm máu</label>
                                <select class="form-select" id="blood_type">
                                    <option value="A" <?php echo $patient['nhom_mau'] === 'A' ? 'selected' : ''; ?>>A</option>
                                    <option value="B" <?php echo $patient['nhom_mau'] === 'B' ? 'selected' : ''; ?>>B</option>
                                    <option value="AB" <?php echo $patient['nhom_mau'] === 'AB' ? 'selected' : ''; ?>>AB</option>
                                    <option value="O" <?php echo $patient['nhom_mau'] === 'O' ? 'selected' : ''; ?>>O</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="allergies" class="form-label">Dị ứng</label>
                                <textarea class="form-control" id="allergies" rows="2"><?php echo htmlspecialchars($patient['di_ung']); ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mở modal chỉnh sửa khi nhấn nút
            document.querySelector('#info .btn-outline-primary').addEventListener('click', function() {
                var editModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
                editModal.show();
            });
        });
    </script>
</body>
</html>