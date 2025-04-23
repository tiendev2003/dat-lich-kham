<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Lịch Hẹn - Quản trị hệ thống</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/lichhen.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Quản lý Lịch Hẹn</h2>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
                                <i class="fas fa-file-export"></i> Xuất báo cáo
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-print"></i> In
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                            <i class="fas fa-plus"></i> Thêm lịch hẹn
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <div class="input-group">
                            <input type="text" id="datepicker" class="form-control" placeholder="Chọn ngày">
                            <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                        </div>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select class="form-select" id="doctorFilter">
                            <option value="">Tất cả bác sĩ</option>
                            <option value="1">BS. Nguyễn Thế Lâm</option>
                            <option value="2">BS. Trần Thị Mai</option>
                            <option value="3">BS. Lê Văn Hùng</option>
                            <option value="4">BS. Phạm Thị Hoa</option>
                            <option value="5">BS. Hoàng Văn Minh</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xác nhận</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <option value="completed">Đã hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Tìm kiếm...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card bg-primary text-white">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stats-info">
                                <h3>254</h3>
                                <p>Tổng lịch hẹn</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-success text-white">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h3>180</h3>
                                <p>Đã xác nhận</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-warning text-white">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stats-info">
                                <h3>45</h3>
                                <p>Chờ xác nhận</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-danger text-white">
                            <div class="stats-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h3>29</h3>
                                <p>Đã hủy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th>ID</th>
                                        <th>Bệnh nhân</th>
                                        <th>Bác sĩ</th>
                                        <th>Dịch vụ</th>
                                        <th>Ngày & Giờ</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Row 1: Pending -->
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </td>
                                        <td>#APT12345</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="../assets/img/avatar-default.jpg" alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Nguyễn Văn A</h6>
                                                    <small class="text-muted">0123456789</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>BS. Nguyễn Thế Lâm</td>
                                        <td>Khám răng hàm mặt</td>
                                        <td>26/04/2025<br>09:00 - 09:30</td>
                                        <td>
                                            <span class="badge bg-warning">Chờ xác nhận</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal"><i class="fas fa-eye"></i> Xem chi tiết</a></li>
                                                    <li><a class="dropdown-item text-success" href="#"><i class="fas fa-check"></i> Xác nhận</a></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-times"></i> Hủy lịch</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-edit"></i> Chỉnh sửa</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Row 2: Confirmed -->
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </td>
                                        <td>#APT12346</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="../assets/img/avatar-default.jpg" alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Trần Thị B</h6>
                                                    <small class="text-muted">0987654321</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>BS. Phạm Thị Hoa</td>
                                        <td>Khám da liễu</td>
                                        <td>27/04/2025<br>10:30 - 11:00</td>
                                        <td>
                                            <span class="badge bg-success">Đã xác nhận</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal"><i class="fas fa-eye"></i> Xem chi tiết</a></li>
                                                    <li><a class="dropdown-item text-primary" href="#"><i class="fas fa-check-double"></i> Hoàn thành</a></li>
                                                    <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-times"></i> Hủy lịch</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-edit"></i> Chỉnh sửa</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Row 3: Completed -->
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </td>
                                        <td>#APT12340</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="../assets/img/avatar-default.jpg" alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Lê Văn C</h6>
                                                    <small class="text-muted">0934567890</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>BS. Trần Thị Mai</td>
                                        <td>Khám tim mạch</td>
                                        <td>22/04/2025<br>14:00 - 14:30</td>
                                        <td>
                                            <span class="badge bg-primary">Đã hoàn thành</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton3" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton3">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal"><i class="fas fa-eye"></i> Xem chi tiết</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-medical"></i> Xem kết quả</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-history"></i> Lịch sử khám</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Row 4: Cancelled -->
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                        </td>
                                        <td>#APT12338</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="../assets/img/avatar-default.jpg" alt="Avatar" class="rounded-circle">
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Phạm Thị D</h6>
                                                    <small class="text-muted">0912345678</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>BS. Vũ Thị Lan</td>
                                        <td>Xét nghiệm máu</td>
                                        <td>20/04/2025<br>08:00 - 08:30</td>
                                        <td>
                                            <span class="badge bg-danger">Đã hủy</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton4" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton4">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewAppointmentModal"><i class="fas fa-eye"></i> Xem chi tiết</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-redo"></i> Đặt lại</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-trash-alt"></i> Xóa</a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- More rows here... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                Hiển thị 1-4 của 254 lịch hẹn
                            </div>
                            <nav aria-label="Page navigation">
                                <ul class="pagination mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Trước</a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Sau</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- View Appointment Modal -->
    <div class="modal fade" id="viewAppointmentModal" tabindex="-1" aria-labelledby="viewAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewAppointmentModalLabel">Chi tiết lịch hẹn #APT12345</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Thông tin bệnh nhân</h6>
                            <div class="mb-2">
                                <strong>Họ tên:</strong> Nguyễn Văn A
                            </div>
                            <div class="mb-2">
                                <strong>Số điện thoại:</strong> 0123456789
                            </div>
                            <div class="mb-2">
                                <strong>Email:</strong> nguyenvana@example.com
                            </div>
                            <div class="mb-2">
                                <strong>Giới tính:</strong> Nam
                            </div>
                            <div class="mb-2">
                                <strong>Ngày sinh:</strong> 15/05/1990
                            </div>
                            <div class="mb-2">
                                <strong>Địa chỉ:</strong> 123 Đường ABC, Phường XYZ, Quận ABC, TP. Hà Nội
                            </div>
                            <div class="mb-2">
                                <strong>CMND/CCCD:</strong> 123456789012
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Thông tin lịch hẹn</h6>
                            <div class="mb-2">
                                <strong>Dịch vụ:</strong> Khám răng hàm mặt
                            </div>
                            <div class="mb-2">
                                <strong>Bác sĩ:</strong> BS. Nguyễn Thế Lâm
                            </div>
                            <div class="mb-2">
                                <strong>Ngày khám:</strong> 26/04/2025
                            </div>
                            <div class="mb-2">
                                <strong>Giờ khám:</strong> 09:00 - 09:30
                            </div>
                            <div class="mb-2">
                                <strong>Phòng khám:</strong> Phòng 203, Tầng 2
                            </div>
                            <div class="mb-2">
                                <strong>Phí khám:</strong> 500.000đ
                            </div>
                            <div class="mb-2">
                                <strong>Trạng thái:</strong> <span class="badge bg-warning">Chờ xác nhận</span>
                            </div>
                            <div class="mb-2">
                                <strong>Ngày đặt lịch:</strong> 23/04/2025
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h6 class="mb-2">Triệu chứng / Lý do khám</h6>
                        <p>Đau răng hàm dưới bên phải, đau nhức nhiều vào ban đêm. Đã uống thuốc giảm đau nhưng không hiệu quả.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success">Xác nhận lịch hẹn</button>
                    <button type="button" class="btn btn-danger">Hủy lịch hẹn</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalLabel">Thêm lịch hẹn mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAppointmentForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên bệnh nhân</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Năm sinh</label>
                                <input type="number" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chuyên khoa</label>
                                <select class="form-select" required>
                                    <option value="">-- Chọn chuyên khoa --</option>
                                    <option value="1">Răng Hàm Mặt</option>
                                    <option value="2">Tim Mạch</option>
                                    <option value="3">Hô Hấp</option>
                                    <option value="4">Da Liễu</option>
                                    <option value="5">Mắt</option>
                                    <option value="6">Xét Nghiệm</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bác sĩ</label>
                                <select class="form-select" required>
                                    <option value="">-- Chọn bác sĩ --</option>
                                    <!-- Các tùy chọn bác sĩ sẽ được load động dựa vào chuyên khoa -->
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ngày khám</label>
                                <input type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giờ khám</label>
                                <select class="form-select" required>
                                    <option value="">-- Chọn giờ khám --</option>
                                    <option value="08:00">08:00 - 08:30</option>
                                    <option value="08:30">08:30 - 09:00</option>
                                    <option value="09:00">09:00 - 09:30</option>
                                    <option value="09:30">09:30 - 10:00</option>
                                    <option value="10:00">10:00 - 10:30</option>
                                    <option value="10:30">10:30 - 11:00</option>
                                    <option value="14:00">14:00 - 14:30</option>
                                    <option value="14:30">14:30 - 15:00</option>
                                    <option value="15:00">15:00 - 15:30</option>
                                    <option value="15:30">15:30 - 16:00</option>
                                    <option value="16:00">16:00 - 16:30</option>
                                    <option value="16:30">16:30 - 17:00</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Triệu chứng / Lý do khám</label>
                                <textarea class="form-control" rows="3"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giá dịch vụ</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="500.000">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Trạng thái</label>
                                <select class="form-select">
                                    <option value="pending" selected>Chờ xác nhận</option>
                                    <option value="confirmed">Đã xác nhận</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Thêm lịch hẹn</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Xuất báo cáo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Định dạng</label>
                        <select class="form-select">
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="pdf">PDF</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thời gian</label>
                        <select class="form-select">
                            <option value="today">Hôm nay</option>
                            <option value="yesterday">Hôm qua</option>
                            <option value="thisWeek">Tuần này</option>
                            <option value="thisMonth" selected>Tháng này</option>
                            <option value="lastMonth">Tháng trước</option>
                            <option value="custom">Tùy chỉnh</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includePatientDetails" checked>
                            <label class="form-check-label" for="includePatientDetails">
                                Bao gồm thông tin chi tiết bệnh nhân
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Tải xuống</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize datepicker
        flatpickr("#datepicker", {
            dateFormat: "d/m/Y",
        });

        // Handle "Select All" checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('tbody .form-check-input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>
</body>
</html>