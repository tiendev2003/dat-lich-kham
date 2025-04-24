<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check login
if (!is_logged_in()) {
    header('Location: dangnhap.php?redirect=huy_lichhen.php');
    exit;
}

// Get patient info
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);
if (!$patient) {
    header('Location: index.php');
    exit;
}

// Get appointment ID and action
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

$appointment = null;
if ($appointment_id) {
    $stmt = $conn->prepare(
        "SELECT l.*, b.ho_ten AS bs_name, c.ten_chuyenkhoa AS specialty, ds.ten_dichvu,
        (SELECT COUNT(*) FROM lichhen WHERE benhnhan_id = l.benhnhan_id AND bacsi_id = l.bacsi_id AND id < l.id) AS visit_count
         FROM lichhen l 
         LEFT JOIN bacsi b ON l.bacsi_id = b.id 
         LEFT JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id 
         LEFT JOIN dichvu ds ON l.dichvu_id = ds.id 
         WHERE l.id = ? AND l.benhnhan_id = ?"
    );
    $stmt->bind_param('ii', $appointment_id, $patient['id']);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
    if (!$appointment) {
        header('Location: user_profile.php');
        exit;
    }
}

// Redirect if no appointment ID provided
if (!$appointment_id) {
    header('Location: user_profile.php');
    exit;
}

// Format status for display
$status_class = '';
$status_text = '';
switch($appointment['trang_thai']) {
    case 'confirmed':
        $status_text = 'Đã xác nhận';
        $status_class = 'status-confirmed';
        break;
    case 'pending':
        $status_text = 'Chờ xác nhận';
        $status_class = 'status-pending';
        break;
    case 'rescheduled':
        $status_text = 'Đã đổi lịch';
        $status_class = 'status-rescheduled';
        break;
    case 'cancelled':
        $status_text = 'Đã hủy';
        $status_class = 'status-cancelled';
        break;
    case 'completed':
        $status_text = 'Đã hoàn thành';
        $status_class = 'status-completed';
        break;
    default:
        $status_text = ucfirst($appointment['trang_thai']);
        $status_class = 'status-' . $appointment['trang_thai'];
}

// Check if appointment can still be modified
$can_modify = in_array($appointment['trang_thai'], ['confirmed', 'pending', 'rescheduled']) &&
    strtotime($appointment['ngay_hen']) > strtotime('+24 hours');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch hẹn - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS bundle with Popper -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .appointment-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/anh-gioithieu.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .detail-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        .detail-header {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .detail-section {
            margin-bottom: 30px;
        }
        .detail-item {
            margin-bottom: 15px;
        }
        .detail-label {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .appointment-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
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
        .status-rescheduled {
            background-color: #e2e3ff;
            color: #3a3b7b;
        }
        .status-completed {
            background-color: #c3e6cb;
            color: #155724;
        }
        .timeline {
            position: relative;
            margin-bottom: 30px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 20px;
            padding-left: 30px;
        }
        .timeline-item:before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }
        .timeline-item:last-child:before {
            bottom: 50%;
        }
        .timeline-item:after {
            content: "";
            position: absolute;
            left: -6px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #007bff;
        }
        .timeline-item.active:after {
            background-color: #007bff;
        }
        .timeline-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <section class="appointment-header">
        <div class="container">
            <h1>Quản lý lịch hẹn</h1>
            <p class="lead">Xem thông tin chi tiết, đổi lịch hoặc hủy lịch hẹn khám bệnh</p>
        </div>
    </section>
    <div class="container mb-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="user_profile.php">Tài khoản</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Quản lý lịch hẹn</li>
                    </ol>
                </nav>
            </div>
        </div>

        <?php if ($action === 'view'): ?>
            <!-- Chi tiết lịch hẹn -->
            <div class="detail-card">
                <div class="detail-header d-flex justify-content-between align-items-center">
                    <h3>Chi tiết lịch hẹn #<?php echo $appointment['ma_lichhen']; ?></h3>
                    <div class="appointment-status <?php echo $status_class; ?>">
                        <?php echo $status_text; ?>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="mb-3">Thông tin lịch hẹn</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Chuyên khoa</div>
                                <div><?php echo htmlspecialchars($appointment['specialty']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Bác sĩ</div>
                                <div><?php echo htmlspecialchars($appointment['bs_name']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Dịch vụ</div>
                                <div><?php echo htmlspecialchars($appointment['ten_dichvu'] ?? 'Khám bệnh thông thường'); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Số lần đã khám</div>
                                <div><?php echo intval($appointment['visit_count']); ?> lần</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Ngày hẹn</div>
                                <div><?php echo date('d/m/Y', strtotime($appointment['ngay_hen'])); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Thời gian</div>
                                <div><?php echo $appointment['gio_hen']; ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Địa điểm</div>
                                <div><?php echo htmlspecialchars($appointment['phong_kham'] ?? get_setting('site_address')); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Phí khám</div>
                                <div><?php echo number_format($appointment['phi_kham'] ?? $appointment['phi_dich_vu'] ?? 0, 0, ',', '.'); ?> VNĐ</div>
                            </div>
                        </div>
                        
                        <?php if (!empty($appointment['hinh_thuc_thanh_toan'])): ?>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Hình thức thanh toán</div>
                                <div><?php echo htmlspecialchars($appointment['hinh_thuc_thanh_toan']); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($appointment['trang_thai_thanh_toan'])): ?>
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label">Trạng thái thanh toán</div>
                                <div>
                                    <?php 
                                    $payment_status = '';
                                    switch($appointment['trang_thai_thanh_toan']) {
                                        case 'paid': $payment_status = 'Đã thanh toán'; break;
                                        case 'pending': $payment_status = 'Chờ thanh toán'; break;
                                        default: $payment_status = $appointment['trang_thai_thanh_toan'];
                                    }
                                    echo htmlspecialchars($payment_status);
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-section">
                    <h4 class="mb-3">Lý do khám</h4>
                    <p><?php echo htmlspecialchars($appointment['ly_do']); ?></p>
                </div>

                <?php if (!empty($appointment['ghi_chu'])): ?>
                <div class="detail-section">
                    <h4 class="mb-3">Ghi chú</h4>
                    <p><?php echo nl2br(htmlspecialchars($appointment['ghi_chu'])); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="detail-section">
                    <h4 class="mb-3">Trạng thái lịch hẹn</h4>
                    <div class="timeline">
                        <div class="timeline-item active">
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Đặt lịch</strong>
                                    <small>
                                        <?php 
                                        if (isset($appointment['thoi_diem_tao']) && !empty($appointment['thoi_diem_tao'])) {
                                            echo date('d/m/Y H:i', strtotime($appointment['thoi_diem_tao']));
                                        } else {
                                            echo date('d/m/Y H:i', strtotime($appointment['ngay_hen']));
                                        }
                                        ?>
                                    </small>
                                </div>
                                <div>Bạn đã đặt lịch khám thành công</div>
                            </div>
                        </div>
                        
                        <div class="timeline-item <?php echo in_array($appointment['trang_thai'], ['confirmed', 'rescheduled', 'cancelled', 'completed']) ? 'active' : ''; ?>">
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Xác nhận</strong>
                                    <?php if ($appointment['trang_thai'] !== 'pending'): ?>
                                    <small><?php echo date('d/m/Y H:i', strtotime($appointment['thoi_diem_xac_nhan'] ?? $appointment['thoi_diem_tao'])); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <?php 
                                    if ($appointment['trang_thai'] === 'pending') {
                                        echo 'Đang chờ phòng khám xác nhận';
                                    } else {
                                        echo 'Lịch hẹn đã được xác nhận';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (in_array($appointment['trang_thai'], ['completed', 'cancelled', 'rescheduled'])): ?>
                        <div class="timeline-item active">
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>
                                        <?php 
                                        if ($appointment['trang_thai'] === 'completed') echo 'Hoàn thành';
                                        elseif ($appointment['trang_thai'] === 'cancelled') echo 'Hủy lịch'; 
                                        else echo 'Đổi lịch';
                                        ?>
                                    </strong>
                                    <small>
                                        <?php 
                                        if ($appointment['trang_thai'] === 'completed') {
                                            echo date('d/m/Y H:i', strtotime($appointment['thoi_diem_hoan_thanh'] ?? $appointment['ngay_hen']));
                                        } elseif ($appointment['trang_thai'] === 'cancelled') {
                                            echo date('d/m/Y H:i', strtotime($appointment['thoi_diem_huy'] ?? $appointment['ngay_hen']));
                                        } else {
                                            echo date('d/m/Y H:i', strtotime($appointment['thoi_diem_doi'] ?? $appointment['ngay_hen']));
                                        }
                                        ?>
                                    </small>
                                </div>
                                <div>
                                    <?php 
                                    if ($appointment['trang_thai'] === 'completed') {
                                        echo 'Bạn đã hoàn thành khám bệnh';
                                    } elseif ($appointment['trang_thai'] === 'cancelled') {
                                        echo 'Lịch hẹn đã bị hủy' . (!empty($appointment['ghi_chu']) ? ': ' . $appointment['ghi_chu'] : '');
                                    } else {
                                        echo 'Lịch hẹn đã được thay đổi';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($can_modify): ?>
                <div class="detail-section">
                    <h4 class="mb-3">Hành động</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="huy_lichhen.php?id=<?php echo $appointment_id; ?>&action=reschedule" class="btn btn-primary w-100">
                                <i class="fas fa-calendar-alt me-2"></i> Thay đổi lịch hẹn
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="huy_lichhen.php?id=<?php echo $appointment_id; ?>&action=cancel" class="btn btn-danger w-100">
                                <i class="fas fa-times-circle me-2"></i> Hủy lịch hẹn
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mt-4">
                    <a href="user_profile.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        <?php elseif ($action === 'cancel'): ?>
            <!-- Form hủy lịch -->
            <div class="detail-card">
                <h3>Hủy lịch hẹn #<?php echo $appointment['ma_lichhen']; ?></h3>
                <p class="text-muted mb-4">
                    <i class="fas fa-info-circle"></i> 
                    Vui lòng cho chúng tôi biết lý do bạn muốn hủy lịch hẹn này. 
                    Điều này giúp chúng tôi cải thiện dịch vụ của mình.
                </p>
                
                <div class="alert alert-warning mb-4">
                    <strong><i class="fas fa-exclamation-triangle me-2"></i>Lưu ý quan trọng:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Việc hủy lịch hẹn sẽ không thể hoàn tác.</li>
                        <li>Nếu bạn đã thanh toán phí khám, vui lòng liên hệ với phòng khám để được hướng dẫn hoàn tiền.</li>
                        <li>Hủy lịch quá 3 lần có thể ảnh hưởng đến việc đặt lịch trong tương lai.</li>
                    </ul>
                </div>
                
                <form action="xuly_huylich.php" method="post">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                    <div class="mb-3">
                        <label for="cancel_reason" class="form-label">Lý do hủy <span class="text-danger">*</span></label>
                        <select id="cancel_reason" name="cancel_reason" class="form-select" required>
                            <option value="" selected disabled>Chọn lý do</option>
                            <option value="Không thể đến được">Không thể đến được vào ngày hẹn</option>
                            <option value="Đã khỏi bệnh">Tình trạng bệnh đã cải thiện/hết bệnh</option>
                            <option value="Muốn thay đổi bác sĩ">Muốn thay đổi bác sĩ khám</option>
                            <option value="Muốn thay đổi cơ sở y tế">Muốn thay đổi cơ sở y tế</option>
                            <option value="Khác">Lý do khác</option>
                        </select>
                    </div>
                    <div class="mb-4" id="other_cancel_div" style="display:none;">
                        <label for="other_reason" class="form-label">Chi tiết lý do <span class="text-danger">*</span></label>
                        <textarea id="other_reason" name="other_reason" class="form-control" rows="3" 
                            placeholder="Vui lòng cho chúng tôi biết chi tiết lý do bạn muốn hủy lịch hẹn"></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="huy_lichhen.php?id=<?php echo $appointment_id; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times-circle me-2"></i> Xác nhận hủy lịch
                        </button>
                    </div>
                </form>
            </div>
        <?php elseif ($action === 'reschedule'): ?>
            <!-- Form thay đổi lịch -->
            <div class="detail-card">
                <h3>Thay đổi lịch hẹn #<?php echo $appointment['ma_lichhen']; ?></h3>
                <p class="text-muted mb-4">
                    <i class="fas fa-info-circle"></i>
                    Vui lòng chọn ngày và giờ mới cho lịch hẹn của bạn. 
                    Lịch mới sẽ được gửi đến bác sĩ để xác nhận.
                </p>
                
                <div class="alert alert-info mb-4">
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-calendar-alt fs-4"></i>
                        </div>
                        <div>
                            <strong>Lịch hẹn hiện tại:</strong><br>
                            <div class="mt-1">
                                Ngày: <strong><?php echo date('d/m/Y', strtotime($appointment['ngay_hen'])); ?></strong><br>
                                Giờ: <strong><?php echo $appointment['gio_hen']; ?></strong><br>
                                Bác sĩ: <strong><?php echo htmlspecialchars($appointment['bs_name']); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form action="xuly_doilich.php" method="post">
                    <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_date" class="form-label">Ngày mới <span class="text-danger">*</span></label>
                            <input type="date" id="new_date" name="new_date" class="form-control" 
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                            <small class="text-muted">Chỉ có thể đổi lịch từ ngày mai trở đi</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_time" class="form-label">Giờ mới <span class="text-danger">*</span></label>
                            <input type="time" id="new_time" name="new_time" class="form-control" 
                                   min="08:00" max="17:00" required>
                            <small class="text-muted">Giờ khám từ 8:00 - 17:00</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reschedule_reason" class="form-label">Lý do thay đổi <span class="text-danger">*</span></label>
                        <select id="reschedule_reason" name="reschedule_reason" class="form-select" required>
                            <option value="" selected disabled>Chọn lý do</option>
                            <option value="Bận việc">Bận việc không thể đến được</option>
                            <option value="Lịch phù hợp hơn">Thời gian mới phù hợp hơn</option>
                            <option value="Tình hình sức khỏe thay đổi">Tình hình sức khỏe thay đổi</option>
                            <option value="Khác">Lý do khác</option>
                        </select>
                    </div>
                    <div class="mb-4" id="other_reschedule_div" style="display:none;">
                        <label for="other_reschedule_reason" class="form-label">Chi tiết lý do <span class="text-danger">*</span></label>
                        <textarea id="other_reschedule_reason" name="other_reschedule_reason" class="form-control" rows="3" 
                            placeholder="Vui lòng cho chúng tôi biết chi tiết lý do bạn muốn thay đổi lịch hẹn"></textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="huy_lichhen.php?id=<?php echo $appointment_id; ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-check me-2"></i> Xác nhận thay đổi lịch
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle reason dropdown for cancellation
            const cancelReasonSelect = document.getElementById('cancel_reason');
            const otherCancelDiv = document.getElementById('other_cancel_div');
            const otherReasonInput = document.getElementById('other_reason');
            
            if (cancelReasonSelect && otherCancelDiv) {
                cancelReasonSelect.addEventListener('change', function() {
                    if (this.value === 'Khác') {
                        otherCancelDiv.style.display = 'block';
                        if (otherReasonInput) otherReasonInput.setAttribute('required', 'required');
                    } else {
                        otherCancelDiv.style.display = 'none';
                        if (otherReasonInput) otherReasonInput.removeAttribute('required');
                    }
                });
            }
            
            // Handle reason dropdown for rescheduling
            const rescheduleReasonSelect = document.getElementById('reschedule_reason');
            const otherRescheduleDiv = document.getElementById('other_reschedule_div');
            const otherRescheduleInput = document.getElementById('other_reschedule_reason');
            
            if (rescheduleReasonSelect && otherRescheduleDiv) {
                rescheduleReasonSelect.addEventListener('change', function() {
                    if (this.value === 'Khác') {
                        otherRescheduleDiv.style.display = 'block';
                        if (otherRescheduleInput) otherRescheduleInput.setAttribute('required', 'required');
                    } else {
                        otherRescheduleDiv.style.display = 'none';
                        if (otherRescheduleInput) otherRescheduleInput.removeAttribute('required');
                    }
                });
            }
        });
    </script>
</body>
</html>