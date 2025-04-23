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
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/lichkham.css">
    <style>
        .stats-card {
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            align-items: center;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        .stats-icon {
            font-size: 2.5rem;
            margin-right: 15px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(255,255,255,0.2);
        }
        .stats-info h3 {
            font-size: 2rem;
            margin-bottom: 0;
            font-weight: 600;
        }
        .stats-info p {
            margin-bottom: 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        .appointment-table {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .appointment-table thead {
            background-color: #f8f9fa;
        }
        .appointment-table th {
            font-weight: 600;
            border-bottom-width: 1px;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 15px;
        }
        .appointment-table td {
            padding: 15px;
            vertical-align: middle;
        }
        .appointment-time {
            text-align: center;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 8px;
        }
        .patient-info {
            display: flex;
            flex-direction: column;
        }
        .filter-container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .btn-action {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
        }
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .appointment-detail {
            padding: 15px;
        }
        .appointment-detail h6 {
            color: #495057;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .badge {
            padding: 8px 12px;
            font-weight: normal;
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header/Navbar -->
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4">
                    <h1 class="h2 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Lịch khám bệnh</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="input-group date-picker-container">
                            <input type="text" id="datePicker" class="form-control shadow-sm" placeholder="Chọn ngày">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-calendar me-1"></i> Xem
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Appointment Stats -->
                <div class="row mb-4 g-3">
                    <div class="col-md-3 col-6">
                        <div class="stats-card bg-primary text-white animate__animated animate__fadeIn">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stats-info">
                                <h3>12</h3>
                                <p>Lịch hẹn hôm nay</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stats-card bg-success text-white animate__animated animate__fadeIn animate__delay-1s">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h3>8</h3>
                                <p>Đã xác nhận</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stats-card bg-warning text-white animate__animated animate__fadeIn animate__delay-2s">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stats-info">
                                <h3>3</h3>
                                <p>Chờ xác nhận</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="stats-card bg-danger text-white animate__animated animate__fadeIn animate__delay-3s">
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
                <div class="filter-container mb-4">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label for="statusFilter" class="form-label small text-muted">Trạng thái</label>
                            <select class="form-select shadow-sm" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending">Chờ xác nhận</option>
                                <option value="confirmed">Đã xác nhận</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label for="timeFilter" class="form-label small text-muted">Khung giờ</label>
                            <select class="form-select shadow-sm" id="timeFilter">
                                <option value="">Tất cả khung giờ</option>
                                <option value="morning">Buổi sáng (7:00 - 12:00)</option>
                                <option value="afternoon">Buổi chiều (13:00 - 17:00)</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-12">
                            <label for="searchInput" class="form-label small text-muted">Tìm kiếm</label>
                            <div class="input-group">
                                <input type="text" class="form-control shadow-sm" id="searchInput" placeholder="Tìm theo tên, SĐT...">
                                <button class="btn btn-outline-primary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Danh sách lịch khám</h5>
                    </div>
                    <div class="table-responsive appointment-table">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th width="15%">Giờ hẹn</th>
                                    <th width="25%">Thông tin bệnh nhân</th>
                                    <th width="15%">Dịch vụ khám</th>
                                    <th width="20%">Triệu chứng</th>
                                    <th width="10%">Trạng thái</th>
                                    <th width="15%">Thao tác</th>
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
                                            <h6 class="mb-0 fw-bold">Nguyễn Văn A</h6>
                                            <small class="text-muted">Nam - 35 tuổi</small>
                                            <small class="text-muted d-block">SĐT: 0123456789</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info bg-opacity-10 text-info">Khám tổng quát</span></td>
                                    <td>Đau đầu, sốt nhẹ</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-action btn-success" onclick="confirmAppointment(1)" title="Xác nhận">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-action btn-danger" onclick="cancelAppointment(1)" title="Hủy lịch">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-action btn-info text-white" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="appointment-time">
                                            <strong>09:30</strong>
                                            <small class="text-muted d-block">20/03/2024</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="patient-info">
                                            <h6 class="mb-0 fw-bold">Trần Thị B</h6>
                                            <small class="text-muted">Nữ - 42 tuổi</small>
                                            <small class="text-muted d-block">SĐT: 0987654321</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary">Khám tim mạch</span></td>
                                    <td>Khó thở, đau ngực</td>
                                    <td>
                                        <span class="badge bg-success">Đã xác nhận</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-action btn-secondary" disabled title="Đã xác nhận">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-action btn-danger" onclick="cancelAppointment(2)" title="Hủy lịch">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-action btn-info text-white" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="appointment-time">
                                            <strong>10:15</strong>
                                            <small class="text-muted d-block">20/03/2024</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="patient-info">
                                            <h6 class="mb-0 fw-bold">Lê Văn C</h6>
                                            <small class="text-muted">Nam - 28 tuổi</small>
                                            <small class="text-muted d-block">SĐT: 0369852147</small>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success">Xét nghiệm máu</span></td>
                                    <td>Mệt mỏi kéo dài</td>
                                    <td>
                                        <span class="badge bg-danger">Đã hủy</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-action btn-success" onclick="confirmAppointment(3)" title="Xác nhận">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-action btn-secondary" disabled title="Đã hủy">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button class="btn btn-action btn-info text-white" data-bs-toggle="modal" data-bs-target="#appointmentDetailModal" title="Chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white py-3">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Chi tiết lịch khám -->
    <div class="modal fade" id="appointmentDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Chi tiết lịch khám</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="fas fa-clock me-2"></i>
                                <div>Trạng thái: <strong>Chờ xác nhận</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-detail">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-primary"><i class="fas fa-user me-2"></i>Thông tin bệnh nhân</h6>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Họ tên:</label>
                                            <div class="col-7">Nguyễn Văn A</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Giới tính:</label>
                                            <div class="col-7">Nam</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Tuổi:</label>
                                            <div class="col-7">35</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Số điện thoại:</label>
                                            <div class="col-7">0123456789</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Email:</label>
                                            <div class="col-7">nguyenvana@email.com</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Địa chỉ:</label>
                                            <div class="col-7">123 Đường ABC, Quận XYZ, TP. HCM</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="text-primary"><i class="fas fa-calendar-alt me-2"></i>Thông tin lịch khám</h6>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Ngày khám:</label>
                                            <div class="col-7">20/03/2024</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Giờ khám:</label>
                                            <div class="col-7">08:00</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Dịch vụ:</label>
                                            <div class="col-7">Khám tổng quát</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Triệu chứng:</label>
                                            <div class="col-7">Đau đầu, sốt nhẹ</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Tiền sử bệnh:</label>
                                            <div class="col-7">Huyết áp cao</div>
                                        </div>
                                        <div class="mb-2 row">
                                            <label class="col-5 fw-bold">Ghi chú:</label>
                                            <div class="col-7">Bệnh nhân có tiền sử huyết áp cao</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Đóng
                    </button>
                    <button type="button" class="btn btn-danger">
                        <i class="fas fa-ban me-1"></i> Hủy lịch khám
                    </button>
                    <button type="button" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Xác nhận lịch khám
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    <!-- Custom JS -->
    <script>
        // Initialize date picker with Vietnamese localization
        flatpickr("#datePicker", {
            dateFormat: "d/m/Y",
            defaultDate: "today",
            locale: "vn",
            onChange: function(selectedDates, dateStr, instance) {
                // Handle date change - you can fetch appointments for the selected date
                console.log('Selected date:', dateStr);
            }
        });

        // Handle appointment confirmation
        function confirmAppointment(id) {
            if (confirm('Bạn có chắc chắn muốn xác nhận lịch khám này?')) {
                // Handle confirmation logic
                console.log('Confirming appointment:', id);
                // Here you would typically make an AJAX call to update the database
                // After successful confirmation, you could update the UI
                showToast('Đã xác nhận lịch khám thành công!', 'success');
            }
        }

        // Handle appointment cancellation
        function cancelAppointment(id) {
            if (confirm('Bạn có chắc chắn muốn hủy lịch khám này?')) {
                // Handle cancellation logic
                console.log('Cancelling appointment:', id);
                // Here you would typically make an AJAX call to update the database
                // After successful cancellation, you could update the UI
                showToast('Đã hủy lịch khám!', 'danger');
            }
        }

        // Simple toast notification function
        function showToast(message, type) {
            // Create toast container if it doesn't exist
            if (!document.querySelector('.toast-container')) {
                const toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }
            
            const toastContainer = document.querySelector('.toast-container');
            
            // Create toast element
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');
            
            const toastContent = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            toastEl.innerHTML = toastContent;
            toastContainer.appendChild(toastEl);
            
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
            
            // Remove toast after it's hidden
            toastEl.addEventListener('hidden.bs.toast', function () {
                toastEl.remove();
            });
        }

        // Enable tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
</body>
</html>