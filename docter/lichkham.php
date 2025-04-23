
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Lịch khám - Bác sĩ</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/lichkham.css">
</head>
<body>
    <!-- Header/Navbar -->
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
              <!-- Sidebar -->
              <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Lịch khám bệnh</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="input-group me-2">
                            <input type="text" id="datePicker" class="form-control" placeholder="Chọn ngày">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-calendar"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Appointment Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card bg-primary text-white">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stats-info">
                                <h3>12</h3>
                                <p>Lịch hẹn hôm nay</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-success text-white">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h3>8</h3>
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
                                <h3>3</h3>
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
                                <h3>1</h3>
                                <p>Đã hủy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Filters -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <select class="form-select" id="statusFilter">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Chờ xác nhận</option>
                            <option value="confirmed">Đã xác nhận</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="timeFilter">
                            <option value="">Tất cả khung giờ</option>
                            <option value="morning">Buổi sáng</option>
                            <option value="afternoon">Buổi chiều</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Tìm kiếm bệnh nhân...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="table-responsive appointment-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Giờ hẹn</th>
                                <th>Thông tin bệnh nhân</th>
                                <th>Dịch vụ khám</th>
                                <th>Triệu chứng</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="appointment-time">
                                        <strong>08:00</strong>
                                        <small class="text-muted d-block">20/03/2024</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="patient-info">
                                        <h6 class="mb-0">Nguyễn Văn A</h6>
                                        <small class="text-muted">Nam - 35 tuổi</small>
                                        <small class="text-muted d-block">SĐT: 0123456789</small>
                                    </div>
                                </td>
                                <td>Khám tổng quát</td>
                                <td>Đau đầu, sốt nhẹ</td>
                                <td>
                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-success" onclick="confirmAppointment(1)">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="cancelAppointment(1)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- More appointment rows... -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Trước</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Sau</a>
                        </li>
                    </ul>
                </nav>
            </main>
        </div>
    </div>

    <!-- Chi tiết lịch khám -->
    <div class="modal fade" id="appointmentDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết lịch khám</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="appointment-detail">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin bệnh nhân</h6>
                                <p><strong>Họ tên:</strong> Nguyễn Văn A</p>
                                <p><strong>Giới tính:</strong> Nam</p>
                                <p><strong>Tuổi:</strong> 35</p>
                                <p><strong>Số điện thoại:</strong> 0123456789</p>
                                <p><strong>Email:</strong> nguyenvana@email.com</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Thông tin lịch khám</h6>
                                <p><strong>Ngày khám:</strong> 20/03/2024</p>
                                <p><strong>Giờ khám:</strong> 08:00</p>
                                <p><strong>Dịch vụ:</strong> Khám tổng quát</p>
                                <p><strong>Triệu chứng:</strong> Đau đầu, sốt nhẹ</p>
                                <p><strong>Ghi chú:</strong> Bệnh nhân có tiền sử huyết áp cao</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success">Xác nhận lịch khám</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Custom JS -->
    <script>
        // Initialize date picker
        flatpickr("#datePicker", {
            dateFormat: "d/m/Y",
            defaultDate: "today"
        });

        // Handle appointment confirmation
        function confirmAppointment(id) {
            if (confirm('Bạn có chắc chắn muốn xác nhận lịch khám này?')) {
                // Handle confirmation logic
                console.log('Confirming appointment:', id);
            }
        }

        // Handle appointment cancellation
        function cancelAppointment(id) {
            if (confirm('Bạn có chắc chắn muốn hủy lịch khám này?')) {
                // Handle cancellation logic
                console.log('Cancelling appointment:', id);
            }
        }
    </script>
</body>
</html> 