<?php
// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Lấy số liệu thống kê cho trang tổng quan
// Lịch hẹn hôm nay
$sql_today = "SELECT COUNT(*) as count FROM lichhen WHERE DATE(ngay_hen) = CURDATE()";
$result_today = $conn->query($sql_today);
$appointments_today = $result_today->fetch_assoc()['count'];

// Tổng số bác sĩ
$sql_doctors = "SELECT COUNT(*) as count FROM bacsi";
$result_doctors = $conn->query($sql_doctors);
$total_doctors = $result_doctors->fetch_assoc()['count'];

// Tổng số bệnh nhân
$sql_patients = "SELECT COUNT(*) as count FROM benhnhan";
$result_patients = $conn->query($sql_patients);
$total_patients = $result_patients->fetch_assoc()['count'];

// Lịch hẹn chờ xác nhận
$sql_pending = "SELECT COUNT(*) as count FROM lichhen WHERE trang_thai = 'pending'";
$result_pending = $conn->query($sql_pending);
$pending_appointments = $result_pending->fetch_assoc()['count'];

// Lấy dữ liệu thống kê lịch hẹn theo tháng
$sql_monthly = "SELECT MONTH(ngay_hen) as month, COUNT(*) as count 
                FROM lichhen 
                WHERE YEAR(ngay_hen) = YEAR(CURDATE()) 
                GROUP BY MONTH(ngay_hen)
                ORDER BY MONTH(ngay_hen)";
$result_monthly = $conn->query($sql_monthly);
$monthly_data = [];
while ($row = $result_monthly->fetch_assoc()) {
    $monthly_data[$row['month']] = $row['count'];
}

// Lấp đầy các tháng không có dữ liệu
for ($i = 1; $i <= 12; $i++) {
    if (!isset($monthly_data[$i])) {
        $monthly_data[$i] = 0;
    }
}
ksort($monthly_data); // Sắp xếp theo key

// Lấy dữ liệu phân bố theo chuyên khoa
$sql_specialty = "SELECT ck.ten_chuyenkhoa, COUNT(lh.id) as count 
                  FROM lichhen lh
                  LEFT JOIN bacsi bs ON lh.bacsi_id = bs.id
                  LEFT JOIN chuyenkhoa ck ON bs.chuyenkhoa_id = ck.id
                  GROUP BY ck.id";
$result_specialty = $conn->query($sql_specialty);
$specialty_labels = [];
$specialty_data = [];
while ($row = $result_specialty->fetch_assoc()) {
    $specialty_labels[] = $row['ten_chuyenkhoa'] ?? 'Không xác định';
    $specialty_data[] = $row['count'];
}

// Lấy danh sách lịch hẹn gần đây
$sql_recent = "SELECT lh.id, lh.ma_lichhen, bn.ho_ten as ten_benhnhan, bs.ho_ten as ten_bacsi,
              lh.ngay_hen, lh.gio_hen, lh.trang_thai
              FROM lichhen lh
              LEFT JOIN benhnhan bn ON lh.benhnhan_id = bn.id
              LEFT JOIN bacsi bs ON lh.bacsi_id = bs.id
              ORDER BY lh.ngay_tao DESC
              LIMIT 5";
$result_recent = $conn->query($sql_recent);
$recent_appointments = [];
while ($row = $result_recent->fetch_assoc()) {
    $recent_appointments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - Quản trị hệ thống</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/admin.css">
    <style>
        .stats-card {
            position: relative;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            color: white;
            overflow: hidden;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            font-size: 30px;
            position: absolute;
            top: 20px;
            right: 20px;
            opacity: 0.8;
        }
        .stats-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .stats-title {
            font-size: 14px;
            opacity: 0.9;
        }
        .graph-card {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .appointments-card {
            border-radius: 10px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .appointments-title {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
        .appointment-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f5f5f5;
        }
        .appointment-item:last-child {
            border-bottom: none;
        }
        .appointment-item:hover {
            background-color: #f8f9fa;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .bg-primary-gradient {
            background: linear-gradient(45deg, #0d6efd, #198ae3);
        }
        .bg-success-gradient {
            background: linear-gradient(45deg, #198754, #20c997);
        }
        .bg-warning-gradient {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
        }
        .bg-danger-gradient {
            background: linear-gradient(45deg, #dc3545, #ff4d5e);
        }
        .chart-container {
            height: 300px;
        }
        .chart-title {
            font-weight: 600;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .stats-number {
                font-size: 24px;
            }
            .stats-icon {
                font-size: 24px;
            }
            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-12 main-content ms-sm-auto p-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Tổng quan</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card bg-primary-gradient">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stats-number"><?php echo $appointments_today; ?></div>
                            <div class="stats-title">Lịch hẹn hôm nay</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card bg-success-gradient">
                            <div class="stats-icon">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="stats-number"><?php echo $total_doctors; ?></div>
                            <div class="stats-title">Bác sĩ</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card bg-warning-gradient">
                            <div class="stats-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-number"><?php echo $total_patients; ?></div>
                            <div class="stats-title">Bệnh nhân</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card bg-danger-gradient">
                            <div class="stats-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="stats-number"><?php echo $pending_appointments; ?></div>
                            <div class="stats-title">Lịch hẹn chờ xác nhận</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row">
                    <!-- Monthly Appointments Chart -->
                    <div class="col-lg-8">
                        <div class="graph-card">
                            <h5 class="chart-title">Lịch hẹn theo tháng trong năm <?php echo date('Y'); ?></h5>
                            <div class="chart-container">
                                <canvas id="monthlyAppointmentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Specialty Distribution Chart -->
                    <div class="col-lg-4">
                        <div class="graph-card">
                            <h5 class="chart-title">Phân bố theo chuyên khoa</h5>
                            <div class="chart-container">
                                <canvas id="specialtyDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Appointments -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="appointments-card">
                            <div class="appointments-title d-flex justify-content-between align-items-center">
                                <span>Lịch hẹn gần đây</span>
                                <a href="lichhen.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                            </div>
                            <?php if (count($recent_appointments) > 0): ?>
                                <?php foreach ($recent_appointments as $appointment): ?>
                                    <div class="appointment-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 col-sm-6">
                                                <div><strong class="text-primary"><?php echo $appointment['ma_lichhen']; ?></strong></div>
                                                <div class="small text-muted">
                                                    <?php echo date('d/m/Y', strtotime($appointment['ngay_hen'])); ?> - 
                                                    <?php echo $appointment['gio_hen']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <div><i class="fas fa-user me-1"></i> <?php echo $appointment['ten_benhnhan']; ?></div>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
                                                <div><i class="fas fa-user-md me-1"></i> <?php echo $appointment['ten_bacsi']; ?></div>
                                            </div>
                                            <div class="col-md-3 col-sm-6 text-md-end">
                                                <?php
                                                    $status_class = "";
                                                    $status_text = "";
                                                    switch($appointment['trang_thai']) {
                                                        case 'pending':
                                                            $status_class = "bg-warning text-dark";
                                                            $status_text = "Chờ xác nhận";
                                                            break;
                                                        case 'confirmed':
                                                            $status_class = "bg-primary";
                                                            $status_text = "Đã xác nhận";
                                                            break;
                                                        case 'completed':
                                                            $status_class = "bg-success";
                                                            $status_text = "Đã hoàn thành";
                                                            break;
                                                        case 'cancelled':
                                                            $status_class = "bg-danger";
                                                            $status_text = "Đã hủy";
                                                            break;
                                                        default:
                                                            $status_class = "bg-secondary";
                                                            $status_text = "Không xác định";
                                                    }
                                                ?>
                                                <span class="status-badge <?php echo $status_class; ?>">
                                                    <?php echo $status_text; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="appointment-item text-center py-4">
                                    <p class="text-muted">Không có lịch hẹn gần đây</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Admin JS -->
    <script src="asset/admin.js"></script>
    <script>
        // Dữ liệu biểu đồ lịch hẹn theo tháng
        const monthlyData = <?php echo json_encode(array_values($monthly_data)); ?>;
        const monthLabels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
                            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        
        // Dữ liệu biểu đồ phân bố theo chuyên khoa
        const specialtyLabels = <?php echo json_encode($specialty_labels); ?>;
        const specialtyData = <?php echo json_encode($specialty_data); ?>;
        
        // Khởi tạo biểu đồ khi trang đã tải xong
        document.addEventListener('DOMContentLoaded', function() {
            // Biểu đồ lịch hẹn theo tháng
            const monthlyCtx = document.getElementById('monthlyAppointmentsChart').getContext('2d');
            const monthlyAppointmentsChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Số lượng lịch hẹn',
                        data: monthlyData,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
            
            // Biểu đồ phân bố theo chuyên khoa
            const specialtyCtx = document.getElementById('specialtyDistributionChart').getContext('2d');
            const specialtyDistributionChart = new Chart(specialtyCtx, {
                type: 'doughnut',
                data: {
                    labels: specialtyLabels,
                    datasets: [{
                        data: specialtyData,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(199, 199, 199, 0.7)',
                            'rgba(83, 102, 255, 0.7)',
                            'rgba(40, 159, 64, 0.7)',
                            'rgba(210, 99, 132, 0.7)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 206, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(153, 102, 255)',
                            'rgb(255, 159, 64)',
                            'rgb(199, 199, 199)',
                            'rgb(83, 102, 255)',
                            'rgb(40, 159, 64)',
                            'rgb(210, 99, 132)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
