<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - Quản trị hệ thống</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/tongquan.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <h2 class="mb-4">Tổng quan hệ thống</h2>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-primary text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-number">150</div>
                            <div>Lịch hẹn hôm nay</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-success text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="stat-number">25</div>
                            <div>Bác sĩ</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-info text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number">1,250</div>
                            <div>Bệnh nhân</div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="dashboard-card bg-warning text-white p-4">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-number">45</div>
                            <div>Chờ xác nhận</div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h4>Thống kê lịch hẹn theo tháng</h4>
                            <canvas id="appointmentChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <h4>Phân bố theo chuyên khoa</h4>
                            <canvas id="specialtyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="recent-appointments">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Lịch hẹn gần đây</h4>
                        <a href="appointments.php" class="btn btn-primary btn-sm">Xem tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Bệnh nhân</th>
                                    <th>Bác sĩ</th>
                                    <th>Ngày giờ</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#12345</td>
                                    <td>Nguyễn Văn A</td>
                                    <td>BS. Trần Thị B</td>
                                    <td>15/03/2024 09:00</td>
                                    <td><span class="badge bg-success">Đã xác nhận</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-info"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
                                    </td>
                                </tr>
                                <!-- Thêm các hàng khác tương tự -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biểu đồ thống kê lịch hẹn
        const appointmentChart = new Chart(
            document.getElementById('appointmentChart'),
            {
                type: 'line',
                data: {
                    labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                    datasets: [{
                        label: 'Số lượng lịch hẹn',
                        data: [65, 59, 80, 81, 56, 55, 40, 88, 96, 67, 71, 90],
                        fill: false,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1
                    }]
                }
            }
        );

        // Biểu đồ phân bố chuyên khoa
        const specialtyChart = new Chart(
            document.getElementById('specialtyChart'),
            {
                type: 'doughnut',
                data: {
                    labels: ['Tim mạch', 'Nhi khoa', 'Da liễu', 'Nội tổng quát', 'Mắt'],
                    datasets: [{
                        data: [30, 20, 15, 25, 10],
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF'
                        ]
                    }]
                }
            }
        );
    </script>
</body>
</html>
