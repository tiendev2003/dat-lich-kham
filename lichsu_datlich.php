<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
if (!is_logged_in()) {
    header('Location: dangnhap.php');
    exit;
}
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt lịch khám - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container history-container">
        <div class="row">
            <div class="col-lg-3">
                <div class="profile-card mb-4">
                    <div class="profile-header">
                        <div class="text-center mb-3">
                            <div class="profile-avatar d-inline-block position-relative" style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 3px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                                <img src="assets/img/user-avatar.png" alt="Ảnh đại diện" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>
                        <h5 class="text-center mb-1"><?php echo htmlspecialchars($patient['ho_ten']); ?></h5>
                        <p class="text-center text-muted small mb-0">
                            <i class="fas fa-phone-alt me-1"></i> <?php echo htmlspecialchars($patient['dien_thoai']); ?>
                        </p>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="user_profile.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user me-2"></i> Thông tin cá nhân
                        </a>
                        <a href="lichsu_datlich.php" class="list-group-item list-group-item-action active">
                            <i class="fas fa-calendar-check me-2"></i> Lịch sử đặt khám
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

            <div class="col-lg-9">
                <div class="profile-card">
                    <div class="profile-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-history me-2"></i>Lịch sử đặt lịch khám</h4>
                        <a href="datlich.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Đặt lịch mới
                        </a>
                    </div>
                    <div class="profile-content">
                        <!-- Lọc và tìm kiếm -->
                        <div class="filter-section">
                            <form id="filter-form" class="row g-3">
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm" id="statusFilter" name="status">
                                        <option value="all">Tất cả trạng thái</option>
                                        <option value="pending">Chờ xác nhận</option>
                                        <option value="confirmed">Đã xác nhận</option>
                                        <option value="completed">Đã hoàn thành</option>
                                        <option value="cancelled">Đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm" id="specialtyFilter" name="specialty">
                                        <option value="all">Tất cả chuyên khoa</option>
                                        <?php foreach($specialties as $specialty): ?>
                                        <option value="<?php echo htmlspecialchars($specialty['id']); ?>"><?php echo htmlspecialchars($specialty['ten_chuyenkhoa']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" id="searchInput" name="search" placeholder="Tìm kiếm...">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
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
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi']); ?></span></div>
                                        </div>
                                        <?php if (!empty($appointment['ma_lichhen'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                            <div>Mã lịch hẹn: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['chan_doan'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-medical"></i></div>
                                            <div>Chẩn đoán: <span class="text-primary fw-medium"><?php echo htmlspecialchars($appointment['chan_doan']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($appointment['trang_thai'] === 'cancelled' && !empty($appointment['ly_do_huy'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-info-circle text-danger"></i></div>
                                            <div>Lý do hủy: <span class="text-danger"><?php echo htmlspecialchars($appointment['ly_do_huy']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        
                                        <?php if ($appointment['trang_thai'] === 'completed'): ?>
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#ratingModal" data-appointment-id="<?php echo $appointment['id']; ?>">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
                                        <a href="datlich.php?rebook=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại
                                        </a>
                                        <?php elseif ($appointment['trang_thai'] === 'cancelled'): ?>
                                        <a href="datlich.php?rebook=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại lịch hẹn
                                        </a>
                                        <?php elseif (in_array($appointment['trang_thai'], ['pending', 'confirmed'])): ?>
                                        <a href="xuly_doilich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Thay đổi lịch
                                        </a>
                                        <a href="xuly_huylich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Hủy lịch hẹn
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
                                            echo $appointment['trang_thai'] === 'confirmed' ? 'Đã xác nhận' : 'Chờ xác nhận'; 
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
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi']); ?></span></div>
                                        </div>
                                        <?php if (!empty($appointment['ma_lichhen'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                                            <div>Mã lịch hẹn: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['phi_kham'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                                            <div>Phí khám: <span class="fw-medium"><?php echo number_format($appointment['phi_kham'], 0, ',', '.'); ?> VNĐ</span></div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($appointment['ly_do'])): ?>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-alt"></i></div>
                                            <div>Lý do khám: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ly_do']); ?></span></div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <a href="xuly_doilich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Thay đổi lịch
                                        </a>
                                        <a href="xuly_huylich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Hủy lịch hẹn
                                        </a>
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
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi']); ?></span></div>
                                        </div>
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
                                    </div>
                                    <div class="appointment-actions">
                                        <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-medical"></i> Xem kết quả
                                        </a>
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#ratingModal" data-appointment-id="<?php echo $appointment['id']; ?>">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
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
                                            <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi']); ?></span></div>
                                        </div>
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
                                    </div>
                                    <div class="appointment-actions">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            
            // Xử lý filter
            const filterForm = document.getElementById('filter-form');
            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Thực hiện lọc dữ liệu (demo)
                    const statusFilter = document.getElementById('statusFilter').value;
                    const specialtyFilter = document.getElementById('specialtyFilter').value;
                    const searchInput = document.getElementById('searchInput').value;
                    
                    console.log('Filtering with:', {
                        status: statusFilter,
                        specialty: specialtyFilter,
                        search: searchInput
                    });
                    
                    // Giả lập lọc dữ liệu - trong thực tế sẽ gửi AJAX request và cập nhật nội dung
                    alert('Đang lọc dữ liệu: Trạng thái = ' + statusFilter + ', Chuyên khoa = ' + specialtyFilter + ', Tìm kiếm = ' + searchInput);
                });
            }
        });
    </script>
</body>
</html>