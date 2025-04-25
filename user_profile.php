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

// Get any profile update messages
$profile_message = null;
if (isset($_SESSION['profile_message'])) {
    $profile_message = $_SESSION['profile_message'];
    unset($_SESSION['profile_message']);
}

// Thiết lập tiêu đề trang
$GLOBALS['page_title'] = 'Thông tin cá nhân';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <?php include 'includes/head.php'; ?>
    <!-- Inline profile page styles -->
    <style>
        .profile-container { padding: 40px 0; }
        .profile-card { border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .profile-header { background-color: #f8f9fa; padding: 30px; text-align: center; border-bottom: 1px solid #eee; }
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
            padding: 20px;
            margin-bottom: 20px;
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
            margin-bottom: 15px;
        }

        .appointment-doctor {
            font-weight: 600;
            font-size: 18px;
        }

        .appointment-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-confirmed {
            background-color: #cfe2ff;
            color: #084298;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .status-rescheduled {
            background-color: #e2e3e5;
            color: #41464b;
        }

        .appointment-date {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .appointment-details {
            margin: 15px 0;
        }

        .appointment-detail {
            display: flex;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .detail-icon {
            min-width: 20px;
            color: #6c757d;
            margin-right: 10px;
        }

        .appointment-actions {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
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

    <div class="container py-5">
        <div class="row">
            <div class="col-md-3">
                <?php include 'includes/user_sidebar.php'; ?>
            </div>
            <div class="col-md-9">
                <?php if ($profile_message): ?>
                    <div class="alert alert-<?php echo $profile_message['type']; ?> alert-dismissible fade show"
                        role="alert">
                        <?php echo $profile_message['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="profile-card">
                    <div class="profile-content">
                        <ul class="nav nav-tabs profile-nav" id="profileTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab"
                                    data-bs-target="#info" type="button" role="tab" aria-controls="info"
                                    aria-selected="true">
                                    Thông tin cá nhân
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="appointments-tab" data-bs-toggle="tab"
                                    data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments"
                                    aria-selected="false">
                                    Lịch hẹn sắp tới
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical"
                                    type="button" role="tab" aria-controls="medical" aria-selected="false">
                                    Hồ sơ bệnh án
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="profileTabContent">
                            <!-- Thông tin cá nhân -->
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                                    <h4>Thông tin cá nhân</h4>
                                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editProfileModal">
                                        <i class="fas fa-edit"></i> Chỉnh sửa
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Họ và tên</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['ho_ten']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Ngày sinh</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($patient['nam_sinh']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Giới tính</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($patient['gioi_tinh']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Số CMND/CCCD</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($patient['cmnd_cccd']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Email</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <div class="info-label">Số điện thoại</div>
                                            <div class="info-value">
                                                <?php echo htmlspecialchars($patient['dien_thoai']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="info-group">
                                            <div class="info-label">Địa chỉ</div>
                                            <div class="info-value"><?php echo htmlspecialchars($patient['dia_chi']); ?>
                                            </div>
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
                                                <div class="info-value">
                                                    <?php echo htmlspecialchars($patient['nhom_mau'] ?: 'Chưa cập nhật'); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <div class="info-label">Dị ứng</div>
                                                <div class="info-value">
                                                    <?php echo htmlspecialchars($patient['di_ung'] ?: 'Không có'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lịch hẹn sắp tới -->
                            <div class="tab-pane fade" id="appointments" role="tabpanel"
                                aria-labelledby="appointments-tab">
                                <h4 class="mt-3 mb-4">Lịch hẹn sắp tới</h4>

                                <?php if (empty($appointments_upcoming)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Bạn chưa có lịch hẹn nào sắp tới. <a
                                            href="datlich.php" class="alert-link">Đặt lịch khám</a> ngay!
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($appointments_upcoming as $appointment): ?>
                                        <div class="appointment-card">
                                            <div class="appointment-header">
                                                <div class="appointment-doctor">BS.
                                                    <?php echo htmlspecialchars($appointment['doctor_name']); ?> -
                                                    <?php echo htmlspecialchars($appointment['specialty']); ?></div>
                                                <div
                                                    class="appointment-status status-<?php echo htmlspecialchars($appointment['trang_thai']); ?>">
                                                    <?php
                                                    $status_text = '';
                                                    switch ($appointment['trang_thai']) {
                                                        case 'confirmed':
                                                            $status_text = 'Đã xác nhận';
                                                            break;
                                                        case 'pending':
                                                            $status_text = 'Chờ xác nhận';
                                                            break;
                                                        case 'rescheduled':
                                                            $status_text = 'Đã đổi lịch';
                                                            break;
                                                        default:
                                                            $status_text = ucfirst($appointment['trang_thai']);
                                                    }
                                                    echo htmlspecialchars($status_text);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="appointment-date">
                                                <i class="far fa-calendar-alt me-2"></i>
                                                <?php echo date('l, d/m/Y', strtotime($appointment['ngay_hen'])); ?>
                                                <i class="far fa-clock ms-3 me-2"></i>
                                                <?php echo htmlspecialchars($appointment['gio_hen']); ?>
                                            </div>
                                            <div class="appointment-details">
                                                <div class="appointment-detail">
                                                    <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                                    <div>Dịch vụ: <span
                                                            class="fw-medium"><?php echo htmlspecialchars($appointment['service'] ?? 'Khám bệnh'); ?></span>
                                                    </div>
                                                </div>
                                                <div class="appointment-detail">
                                                    <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                                    <div>Địa điểm: <span
                                                            class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi'] ?? 'Chưa cập nhật'); ?></span>
                                                    </div>
                                                </div>
                                                <?php if (!empty($appointment['phi_kham'])): ?>
                                                    <div class="appointment-detail">
                                                        <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                                                        <div>Phí khám: <span
                                                                class="fw-medium"><?php echo number_format($appointment['phi_kham'], 0, ',', '.'); ?>
                                                                VNĐ</span></div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($appointment['ma_lichhen'])): ?>
                                                    <div class="appointment-detail">
                                                        <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                                        <div>Mã lịch hẹn: <span
                                                                class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($appointment['ly_do'])): ?>
                                                    <div class="appointment-detail">
                                                        <div class="detail-icon"><i class="fas fa-comment-alt"></i></div>
                                                        <div>Lý do khám: <span
                                                                class="fst-italic"><?php echo htmlspecialchars($appointment['ly_do']); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($appointment['thanh_toan'])): ?>
                                                    <div class="appointment-detail">
                                                        <div class="detail-icon"><i class="fas fa-credit-card"></i></div>
                                                        <div>Thanh toán:
                                                            <span class="fw-medium">
                                                                <?php
                                                                $payment_status = '';
                                                                switch ($appointment['thanh_toan']) {
                                                                    case 'paid':
                                                                        $payment_status = '<span class="text-success">Đã thanh toán</span>';
                                                                        break;
                                                                    case 'unpaid':
                                                                        $payment_status = '<span class="text-danger">Chưa thanh toán</span>';
                                                                        break;
                                                                    case 'partial':
                                                                        $payment_status = '<span class="text-warning">Thanh toán một phần</span>';
                                                                        break;
                                                                    default:
                                                                        $payment_status = htmlspecialchars($appointment['thanh_toan']);
                                                                }
                                                                echo $payment_status;
                                                                ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($appointment['ghi_chu'])): ?>
                                                    <div class="appointment-detail">
                                                        <div class="detail-icon"><i class="fas fa-sticky-note"></i></div>
                                                        <div>Ghi chú: <span
                                                                class="fst-italic"><?php echo htmlspecialchars($appointment['ghi_chu']); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="appointment-actions">
                                                <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </a>
                                                <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=reschedule"
                                                    class="btn btn-sm btn-outline-warning">
                                                    <i class="fas fa-edit"></i> Thay đổi lịch
                                                </a>
                                                <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=cancel"
                                                    class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-times"></i> Hủy lịch hẹn
                                                </a>
                                                <?php if (!isset($appointment['thanh_toan']) || $appointment['thanh_toan'] !== 'paid'): ?>
                                                    <a href="thanhtoan.php?id=<?php echo $appointment['id']; ?>"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-credit-card"></i> Thanh toán
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
                                    <a href="lichsu_datlich.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-history me-1"></i> Xem tất cả lịch sử
                                    </a>
                                </div>
                            </div>

                            <!-- Hồ sơ bệnh án -->
                            <div class="tab-pane fade" id="medical" role="tabpanel" aria-labelledby="medical-tab">
                                <h4 class="mt-3 mb-4">Hồ sơ bệnh án</h4>

                                <?php if (empty($medical_records)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Hồ sơ bệnh án của bạn sẽ được hiển thị tại đây
                                        sau khi bạn đã có ít nhất một lần khám bệnh tại cơ sở y tế.
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($medical_records as $record): ?>
                                        <div class="card mb-3">
                                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($record['ly_do'] ?? 'Khám bệnh'); ?></strong>
                                                    <div class="text-muted small">BS.
                                                        <?php echo htmlspecialchars($record['doctor_name']); ?> -
                                                        <?php echo htmlspecialchars(date('d/m/Y', strtotime($record['ngay_hen']))); ?>
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary view-record"
                                                        data-bs-toggle="modal" data-bs-target="#viewMedicalRecordModal"
                                                        data-id="<?php echo $record['id']; ?>"
                                                        data-reason="<?php echo htmlspecialchars($record['ly_do'] ?? 'Khám bệnh'); ?>"
                                                        data-doctor="<?php echo htmlspecialchars($record['doctor_name']); ?>"
                                                        data-date="<?php echo htmlspecialchars(date('d/m/Y', strtotime($record['ngay_hen']))); ?>"
                                                        data-diagnosis="<?php echo htmlspecialchars($record['chan_doan']); ?>"
                                                        data-treatment="<?php echo htmlspecialchars($record['mo_ta'] ?? ''); ?>"
                                                        data-prescription="<?php echo htmlspecialchars($record['don_thuoc'] ?? ''); ?>"
                                                        data-notes="<?php echo htmlspecialchars($record['loi_dan'] ?? ''); ?>">
                                                        <i class="fas fa-eye"></i> Xem chi tiết
                                                    </button>
                                                    <a href="download_medical_record.php?id=<?php echo $record['id']; ?>"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-file-download"></i> Tải xuống
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong>Chẩn đoán:</strong>
                                                    <?php echo htmlspecialchars($record['chan_doan']); ?>
                                                </div>
                                                <?php if (!empty($record['mo_ta'])): ?>
                                                    <div class="mb-3 d-flex align-items-center gap-2">
                                                        <strong>Kết quả khám:</strong>
                                                        <span class="text-truncate d-inline-block"
                                                            style="max-width: 300px;"><?php echo htmlspecialchars($record['mo_ta']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($record['don_thuoc'])): ?>
                                                    <div class="mb-3 d-flex align-items-center gap-2">
                                                        <strong>Đơn thuốc:</strong>
                                                        <span class="text-truncate d-inline-block"
                                                            style="max-width: 300px;"><?php echo htmlspecialchars($record['don_thuoc']); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($record['loi_dan'])): ?>
                                                    <div>
                                                        <strong>Lời dặn:</strong>
                                                        <?php echo htmlspecialchars($record['loi_dan']); ?>
                                                    </div>
                                                <?php endif; ?>
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
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Chỉnh sửa thông tin cá nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateProfileForm" action="process_update_profile.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ho_ten" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="ho_ten" name="ho_ten"
                                    value="<?php echo htmlspecialchars($patient['ho_ten']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nam_sinh" class="form-label">Năm sinh</label>
                                <input type="number" class="form-control" id="nam_sinh" name="nam_sinh"
                                    value="<?php echo htmlspecialchars($patient['nam_sinh']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gioi_tinh" class="form-label">Giới tính</label>
                                <select class="form-select" id="gioi_tinh" name="gioi_tinh" required>
                                    <option value="Nam" <?php echo $patient['gioi_tinh'] === 'Nam' ? 'selected' : ''; ?>>
                                        Nam</option>
                                    <option value="Nữ" <?php echo $patient['gioi_tinh'] === 'Nữ' ? 'selected' : ''; ?>>Nữ
                                    </option>
                                    <option value="Khác" <?php echo $patient['gioi_tinh'] === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cmnd_cccd" class="form-label">Số CMND/CCCD</label>
                                <input type="text" class="form-control" id="cmnd_cccd" name="cmnd_cccd"
                                    value="<?php echo htmlspecialchars($patient['cmnd_cccd']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($patient['email']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="dien_thoai" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="dien_thoai" name="dien_thoai"
                                    value="<?php echo htmlspecialchars($patient['dien_thoai']); ?>" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="dia_chi" class="form-label">Địa chỉ</label>
                                <input type="text" class="form-control" id="dia_chi" name="dia_chi"
                                    value="<?php echo htmlspecialchars($patient['dia_chi']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nhom_mau" class="form-label">Nhóm máu</label>
                                <select class="form-select" id="nhom_mau" name="nhom_mau">
                                    <option value="" <?php echo empty($patient['nhom_mau']) ? 'selected' : ''; ?>>Chọn
                                        nhóm máu</option>
                                    <option value="A" <?php echo $patient['nhom_mau'] === 'A' ? 'selected' : ''; ?>>A
                                    </option>
                                    <option value="B" <?php echo $patient['nhom_mau'] === 'B' ? 'selected' : ''; ?>>B
                                    </option>
                                    <option value="AB" <?php echo $patient['nhom_mau'] === 'AB' ? 'selected' : ''; ?>>AB
                                    </option>
                                    <option value="O" <?php echo $patient['nhom_mau'] === 'O' ? 'selected' : ''; ?>>O
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="di_ung" class="form-label">Dị ứng</label>
                                <textarea class="form-control" id="di_ung" name="di_ung"
                                    rows="2"><?php echo htmlspecialchars($patient['di_ung']); ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="saveProfileBtn">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Xem chi tiết hồ sơ bệnh án -->
    <div class="modal fade" id="viewMedicalRecordModal" tabindex="-1" aria-labelledby="viewMedicalRecordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewMedicalRecordModalLabel">Chi tiết hồ sơ bệnh án</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="record-reason text-primary mb-0"></h5>
                            <span class="badge bg-info record-date"></span>
                        </div>
                        <p class="text-muted mb-0">Bác sĩ: <span class="record-doctor"></span></p>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold">Chẩn đoán</h6>
                        <div class="record-diagnosis p-2 bg-light rounded"></div>
                    </div>

                    <div class="mb-3 record-treatment-section">
                        <h6 class="fw-bold">Kết quả khám</h6>
                        <div class="record-treatment p-2 bg-light rounded" style="white-space: pre-wrap;"></div>
                    </div>

                    <div class="mb-3 record-prescription-section">
                        <h6 class="fw-bold">Đơn thuốc</h6>
                        <div class="record-prescription p-2 bg-light rounded" style="white-space: pre-wrap;"></div>
                    </div>

                    <div class="mb-3 record-notes-section">
                        <h6 class="fw-bold">Lời dặn</h6>
                        <div class="record-notes p-2 bg-light rounded" style="white-space: pre-wrap;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary download-record">
                        <i class="fas fa-file-download"></i> Tải xuống
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle save button click
            document.getElementById('saveProfileBtn').addEventListener('click', function () {
                document.getElementById('updateProfileForm').submit();
            });

            // Auto-show the tab if there's a hash in URL
            let hash = window.location.hash;
            if (hash) {
                let tab = document.querySelector(`button[data-bs-target="${hash}"]`);
                if (tab) {
                    let tabInstance = new bootstrap.Tab(tab);
                    tabInstance.show();
                }
            }

            // Handle medical record modal
            const viewRecordButtons = document.querySelectorAll('.view-record');
            viewRecordButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const reason = this.getAttribute('data-reason');
                    const doctor = this.getAttribute('data-doctor');
                    const date = this.getAttribute('data-date');
                    const diagnosis = this.getAttribute('data-diagnosis');
                    const treatment = this.getAttribute('data-treatment');
                    const prescription = this.getAttribute('data-prescription');
                    const notes = this.getAttribute('data-notes');
                    const recordId = this.getAttribute('data-id');

                    // Populate modal
                    document.querySelector('.record-reason').textContent = reason;
                    document.querySelector('.record-doctor').textContent = doctor;
                    document.querySelector('.record-date').textContent = date;
                    document.querySelector('.record-diagnosis').textContent = diagnosis;

                    // Treatment section
                    const treatmentSection = document.querySelector('.record-treatment-section');
                    const treatmentContent = document.querySelector('.record-treatment');
                    if (treatment && treatment.trim() !== '') {
                        treatmentContent.textContent = treatment;
                        treatmentSection.style.display = 'block';
                    } else {
                        treatmentSection.style.display = 'none';
                    }

                    // Prescription section
                    const prescriptionSection = document.querySelector('.record-prescription-section');
                    const prescriptionContent = document.querySelector('.record-prescription');
                    if (prescription && prescription.trim() !== '') {
                        prescriptionContent.textContent = prescription;
                        prescriptionSection.style.display = 'block';
                    } else {
                        prescriptionSection.style.display = 'none';
                    }

                    // Notes section
                    const notesSection = document.querySelector('.record-notes-section');
                    const notesContent = document.querySelector('.record-notes');
                    if (notes && notes.trim() !== '') {
                        notesContent.textContent = notes;
                        notesSection.style.display = 'block';
                    } else {
                        notesSection.style.display = 'none';
                    }

                    // Update download link
                    document.querySelector('.download-record').href = 'download_medical_record.php?id=' + recordId;
                });
            });
        });
    </script>
</body>

</html>