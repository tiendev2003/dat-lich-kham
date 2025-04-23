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
        .history-card {
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .history-header {
            background-color: #f8f9fa;
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
        }
        .history-content {
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
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container history-container">
        <div class="row">
            <div class="col-lg-3">
                <div class="history-card mb-4">
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
                        <a href="#" class="list-group-item list-group-item-action text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="history-card">
                    <div class="history-header">
                        <h2>Lịch sử đặt lịch khám</h2>
                    </div>
                    <div class="history-content">
                        <!-- Lọc và tìm kiếm -->
                        <div class="filter-section">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select class="form-select" id="statusFilter">
                                        <option value="all">Tất cả trạng thái</option>
                                        <option value="completed">Đã hoàn thành</option>
                                        <option value="confirmed">Đã xác nhận</option>
                                        <option value="cancelled">Đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="specialtyFilter">
                                        <option value="all">Tất cả chuyên khoa</option>
                                        <option value="rang-ham-mat">Răng Hàm Mặt</option>
                                        <option value="tim-mach">Tim Mạch</option>
                                        <option value="ho-hap">Hô Hấp</option>
                                        <option value="da-lieu">Da Liễu</option>
                                        <option value="mat">Mắt</option>
                                        <option value="xet-nghiem">Xét Nghiệm</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Tìm kiếm...">
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-4" id="historyTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                                    Tất cả
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button">
                                    Sắp tới
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button">
                                    Đã hoàn thành
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button">
                                    Đã hủy
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="historyTabContent">
                            <!-- Tất cả -->
                            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                                <!-- Lịch hẹn đã hoàn thành -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Nguyễn Thế Lâm - Răng Hàm Mặt</div>
                                        <div class="appointment-status status-completed">Đã hoàn thành</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 3, 15/03/2025 - 09:00
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám răng định kỳ</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-medical"></i></div>
                                            <div>Chẩn đoán: <span class="text-primary">Viêm nướu nhẹ</span></div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-medical"></i> Xem kết quả
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#ratingModal">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại
                                        </button>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">Đánh giá của bạn:</small>
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lịch hẹn đã xác nhận -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Nguyễn Thế Lâm - Răng Hàm Mặt</div>
                                        <div class="appointment-status status-confirmed">Đã xác nhận</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 2, 25/04/2025 - 10:30
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám răng định kỳ</div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Thay đổi lịch
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Hủy lịch hẹn
                                        </button>
                                    </div>
                                </div>

                                <!-- Lịch hẹn đã hủy -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Lê Văn Hùng - Hô Hấp</div>
                                        <div class="appointment-status status-cancelled">Đã hủy</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 5, 10/02/2025 - 15:30
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám ho, sốt nhẹ</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-info-circle"></i></div>
                                            <div>Lý do hủy: <span class="text-danger">Bận công việc đột xuất</span></div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại lịch hẹn
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Sắp tới -->
                            <div class="tab-pane fade" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                                <!-- Lịch hẹn đã xác nhận -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Nguyễn Thế Lâm - Răng Hàm Mặt</div>
                                        <div class="appointment-status status-confirmed">Đã xác nhận</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 2, 25/04/2025 - 10:30
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám răng định kỳ</div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Thay đổi lịch
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Hủy lịch hẹn
                                        </button>
                                    </div>
                                </div>

                                <!-- Lịch hẹn đã xác nhận 2 -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Trần Thị Mai - Tim Mạch</div>
                                        <div class="appointment-status status-confirmed">Chờ xác nhận</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 4, 27/04/2025 - 14:00
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám tim định kỳ</div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Thay đổi lịch
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i> Hủy lịch hẹn
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Đã hoàn thành -->
                            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                                <!-- Lịch hẹn đã hoàn thành -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Nguyễn Thế Lâm - Răng Hàm Mặt</div>
                                        <div class="appointment-status status-completed">Đã hoàn thành</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 3, 15/03/2025 - 09:00
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám răng định kỳ</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-medical"></i></div>
                                            <div>Chẩn đoán: <span class="text-primary">Viêm nướu nhẹ</span></div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-medical"></i> Xem kết quả
                                        </button>
                                        <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#ratingModal">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại
                                        </button>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted">Đánh giá của bạn:</small>
                                        <div class="rating-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lịch hẹn đã hoàn thành 2 -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Trần Thị Mai - Tim Mạch</div>
                                        <div class="appointment-status status-completed">Đã hoàn thành</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 4, 10/02/2025 - 14:00
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám tim định kỳ</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-comment-medical"></i></div>
                                            <div>Chẩn đoán: <span class="text-primary">Tăng huyết áp nhẹ</span></div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-medical"></i> Xem kết quả
                                        </button>
                                        <button class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-star"></i> Đánh giá
                                        </button>
                                        <button class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Đã hủy -->
                            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                                <!-- Lịch hẹn đã hủy -->
                                <div class="appointment-card">
                                    <div class="appointment-header">
                                        <div class="appointment-doctor">BS. Lê Văn Hùng - Hô Hấp</div>
                                        <div class="appointment-status status-cancelled">Đã hủy</div>
                                    </div>
                                    <div class="appointment-date">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ 5, 10/02/2025 - 15:30
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                                            <div>Phòng khám Lộc Bình - 67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                                            <div>Khám ho, sốt nhẹ</div>
                                        </div>
                                        <div class="appointment-detail">
                                            <div class="detail-icon"><i class="fas fa-info-circle"></i></div>
                                            <div>Lý do hủy: <span class="text-danger">Bận công việc đột xuất</span></div>
                                        </div>
                                    </div>
                                    <div class="appointment-actions">
                                        <button class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-calendar-plus"></i> Đặt lại lịch hẹn
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Phân trang -->
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
                    <form>
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold">Đánh giá chất lượng dịch vụ</label>
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
                            <label for="doctorRating" class="form-label">Bác sĩ</label>
                            <select class="form-select" id="doctorRating">
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="facilityRating" class="form-label">Cơ sở vật chất</label>
                            <select class="form-select" id="facilityRating">
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="serviceRating" class="form-label">Thái độ phục vụ</label>
                            <select class="form-select" id="serviceRating">
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="comment" class="form-label">Nhận xét của bạn</label>
                            <textarea class="form-control" id="comment" rows="3" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Gửi đánh giá</button>
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
        });
    </script>
</body>
</html>