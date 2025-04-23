<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - Bác sĩ</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="asset/dashboard.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Main Content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">Tổng quan</h2>
                
                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-primary text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-number">8</div>
                            <div>Lịch hẹn hôm nay</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-success text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="stat-number">120</div>
                            <div>Đã khám tháng này</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-info text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number">85</div>
                            <div>Bệnh nhân của tôi</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-warning text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-number">5</div>
                            <div>Chờ xác nhận</div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="chart-container p-3 bg-white rounded shadow-sm">
                            <h4>Thống kê lịch hẹn theo tuần</h4>
                            <canvas id="appointmentChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="chart-container p-3 bg-white rounded shadow-sm">
                            <h4>Phân loại bệnh nhân</h4>
                            <canvas id="patientChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="p-3 bg-white rounded shadow-sm mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Lịch hẹn hôm nay</h4>
                        <a href="lichkham.php" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Giờ</th>
                                    <th>Bệnh nhân</th>
                                    <th>Dịch vụ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>08:00</td>
                                    <td>Nguyễn Văn A</td>
                                    <td>Khám tổng quát</td>
                                    <td><span class="badge bg-success">Đã xác nhận</span></td>
                                    <td>
                                        <a href="ketqua.php?id=12345" class="btn btn-sm btn-primary">
                                            <i class="fas fa-notes-medical"></i> Nhập kết quả
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>09:30</td>
                                    <td>Lê Thị B</td>
                                    <td>Tái khám</td>
                                    <td><span class="badge bg-success">Đã xác nhận</span></td>
                                    <td>
                                        <a href="ketqua.php?id=12346" class="btn btn-sm btn-primary">
                                            <i class="fas fa-notes-medical"></i> Nhập kết quả
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>10:15</td>
                                    <td>Trần Văn C</td>
                                    <td>Khám chuyên khoa</td>
                                    <td><span class="badge bg-warning">Đang chờ</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-success me-1">
                                            <i class="fas fa-check"></i> Xác nhận
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i> Từ chối
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Patient Records -->
                <div class="p-3 bg-white rounded shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Bệnh nhân gần đây</h4>
                        <a href="benhnhan.php" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Họ tên</th>
                                    <th>Ngày khám</th>
                                    <th>Chẩn đoán</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Phạm Thị D</td>
                                    <td>22/04/2025</td>
                                    <td>Viêm họng</td>
                                    <td>
                                        <a href="ketqua.php?id=12340" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="donthuoc.php?id=12340" class="btn btn-sm btn-warning">
                                            <i class="fas fa-prescription"></i> Đơn thuốc
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hoàng Văn E</td>
                                    <td>21/04/2025</td>
                                    <td>Đau lưng</td>
                                    <td>
                                        <a href="ketqua.php?id=12339" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="donthuoc.php?id=12339" class="btn btn-sm btn-warning">
                                            <i class="fas fa-prescription"></i> Đơn thuốc
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Vũ Thị F</td>
                                    <td>20/04/2025</td>
                                    <td>Dị ứng theo mùa</td>
                                    <td>
                                        <a href="ketqua.php?id=12338" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="donthuoc.php?id=12338" class="btn btn-sm btn-warning">
                                            <i class="fas fa-prescription"></i> Đơn thuốc
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Charts Initialization -->
    <script>
        // Appointments Chart
        const appointmentCtx = document.getElementById('appointmentChart').getContext('2d');
        const appointmentChart = new Chart(appointmentCtx, {
            type: 'line',
            data: {
                labels: ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'],
                datasets: [{
                    label: 'Số lượng lịch hẹn',
                    data: [12, 15, 8, 10, 14, 6, 2],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Tuần hiện tại'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Patient Distribution Chart
        const patientCtx = document.getElementById('patientChart').getContext('2d');
        const patientChart = new Chart(patientCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lần đầu', 'Tái khám', 'Khám định kỳ', 'Cấp cứu'],
                datasets: [{
                    data: [25, 40, 30, 5],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
    </script>
</body>
</html>