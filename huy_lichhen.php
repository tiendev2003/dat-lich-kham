<?php
// Start session
include 'includes/header.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php?redirect=huy_lichhen.php');
    exit();
}

// Lấy ID lịch hẹn từ query string nếu có
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin chi tiết về lịch hẹn nếu có ID
$appointment_details = null;
if ($appointment_id > 0) {
    // Trong trường hợp thực tế, bạn sẽ truy vấn database để lấy thông tin
    // Code mẫu này giả định bạn đã có kết nối với database
    // $query = "SELECT * FROM lichhen WHERE id = ? AND user_id = ?";
    // $stmt = $conn->prepare($query);
    // $stmt->bind_param("ii", $appointment_id, $_SESSION['user_id']);
    // $stmt->execute();
    // $result = $stmt->get_result();
    // if ($result->num_rows > 0) {
    //     $appointment_details = $result->fetch_assoc();
    // }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hủy hoặc thay đổi lịch hẹn - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .appointment-list {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
        }
        .appointment-card {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .appointment-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .appointment-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-completed {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-canceled {
            background-color: #f8d7da;
            color: #721c24;
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
        .tab-content {
            padding: 25px 0;
        }
        .nav-tabs .nav-link.active {
            font-weight: 600;
            color: #0d6efd;
            border-bottom: 3px solid #0d6efd;
        }
        .cancel-form {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        .reschedule-section {
            background-color: #e9f0ff;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        .time-slot {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .time-slot:hover {
            background-color: #e9ecef;
        }
        .time-slot.selected {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }
        .time-slot.unavailable {
            background-color: #f8f9fa;
            color: #adb5bd;
            cursor: not-allowed;
        }
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        .empty-state-icon {
            font-size: 80px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        .policy-alert {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Appointment Header -->
    <section class="appointment-header">
        <div class="container">
            <h1>Quản lý lịch hẹn</h1>
            <p class="lead">Hủy hoặc thay đổi lịch hẹn khám bệnh của bạn</p>
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

        <?php if (!$appointment_id): ?>
        <!-- Danh sách lịch hẹn -->
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="list-group">
                    <a href="user_profile.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-circle me-2"></i> Thông tin tài khoản
                    </a>
                    <a href="lichsu_datlich.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i> Lịch sử đặt lịch
                    </a>
                    <a href="huy_lichhen.php" class="list-group-item list-group-item-action active">
                        <i class="fas fa-calendar-times me-2"></i> Hủy/thay đổi lịch hẹn
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-bell me-2"></i> Thông báo
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-file-medical me-2"></i> Hồ sơ bệnh án
                    </a>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="appointment-list">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Các lịch hẹn sắp tới</h3>
                        <a href="datlich.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i> Đặt lịch mới
                        </a>
                    </div>

                    <ul class="nav nav-tabs mb-4" id="appointmentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" 
                                    data-bs-target="#upcomingAppointments" type="button" role="tab">
                                Sắp tới (3)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="past-tab" data-bs-toggle="tab" 
                                    data-bs-target="#pastAppointments" type="button" role="tab">
                                Đã hoàn thành
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="canceled-tab" data-bs-toggle="tab" 
                                    data-bs-target="#canceledAppointments" type="button" role="tab">
                                Đã hủy
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="appointmentTabContent">
                        <!-- Lịch hẹn sắp tới -->
                        <div class="tab-pane fade show active" id="upcomingAppointments" role="tabpanel">
                            <div class="appointment-card">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="h4 mb-0">28</div>
                                        <div>Tháng 4</div>
                                        <div>2025</div>
                                    </div>
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <h5>Khám Răng Hàm Mặt</h5>
                                        <p class="mb-1"><i class="fas fa-user-md me-2"></i> BS. Nguyễn Thế Lâm</p>
                                        <p class="mb-1"><i class="far fa-clock me-2"></i> 09:30 - 10:00</p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Phòng khám số 3, Tầng 2</p>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="appointment-status status-confirmed mb-2">Đã xác nhận</div>
                                        <a href="huy_lichhen.php?id=101" class="btn btn-outline-primary btn-sm mb-2">
                                            Chi tiết
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal101">
                                            Hủy lịch
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="appointment-card">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="h4 mb-0">02</div>
                                        <div>Tháng 5</div>
                                        <div>2025</div>
                                    </div>
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <h5>Khám Nội tổng quát</h5>
                                        <p class="mb-1"><i class="fas fa-user-md me-2"></i> BS. Lê Văn Hùng</p>
                                        <p class="mb-1"><i class="far fa-clock me-2"></i> 14:00 - 14:30</p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Phòng khám số 5, Tầng 1</p>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="appointment-status status-confirmed mb-2">Đã xác nhận</div>
                                        <a href="huy_lichhen.php?id=102" class="btn btn-outline-primary btn-sm mb-2">
                                            Chi tiết
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal102">
                                            Hủy lịch
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="appointment-card">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="h4 mb-0">15</div>
                                        <div>Tháng 5</div>
                                        <div>2025</div>
                                    </div>
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <h5>Khám Tim mạch</h5>
                                        <p class="mb-1"><i class="fas fa-user-md me-2"></i> BS. Trần Thị Hoa</p>
                                        <p class="mb-1"><i class="far fa-clock me-2"></i> 10:30 - 11:15</p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Phòng khám số 8, Tầng 3</p>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="appointment-status status-pending mb-2">Chờ xác nhận</div>
                                        <a href="huy_lichhen.php?id=103" class="btn btn-outline-primary btn-sm mb-2">
                                            Chi tiết
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal103">
                                            Hủy lịch
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lịch hẹn đã hoàn thành -->
                        <div class="tab-pane fade" id="pastAppointments" role="tabpanel">
                            <div class="appointment-card">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="h4 mb-0">15</div>
                                        <div>Tháng 3</div>
                                        <div>2025</div>
                                    </div>
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <h5>Khám Răng Hàm Mặt</h5>
                                        <p class="mb-1"><i class="fas fa-user-md me-2"></i> BS. Nguyễn Thế Lâm</p>
                                        <p class="mb-1"><i class="far fa-clock me-2"></i> 09:30 - 10:00</p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Phòng khám số 3, Tầng 2</p>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="appointment-status status-completed mb-2">Đã hoàn thành</div>
                                        <a href="lichsu_datlich.php?id=98" class="btn btn-outline-primary btn-sm mb-2">
                                            Xem chi tiết
                                        </a>
                                        <button class="btn btn-primary btn-sm">
                                            Đặt lại
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="appointment-card">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="h4 mb-0">02</div>
                                        <div>Tháng 2</div>
                                        <div>2025</div>
                                    </div>
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <h5>Khám Nội tổng quát</h5>
                                        <p class="mb-1"><i class="fas fa-user-md me-2"></i> BS. Lê Văn Hùng</p>
                                        <p class="mb-1"><i class="far fa-clock me-2"></i> 14:00 - 14:30</p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Phòng khám số 5, Tầng 1</p>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="appointment-status status-completed mb-2">Đã hoàn thành</div>
                                        <a href="lichsu_datlich.php?id=95" class="btn btn-outline-primary btn-sm mb-2">
                                            Xem chi tiết
                                        </a>
                                        <button class="btn btn-primary btn-sm">
                                            Đặt lại
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lịch hẹn đã hủy -->
                        <div class="tab-pane fade" id="canceledAppointments" role="tabpanel">
                            <div class="appointment-card">
                                <div class="row align-items-center">
                                    <div class="col-md-2 text-center mb-3 mb-md-0">
                                        <div class="h4 mb-0">10</div>
                                        <div>Tháng 4</div>
                                        <div>2025</div>
                                    </div>
                                    <div class="col-md-7 mb-3 mb-md-0">
                                        <h5>Khám Mắt</h5>
                                        <p class="mb-1"><i class="fas fa-user-md me-2"></i> BS. Trần Minh Tuấn</p>
                                        <p class="mb-1"><i class="far fa-clock me-2"></i> 15:30 - 16:00</p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> Phòng khám số 7, Tầng 2</p>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="appointment-status status-canceled mb-2">Đã hủy</div>
                                        <p class="text-muted small mb-2">Hủy bởi: Bạn</p>
                                        <button class="btn btn-primary btn-sm">
                                            Đặt lại
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="policy-alert">
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle me-2"></i>Chính sách hủy lịch hẹn</h5>
                            <ul class="mb-0">
                                <li>Bạn có thể hủy hoặc thay đổi lịch hẹn trước 24 giờ so với thời gian khám mà không bị tính phí.</li>
                                <li>Hủy lịch trong vòng 24 giờ trước thời gian khám có thể bị tính phí hủy muộn (100.000đ).</li>
                                <li>Lịch hẹn không đến mà không thông báo sẽ bị tính toàn bộ phí khám.</li>
                                <li>Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ tổng đài 1900 1234.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Chi tiết lịch hẹn và form hủy/thay đổi -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="detail-card">
                    <div class="detail-header d-flex justify-content-between align-items-center">
                        <h3>Chi tiết lịch hẹn #101</h3>
                        <div class="appointment-status status-confirmed">Đã xác nhận</div>
                    </div>

                    <div class="detail-section">
                        <h4 class="mb-3">Thông tin lịch hẹn</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Chuyên khoa</div>
                                    <div>Răng Hàm Mặt</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Bác sĩ</div>
                                    <div>BS. Nguyễn Thế Lâm</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Ngày hẹn</div>
                                    <div>28/04/2025</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Thời gian</div>
                                    <div>09:30 - 10:00</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Địa điểm</div>
                                    <div>Phòng khám số 3, Tầng 2</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <div class="detail-label">Phí khám</div>
                                    <div>300.000 VNĐ</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="detail-section">
                        <h4 class="mb-3">Lý do khám</h4>
                        <p>Đau nhức răng hàm dưới bên phải, sưng nướu và đôi khi bị nhức đầu.</p>
                    </div>

                    <div class="detail-section">
                        <h4 class="mb-3">Hành động</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                    <i class="fas fa-calendar-alt me-2"></i> Thay đổi lịch hẹn
                                </button>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times-circle me-2"></i> Hủy lịch hẹn
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="huy_lichhen.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Modal xác nhận hủy lịch hẹn -->
        <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">Xác nhận hủy lịch hẹn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Bạn có chắc chắn muốn hủy lịch hẹn này không?
                        </div>
                        <p>Thông tin lịch hẹn:</p>
                        <ul>
                            <li><strong>Chuyên khoa:</strong> Răng Hàm Mặt</li>
                            <li><strong>Bác sĩ:</strong> BS. Nguyễn Thế Lâm</li>
                            <li><strong>Ngày giờ:</strong> 28/04/2025, 09:30 - 10:00</li>
                        </ul>
                        <form action="xuly_huylich.php" method="post">
                            <input type="hidden" name="appointment_id" value="101">
                            <div class="mb-3">
                                <label for="cancelReason" class="form-label">Lý do hủy lịch</label>
                                <select class="form-select" id="cancelReason" name="cancel_reason" required>
                                    <option value="" selected disabled>Chọn lý do</option>
                                    <option value="Không thể đến được vào thời gian này">Không thể đến được vào thời gian này</option>
                                    <option value="Đã khỏi bệnh">Đã khỏi bệnh</option>
                                    <option value="Muốn thay đổi bác sĩ">Muốn thay đổi bác sĩ</option>
                                    <option value="Muốn thay đổi chuyên khoa">Muốn thay đổi chuyên khoa</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                            <div class="mb-3" id="otherReasonDiv" style="display: none;">
                                <label for="otherReason" class="form-label">Lý do khác</label>
                                <textarea class="form-control" id="otherReason" name="other_reason" rows="3"></textarea>
                            </div>
                        </form>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i> Lưu ý: Hủy lịch hẹn trong vòng 24 giờ trước thời gian khám có thể bị tính phí hủy muộn.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-danger" id="confirmCancelBtn">Xác nhận hủy lịch</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal thay đổi lịch hẹn -->
        <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rescheduleModalLabel">Thay đổi lịch hẹn</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-primary">
                            <i class="fas fa-info-circle me-2"></i> Bạn đang thay đổi lịch hẹn khám Răng Hàm Mặt với BS. Nguyễn Thế Lâm
                        </div>

                        <form action="xuly_doilich.php" method="post">
                            <input type="hidden" name="appointment_id" value="101">
                            
                            <div class="mb-3">
                                <label for="newDate" class="form-label">Chọn ngày mới</label>
                                <input type="date" class="form-control" id="newDate" name="new_date" min="2025-04-24" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Chọn thời gian mới</label>
                                <div class="d-flex flex-wrap">
                                    <div class="time-slot">08:00 - 08:30</div>
                                    <div class="time-slot">08:30 - 09:00</div>
                                    <div class="time-slot unavailable">09:00 - 09:30</div>
                                    <div class="time-slot unavailable">09:30 - 10:00</div>
                                    <div class="time-slot">10:00 - 10:30</div>
                                    <div class="time-slot">10:30 - 11:00</div>
                                    <div class="time-slot">11:00 - 11:30</div>
                                    <div class="time-slot unavailable">11:30 - 12:00</div>
                                    <div class="time-slot">14:00 - 14:30</div>
                                    <div class="time-slot">14:30 - 15:00</div>
                                    <div class="time-slot unavailable">15:00 - 15:30</div>
                                    <div class="time-slot">15:30 - 16:00</div>
                                    <div class="time-slot">16:00 - 16:30</div>
                                    <div class="time-slot">16:30 - 17:00</div>
                                </div>
                                <input type="hidden" name="new_time" id="selectedTime" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="rescheduleReason" class="form-label">Lý do thay đổi</label>
                                <select class="form-select" id="rescheduleReason" name="reschedule_reason" required>
                                    <option value="" selected disabled>Chọn lý do</option>
                                    <option value="Bận việc đột xuất">Bận việc đột xuất</option>
                                    <option value="Lịch phù hợp hơn">Lịch phù hợp hơn</option>
                                    <option value="Lý do sức khỏe">Lý do sức khỏe</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                            
                            <div class="mb-3" id="otherRescheduleReasonDiv" style="display: none;">
                                <label for="otherRescheduleReason" class="form-label">Lý do khác</label>
                                <textarea class="form-control" id="otherRescheduleReason" name="other_reschedule_reason" rows="3"></textarea>
                            </div>
                        </form>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i> Lưu ý: Thay đổi lịch hẹn trong vòng 24 giờ trước thời gian khám có thể bị tính phí thay đổi muộn.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-primary" id="confirmRescheduleBtn">Xác nhận thay đổi</button>
                    </div>
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
            // Xử lý khi chọn "Lý do khác" trong form hủy lịch
            document.getElementById('cancelReason').addEventListener('change', function() {
                const otherReasonDiv = document.getElementById('otherReasonDiv');
                if (this.value === 'Khác') {
                    otherReasonDiv.style.display = 'block';
                } else {
                    otherReasonDiv.style.display = 'none';
                }
            });

            // Xử lý khi chọn "Lý do khác" trong form đổi lịch
            document.getElementById('rescheduleReason').addEventListener('change', function() {
                const otherRescheduleReasonDiv = document.getElementById('otherRescheduleReasonDiv');
                if (this.value === 'Khác') {
                    otherRescheduleReasonDiv.style.display = 'block';
                } else {
                    otherRescheduleReasonDiv.style.display = 'none';
                }
            });

            // Xử lý khi chọn time slot
            const timeSlots = document.querySelectorAll('.time-slot:not(.unavailable)');
            timeSlots.forEach(slot => {
                slot.addEventListener('click', function() {
                    // Bỏ chọn tất cả các slot khác
                    timeSlots.forEach(s => s.classList.remove('selected'));
                    
                    // Chọn slot hiện tại
                    this.classList.add('selected');
                    
                    // Cập nhật giá trị input hidden
                    document.getElementById('selectedTime').value = this.innerText;
                });
            });

            // Xử lý nút xác nhận hủy lịch
            document.getElementById('confirmCancelBtn').addEventListener('click', function() {
                // Giả lập submit form
                alert('Đã hủy lịch thành công! Bạn sẽ nhận được email xác nhận.');
                window.location.href = 'huy_lichhen.php';
            });

            // Xử lý nút xác nhận đổi lịch
            document.getElementById('confirmRescheduleBtn').addEventListener('click', function() {
                // Kiểm tra đã chọn thời gian mới chưa
                const selectedTime = document.getElementById('selectedTime').value;
                if (!selectedTime) {
                    alert('Vui lòng chọn thời gian mới cho lịch hẹn!');
                    return;
                }

                // Giả lập submit form
                alert('Đã thay đổi lịch hẹn thành công! Bạn sẽ nhận được email xác nhận lịch hẹn mới.');
                window.location.href = 'huy_lichhen.php';
            });
        });
    </script>
</body>
</html>