<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
if (!is_logged_in()) {
    header('Location: dangnhap.php');
    exit;
}

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = 'Lịch sử đặt lịch';

$patient = get_patient_info($_SESSION['user_id']);
$today = date('Y-m-d');
// All appointments
$stmt = $conn->prepare("SELECT l.*, b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty, d.ten_dichvu AS service FROM lichhen l JOIN bacsi b ON l.bacsi_id=b.id JOIN chuyenkhoa c ON b.chuyenkhoa_id=c.id JOIN dichvu d ON l.dichvu_id=d.id WHERE l.benhnhan_id=? ORDER BY l.ngay_hen DESC");
$stmt->bind_param('i', $patient['id']);
$stmt->execute();
$appointments_all = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// Upcoming
$stmt = $conn->prepare("SELECT l.*, b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty, d.ten_dichvu AS service FROM lichhen l JOIN bacsi b ON l.bacsi_id=b.id JOIN chuyenkhoa c ON b.chuyenkhoa_id=c.id JOIN dichvu d ON l.dichvu_id=d.id WHERE l.benhnhan_id=? AND l.trang_thai IN ('pending','confirmed') AND l.ngay_hen>=? ORDER BY l.ngay_hen, l.gio_hen");
$stmt->bind_param('is', $patient['id'], $today);
$stmt->execute();
$appointments_upcoming = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// Completed
$stmt = $conn->prepare("SELECT l.*, b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty, d.ten_dichvu AS service FROM lichhen l JOIN bacsi b ON l.bacsi_id=b.id JOIN chuyenkhoa c ON b.chuyenkhoa_id=c.id JOIN dichvu d ON l.dichvu_id=d.id WHERE l.benhnhan_id=? AND l.trang_thai='completed' ORDER BY l.ngay_hen DESC");
$stmt->bind_param('i', $patient['id']);
$stmt->execute();
$appointments_completed = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// Cancelled
$stmt = $conn->prepare("SELECT l.*, b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty, d.ten_dichvu AS service FROM lichhen l JOIN bacsi b ON l.bacsi_id=b.id JOIN chuyenkhoa c ON b.chuyenkhoa_id=c.id JOIN dichvu d ON l.dichvu_id=d.id WHERE l.benhnhan_id=? AND l.trang_thai='cancelled' ORDER BY l.ngay_hen DESC");
$stmt->bind_param('i', $patient['id']);
$stmt->execute();
$appointments_cancelled = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch all available specialties for filter
$stmt = $conn->prepare("SELECT id, ten_chuyenkhoa FROM chuyenkhoa ORDER BY ten_chuyenkhoa");
$stmt->execute();
$specialties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .history-container {
            padding: 40px 0;
        }
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .profile-header {
            background-color: #f8f9fa;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
        }
        .profile-content {
            padding: 30px;
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
        .rating-stars {
            color: #ffc107;
        }
        .filter-section {
            margin-bottom: 30px;
        }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        .empty-icon {
            font-size: 60px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        .tab-content {
            min-height: 400px;
        }
        .profile-nav {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 25px;
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
        .filter-collapse {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }
        .filter-collapse.show {
            max-height: 500px;
        }
        #loadingIndicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 100;
            background: rgba(255,255,255,0.8);
            padding: 20px;
            border-radius: 5px;
            display: none;
        }
        .filter-active {
            background: #e9f0ff !important;
            border: 1px solid #b8d0ff !important;
        }
        .filtered-results {
            position: relative;
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
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử đặt lịch khám</h4>
                        <a href="datlich.php" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Đặt lịch mới
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Lọc và tìm kiếm nâng cao -->
                        <div class="filter-section mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Lọc lịch hẹn</h5>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleFilterBtn">
                                    <i class="fas fa-sliders-h me-1"></i> Hiển thị bộ lọc
                                </button>
                            </div>
                            
                            <form id="filter-form" class="filter-collapse">
                                <div class="card card-body border-light bg-light-subtle">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small mb-1">Khoảng thời gian</label>
                                            <div class="d-flex gap-2">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white">Từ</span>
                                                    <input type="date" class="form-control" name="date_from" id="dateFrom">
                                                </div>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white">Đến</span>
                                                    <input type="date" class="form-control" name="date_to" id="dateTo">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small mb-1">Bác sĩ và chuyên khoa</label>
                                            <div class="d-flex gap-2">
                                                <select class="form-select form-select-sm" id="doctorFilter" name="doctor">
                                                    <option value="">Tất cả bác sĩ</option>
                                                    <?php 
                                                    $stmt = $conn->prepare("SELECT DISTINCT b.id, b.ho_ten FROM bacsi b 
                                                                            JOIN lichhen l ON l.bacsi_id = b.id 
                                                                            WHERE l.benhnhan_id = ? ORDER BY b.ho_ten");
                                                    $stmt->bind_param('i', $patient['id']);
                                                    $stmt->execute();
                                                    $doctors = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                                                    foreach($doctors as $doctor): 
                                                    ?>
                                                    <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['ho_ten']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <select class="form-select form-select-sm" id="specialtyFilter" name="specialty">
                                                    <option value="">Tất cả chuyên khoa</option>
                                                    <?php foreach($specialties as $specialty): ?>
                                                    <option value="<?php echo htmlspecialchars($specialty['id']); ?>"><?php echo htmlspecialchars($specialty['ten_chuyenkhoa']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small mb-1">Trạng thái và sắp xếp</label>
                                            <div class="d-flex gap-2">
                                                <select class="form-select form-select-sm" id="statusFilter" name="status">
                                                    <option value="">Tất cả trạng thái</option>
                                                    <option value="pending">Chờ xác nhận</option>
                                                    <option value="confirmed">Đã xác nhận</option>
                                                    <option value="completed">Đã hoàn thành</option>
                                                    <option value="cancelled">Đã hủy</option>
                                                    <option value="rescheduled">Đã đổi lịch</option>
                                                </select>
                                                <select class="form-select form-select-sm" id="sortFilter" name="sort">
                                                    <option value="newest">Mới nhất trước</option>
                                                    <option value="oldest">Cũ nhất trước</option>
                                                    <option value="service">Theo dịch vụ</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small mb-1">Tìm kiếm</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control" id="searchInput" name="search" placeholder="Tìm theo mã lịch hẹn, lý do khám...">
                                                <select class="form-select" name="search_type" style="max-width: 130px;">
                                                    <option value="all">Tất cả</option>
                                                    <option value="code">Mã lịch hẹn</option>
                                                    <option value="reason">Lý do khám</option>
                                                </select>
                                                <button class="btn btn-outline-primary" type="submit">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top mt-2">
                                            <button type="reset" class="btn btn-sm btn-outline-secondary" id="resetFilter">
                                                <i class="fas fa-undo-alt me-1"></i>Đặt lại
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-primary" id="applyFilter">
                                                <i class="fas fa-filter me-1"></i>Áp dụng bộ lọc
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs profile-nav" id="historyTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                                    Tất cả <span class="badge bg-secondary ms-1"><?php echo count($appointments_all); ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                                    Sắp tới <span class="badge bg-primary ms-1"><?php echo count($appointments_upcoming); ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button">
                                    Đã hoàn thành <span class="badge bg-success ms-1"><?php echo count($appointments_completed); ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button">
                                    Đã hủy <span class="badge bg-danger ms-1"><?php echo count($appointments_cancelled); ?></span>
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="historyTabContent">
                            <!-- Tất cả -->
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <?php if (empty($appointments_all)): ?>
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="far fa-calendar"></i></div>
                                    <h5>Bạn chưa có lịch sử đặt khám</h5>
                                    <p class="text-muted">Các lịch hẹn của bạn sẽ xuất hiện ở đây</p>
                                    <a href="datlich.php" class="btn btn-primary">Đặt lịch khám ngay</a>
                                </div>
                                <?php else: ?>
                                <?php foreach ($appointments_all as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. <?php echo htmlspecialchars($appointment['doctor_name']); ?> - <?php echo htmlspecialchars($appointment['specialty']); ?></div>
                                        <div class="appointment-status status-<?php echo htmlspecialchars($appointment['trang_thai']); ?>">
                                            <?php 
                                            $status_text = '';
                                            switch($appointment['trang_thai']) {
                                                case 'confirmed': $status_text = 'Đã xác nhận'; break;
                                                case 'pending': $status_text = 'Chờ xác nhận'; break;
                                                case 'completed': $status_text = 'Đã hoàn thành'; break;
                                                case 'cancelled': $status_text = 'Đã hủy'; break;
                                                case 'rescheduled': $status_text = 'Đã đổi lịch'; break;
                                                default: $status_text = ucfirst($appointment['trang_thai']);
                                            }
                                            echo htmlspecialchars($status_text); 
                                            ?>
                                        </div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> <?php echo date('l, d/m/Y', strtotime($appointment['ngay_hen'])); ?>
                                        <i class="far fa-clock ms-3 me-2"></i> <?php echo htmlspecialchars($appointment['gio_hen']); ?>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Dịch vụ: <span class="fw-medium"><?php echo htmlspecialchars($appointment['service']); ?></span></div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi'] ?? 'Chưa cập nhật'); ?></span></div>
                                        </div>
                                        <?php if (!empty($appointment['phi_kham'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                                            <div>Phí khám: <span class="fw-medium"><?php echo number_format($appointment['phi_kham'], 0, ',', '.'); ?> VNĐ</span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ma_lichhen'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                            <div>Mã lịch hẹn: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ly_do'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-alt"></i></div>
                                            <div>Lý do khám: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ly_do']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['chan_doan'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-medical"></i></div>
                                            <div>Chẩn đoán: <span class="text-primary fw-medium"><?php echo htmlspecialchars($appointment['chan_doan']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['ket_qua'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-clipboard-check"></i></div>
                                            <div>Kết quả: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ket_qua']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['don_thuoc'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                                            <div>Đơn thuốc: <span class="fw-medium"><?php echo htmlspecialchars($appointment['don_thuoc']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['loi_dan'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-dots"></i></div>
                                            <div>Lời dặn: <span class="fst-italic"><?php echo htmlspecialchars($appointment['loi_dan']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'cancelled' && !empty($appointment['ly_do_huy'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-info-circle text-danger"></i></div>
                                            <div>Lý do hủy: <span class="text-danger"><?php echo htmlspecialchars($appointment['ly_do_huy']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'cancelled' && !empty($appointment['ngay_huy'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-calendar-times"></i></div>
                                            <div>Ngày hủy: <span class="fw-medium"><?php echo date('d/m/Y', strtotime($appointment['ngay_huy'])); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'rescheduled' && !empty($appointment['ly_do_doi'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-exchange-alt text-warning"></i></div>
                                            <div>Lý do đổi lịch: <span class="text-warning"><?php echo htmlspecialchars($appointment['ly_do_doi']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($appointment['ghi_chu'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-sticky-note"></i></div>
                                            <div>Ghi chú: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ghi_chu']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <?php if ($appointment['trang_thai'] == 'pending'): ?>
                                        <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=reschedule" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Thay đổi lịch
                                        </a>
                                        <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=cancel" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Hủy lịch hẹn
                                        </a>
                                        <?php endif; ?>
                                        <?php if (!isset($appointment['thanh_toan']) || $appointment['thanh_toan'] !== 'paid'): ?>
                                        <a href="thanhtoan.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-credit-card"></i> Thanh toán
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Tab Sắp tới -->
                            <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                <?php if (empty($appointments_upcoming)): ?>
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="far fa-calendar-plus"></i></div>
                                    <h5>Không có lịch hẹn sắp tới</h5>
                                    <p class="text-muted">Bạn chưa có lịch hẹn nào trong thời gian tới</p>
                                    <a href="datlich.php" class="btn btn-primary">Đặt lịch khám ngay</a>
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
                                                default: $status_text = ucfirst($appointment['trang_thai']);
                                            }
                                            echo htmlspecialchars($status_text); 
                                            ?>
                                        </div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> <?php echo date('l, d/m/Y', strtotime($appointment['ngay_hen'])); ?>
                                        <i class="far fa-clock ms-3 me-2"></i> <?php echo htmlspecialchars($appointment['gio_hen']); ?>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Dịch vụ: <span class="fw-medium"><?php echo htmlspecialchars($appointment['service']); ?></span></div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi'] ?? 'Chưa cập nhật'); ?></span></div>
                                        </div>
                                        <?php if (!empty($appointment['phi_kham'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                                            <div>Phí khám: <span class="fw-medium"><?php echo number_format($appointment['phi_kham'], 0, ',', '.'); ?> VNĐ</span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ma_lichhen'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                            <div>Mã lịch hẹn: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ly_do'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-alt"></i></div>
                                            <div>Lý do khám: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ly_do']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($appointment['ghi_chu'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-sticky-note"></i></div>
                                            <div>Ghi chú: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ghi_chu']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=reschedule" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Thay đổi lịch
                                        </a>
                                        <a href="huy_lichhen.php?id=<?php echo $appointment['id']; ?>&action=cancel" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Hủy lịch hẹn
                                        </a>
                                        <?php if (!isset($appointment['thanh_toan']) || $appointment['thanh_toan'] !== 'paid'): ?>
                                        <a href="thanhtoan.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-credit-card"></i> Thanh toán
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Tab Đã hoàn thành -->
                            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                                <?php if (empty($appointments_completed)): ?>
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-check-circle"></i></div>
                                    <h5>Không có lịch khám nào đã hoàn thành</h5>
                                    <p class="text-muted">Các lịch hẹn đã hoàn thành sẽ hiển thị ở đây</p>
                                </div>
                                <?php else: ?>
                                <?php foreach ($appointments_completed as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. <?php echo htmlspecialchars($appointment['doctor_name']); ?> - <?php echo htmlspecialchars($appointment['specialty']); ?></div>
                                        <div class="appointment-status status-completed">Đã hoàn thành</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> <?php echo date('l, d/m/Y', strtotime($appointment['ngay_hen'])); ?>
                                        <i class="far fa-clock ms-3 me-2"></i> <?php echo htmlspecialchars($appointment['gio_hen']); ?>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Dịch vụ: <span class="fw-medium"><?php echo htmlspecialchars($appointment['service']); ?></span></div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi'] ?? 'Chưa cập nhật'); ?></span></div>
                                        </div>
                                        <?php if (!empty($appointment['phi_kham'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                                            <div>Phí khám: <span class="fw-medium"><?php echo number_format($appointment['phi_kham'], 0, ',', '.'); ?> VNĐ</span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ma_lichhen'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                            <div>Mã lịch hẹn: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ly_do'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-alt"></i></div>
                                            <div>Lý do khám: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ly_do']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['chan_doan'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-medical"></i></div>
                                            <div>Chẩn đoán: <span class="text-primary fw-medium"><?php echo htmlspecialchars($appointment['chan_doan']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ket_qua'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-clipboard-check"></i></div>
                                            <div>Kết quả: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ket_qua']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['don_thuoc'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                                            <div>Đơn thuốc: <span class="fw-medium"><?php echo htmlspecialchars($appointment['don_thuoc']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['loi_dan'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-dots"></i></div>
                                            <div>Lời dặn: <span class="fst-italic"><?php echo htmlspecialchars($appointment['loi_dan']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($appointment['ghi_chu'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-sticky-note"></i></div>
                                            <div>Ghi chú: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ghi_chu']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#ratingModal" data-appointment-id="<?php echo $appointment['id']; ?>">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
                                        <?php if (!empty($appointment['ket_qua'])): ?>
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>&result=view" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-file-medical"></i> Xem kết quả
                                        </a>
                                        <?php endif; ?>
                                        <a href="datlich.php?rebook=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Tab Đã hủy -->
                            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                                <?php if (empty($appointments_cancelled)): ?>
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
                                    <h5>Không có lịch khám nào đã hủy</h5>
                                    <p class="text-muted">Các lịch hẹn đã hủy sẽ hiển thị ở đây</p>
                                </div>
                                <?php else: ?>
                                <?php foreach ($appointments_cancelled as $appointment): ?>
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. <?php echo htmlspecialchars($appointment['doctor_name']); ?> - <?php echo htmlspecialchars($appointment['specialty']); ?></div>
                                        <div class="appointment-status status-cancelled">Đã hủy</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> <?php echo date('l, d/m/Y', strtotime($appointment['ngay_hen'])); ?>
                                        <i class="far fa-clock ms-3 me-2"></i> <?php echo htmlspecialchars($appointment['gio_hen']); ?>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Dịch vụ: <span class="fw-medium"><?php echo htmlspecialchars($appointment['service']); ?></span></div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi'] ?? 'Chưa cập nhật'); ?></span></div>
                                        </div>
                                        <?php if (!empty($appointment['phi_kham'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                                            <div>Phí khám: <span class="fw-medium"><?php echo number_format($appointment['phi_kham'], 0, ',', '.'); ?> VNĐ</span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ma_lichhen'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                            <div>Mã lịch hẹn: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ly_do'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-alt"></i></div>
                                            <div>Lý do khám: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ly_do']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ly_do_huy'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-info-circle text-danger"></i></div>
                                            <div>Lý do hủy: <span class="text-danger"><?php echo htmlspecialchars($appointment['ly_do_huy']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ngay_huy'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-calendar-times"></i></div>
                                            <div>Ngày hủy: <span class="fw-medium"><?php echo date('d/m/Y', strtotime($appointment['ngay_huy'])); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($appointment['ghi_chu'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-sticky-note"></i></div>
                                            <div>Ghi chú: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ghi_chu']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <a href="datlich.php?rebook=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại lịch hẹn
                                        </a>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Phân trang - chỉ hiển thị nếu có nhiều lịch hẹn -->
                        <?php if (count($appointments_all) > 10): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Đánh giá -->
    <div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ratingModalLabel">Đánh giá lịch khám</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rating-form">
                        <input type="hidden" id="appointment_id" name="appointment_id" value="">
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold">Đánh giá chung về dịch vụ</label>
                            <div class="rating-stars fs-2">
                                <i class="far fa-star star-rating" data-rating="1"></i>
                                <i class="far fa-star star-rating" data-rating="2"></i>
                                <i class="far fa-star star-rating" data-rating="3"></i>
                                <i class="far fa-star star-rating" data-rating="4"></i>
                                <i class="far fa-star star-rating" data-rating="5"></i>
                            </div>
                            <input type="hidden" id="ratingValue" name="rating" value="0">
                        </div>

                        <div class="mb-3">
                            <label for="doctorRating" class="form-label">Chất lượng chuyên môn của bác sĩ</label>
                            <select class="form-select" id="doctorRating" name="doctor_rating">
                                <option value="" selected disabled>Chọn đánh giá</option>
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="facilityRating" class="form-label">Cơ sở vật chất</label>
                            <select class="form-select" id="facilityRating" name="facility_rating">
                                <option value="" selected disabled>Chọn đánh giá</option>
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="serviceRating" class="form-label">Thái độ phục vụ</label>
                            <select class="form-select" id="serviceRating" name="service_rating">
                                <option value="" selected disabled>Chọn đánh giá</option>
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Nhận xét của bạn</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitRating">Gửi đánh giá</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hiển thị/ẩn bộ lọc
            const toggleFilterBtn = document.getElementById('toggleFilterBtn');
            const filterForm = document.getElementById('filter-form');
            
            toggleFilterBtn.addEventListener('click', function() {
                filterForm.classList.toggle('show');
                if (filterForm.classList.contains('show')) {
                    toggleFilterBtn.innerHTML = '<i class="fas fa-times me-1"></i> Ẩn bộ lọc';
                    toggleFilterBtn.classList.add('btn-outline-primary');
                    toggleFilterBtn.classList.remove('btn-outline-secondary');
                } else {
                    toggleFilterBtn.innerHTML = '<i class="fas fa-sliders-h me-1"></i> Hiển thị bộ lọc';
                    toggleFilterBtn.classList.remove('btn-outline-primary');
                    toggleFilterBtn.classList.add('btn-outline-secondary');
                }
            });
            
            // Xử lý đánh giá sao
            const stars = document.querySelectorAll('.star-rating');
            const ratingInput = document.getElementById('ratingValue');
            
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = this.dataset.rating;
                    highlightStars(rating);
                });
                
                star.addEventListener('mouseout', function() {
                    const currentRating = ratingInput.value;
                    highlightStars(currentRating);
                });
                
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;
                    highlightStars(rating);
                });
            });
            
            function highlightStars(rating) {
                stars.forEach(star => {
                    if (star.dataset.rating <= rating) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            }
            
            // Cập nhật ID lịch hẹn khi mở modal đánh giá
            const ratingModal = document.getElementById('ratingModal');
            if (ratingModal) {
                ratingModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const appointmentId = button.getAttribute('data-appointment-id');
                    document.getElementById('appointment_id').value = appointmentId;
                });
            }
            
            // Xử lý gửi form đánh giá
            const submitRating = document.getElementById('submitRating');
            if (submitRating) {
                submitRating.addEventListener('click', function() {
                    const form = document.getElementById('rating-form');
                    // Kiểm tra form có hợp lệ không
                    if (ratingInput.value == 0) {
                        alert('Vui lòng chọn đánh giá sao!');
                        return;
                    }
                    
                    // Gửi form bằng AJAX (giả lập trong ví dụ này)
                    alert('Cảm ơn bạn đã đánh giá! Đánh giá của bạn đã được ghi nhận.');
                    
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(ratingModal);
                    modal.hide();
                    
                    // Reset form
                    form.reset();
                    highlightStars(0);
                });
            }
            
            // Thêm indicator cho loading
            const tabPanes = document.querySelectorAll('.tab-pane');
            tabPanes.forEach(pane => {
                const loadingDiv = document.createElement('div');
                loadingDiv.id = 'loadingIndicator';
                loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div><div class="ms-2">Đang tải dữ liệu...</div>';
                pane.classList.add('filtered-results');
                pane.appendChild(loadingDiv);
            });
            
            // AJAX filtering functionality
            const applyFilter = document.getElementById('applyFilter');
            const resetFilter = document.getElementById('resetFilter');
            
            // Set today as the default date for dateTo
            document.getElementById('dateTo').valueAsDate = new Date();
            
            // Set 3 months ago as the default date for dateFrom
            const threeMonthsAgo = new Date();
            threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
            document.getElementById('dateFrom').valueAsDate = threeMonthsAgo;
            
            // Filter function
            applyFilter.addEventListener('click', function(e) {
                e.preventDefault();
                applyFiltering();
            });
            
            // Reset filter button
            resetFilter.addEventListener('click', function(e) {
                setTimeout(() => {
                    document.getElementById('dateTo').valueAsDate = new Date();
                    const threeMonthsAgo = new Date();
                    threeMonthsAgo.setMonth(threeMonthsAgo.getMonth() - 3);
                    document.getElementById('dateFrom').valueAsDate = threeMonthsAgo;
                    applyFiltering();
                }, 100);
            });
            
            // Tab change handling
            const tabButtons = document.querySelectorAll('button[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function (event) {
                    const activeTabId = event.target.getAttribute('data-bs-target').substr(1);
                    applyFiltering(activeTabId);
                });
            });
            
            // Main filtering function
            function applyFiltering(tabId = null) {
                // Make filter card active
                document.querySelector('.card.card-body').classList.add('filter-active');
                
                // Show loading indicator
                const activeTab = tabId ? document.getElementById(tabId) : document.querySelector('.tab-pane.active');
                const loadingIndicator = activeTab.querySelector('#loadingIndicator');
                loadingIndicator.style.display = 'flex';
                
                // Get all form data
                const formData = new FormData(document.getElementById('filter-form'));
                
                // Add the active tab type to filter
                const tabType = tabId || document.querySelector('.tab-pane.active').id;
                formData.append('tab_type', tabType);
                
                // Convert FormData to URL params
                const params = new URLSearchParams(formData);
                
                // Add current page info
                formData.append('page', '1');
                
                // Send AJAX request
                fetch('filter_appointments.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Update the tab content with filtered results
                    if (data.status === 'success') {
                        // Update the tab content
                        activeTab.innerHTML = data.html;
                        
                        // Re-add the loading indicator since we replaced everything
                        activeTab.classList.add('filtered-results');
                        const newLoadingDiv = document.createElement('div');
                        newLoadingDiv.id = 'loadingIndicator';
                        newLoadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div><div class="ms-2">Đang tải dữ liệu...</div>';
                        newLoadingDiv.style.display = 'none';
                        activeTab.appendChild(newLoadingDiv);
                        
                        // Update badge counter
                        const countBadge = document.querySelector(`button[data-bs-target="#${activeTab.id}"] .badge`);
                        if (countBadge) {
                            countBadge.textContent = data.count;
                        }
                        
                        // Show filter summary if applied
                        const filterCount = Object.values(data.filters).filter(val => val !== '').length;
                        if (filterCount > 0) {
                            const filterSummary = document.createElement('div');
                            filterSummary.className = 'alert alert-info d-flex align-items-center my-3';
                            
                            let summaryText = `<i class="fas fa-filter me-2"></i> Đang hiển thị ${data.count} kết quả theo: `;
                            const filterTexts = [];
                            
                            if (data.filters.date_from) filterTexts.push(`Từ ngày ${data.filters.date_from}`);
                            if (data.filters.date_to) filterTexts.push(`Đến ngày ${data.filters.date_to}`);
                            if (data.filters.doctor_name) filterTexts.push(`Bác sĩ ${data.filters.doctor_name}`);
                            if (data.filters.specialty_name) filterTexts.push(`Chuyên khoa ${data.filters.specialty_name}`);
                            if (data.filters.status_text) filterTexts.push(`Trạng thái ${data.filters.status_text}`);
                            if (data.filters.search) filterTexts.push(`Tìm kiếm "${data.filters.search}"`);
                            
                            summaryText += filterTexts.join(', ');
                            filterSummary.innerHTML = summaryText;
                            
                            // Add the summary before the appointment listings
                            if (data.count > 0) {
                                activeTab.insertBefore(filterSummary, activeTab.firstChild.nextSibling);
                            }
                        }
                    } else {
                        // Show error
                        console.error('Error filtering appointments:', data.message);
                        activeTab.innerHTML += `<div class="alert alert-danger mt-3">${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    activeTab.innerHTML += `<div class="alert alert-danger mt-3">Đã xảy ra lỗi khi lọc dữ liệu. Vui lòng thử lại sau.</div>`;
                })
                .finally(() => {
                    // Hide loading indicator
                    loadingIndicator.style.display = 'none';
                    
                    // Remove active state after delay
                    setTimeout(() => {
                        document.querySelector('.card.card-body').classList.remove('filter-active');
                    }, 500);
                });
            }
            
            // Set initial values if provided in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('specialty')) {
                document.getElementById('specialtyFilter').value = urlParams.get('specialty');
            }
            if (urlParams.has('status')) {
                document.getElementById('statusFilter').value = urlParams.get('status');
            }
            if (urlParams.has('search')) {
                document.getElementById('searchInput').value = urlParams.get('search');
            }
            
            // Expand filter by default if any filter is applied
            if (urlParams.toString()) {
                filterForm.classList.add('show');
                toggleFilterBtn.innerHTML = '<i class="fas fa-times me-1"></i> Ẩn bộ lọc';
                toggleFilterBtn.classList.add('btn-outline-primary');
                toggleFilterBtn.classList.remove('btn-outline-secondary');
            }
        });
    </script>
</body>
</html>