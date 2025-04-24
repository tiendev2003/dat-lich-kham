<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Lấy thông tin bác sĩ đang đăng nhập
$user = get_logged_in_user();
$doctor_id = null;
$stmt = $conn->prepare("SELECT id, ho_ten, chuyenkhoa_id FROM bacsi WHERE nguoidung_id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $doctor_id = $doctor['id'];
    $doctor_name = $doctor['ho_ten'];
    $specialty_id = $doctor['chuyenkhoa_id'];
}

// Lấy số liệu thống kê cho trang tổng quan
// Lịch hẹn hôm nay
$sql_today = "SELECT COUNT(*) as count FROM lichhen WHERE bacsi_id = ? AND DATE(ngay_hen) = CURDATE()";
$stmt_today = $conn->prepare($sql_today);
$stmt_today->bind_param('i', $doctor_id);
$stmt_today->execute();
$appointments_today = $stmt_today->get_result()->fetch_assoc()['count'];

// Số bệnh nhân đã khám
$sql_patients = "SELECT COUNT(DISTINCT benhnhan_id) as count FROM lichhen WHERE bacsi_id = ? AND trang_thai = 'completed'";
$stmt_patients = $conn->prepare($sql_patients);
$stmt_patients->bind_param('i', $doctor_id);
$stmt_patients->execute();
$total_patients = $stmt_patients->get_result()->fetch_assoc()['count'];

// Số lịch hẹn đang chờ
$sql_pending = "SELECT COUNT(*) as count FROM lichhen WHERE bacsi_id = ? AND trang_thai = 'pending'";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->bind_param('i', $doctor_id);
$stmt_pending->execute();
$pending_appointments = $stmt_pending->get_result()->fetch_assoc()['count'];

// Số lịch hẹn đã hoàn thành
$sql_completed = "SELECT COUNT(*) as count FROM lichhen WHERE bacsi_id = ? AND trang_thai = 'completed'";
$stmt_completed = $conn->prepare($sql_completed);
$stmt_completed->bind_param('i', $doctor_id);
$stmt_completed->execute();
$completed_appointments = $stmt_completed->get_result()->fetch_assoc()['count'];

// Lấy dữ liệu lịch hẹn theo tháng cho biểu đồ
$sql_monthly = "SELECT MONTH(ngay_hen) as month, COUNT(*) as count 
               FROM lichhen 
               WHERE bacsi_id = ? AND YEAR(ngay_hen) = YEAR(CURDATE()) 
               GROUP BY MONTH(ngay_hen)";
$stmt_monthly = $conn->prepare($sql_monthly);
$stmt_monthly->bind_param('i', $doctor_id);
$stmt_monthly->execute();
$monthly_result = $stmt_monthly->get_result();

$monthly_data = array_fill(0, 12, 0); // Khởi tạo mảng 12 tháng với giá trị 0
while ($row = $monthly_result->fetch_assoc()) {
    $month_index = $row['month'] - 1; // Chuyển từ 1-12 sang 0-11 cho mảng
    $monthly_data[$month_index] = (int)$row['count'];
}

// Lấy các lịch hẹn sắp tới
$sql_upcoming = "SELECT lh.id, lh.ma_lichhen, bn.ho_ten as ten_benhnhan, 
              lh.ngay_hen, lh.gio_hen, lh.trang_thai, lh.ly_do
              FROM lichhen lh
              LEFT JOIN benhnhan bn ON lh.benhnhan_id = bn.id
              WHERE lh.bacsi_id = ? AND lh.trang_thai IN ('pending', 'confirmed')
              AND lh.ngay_hen >= CURDATE()
              ORDER BY lh.ngay_hen, lh.gio_hen
              LIMIT 5";
$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param('i', $doctor_id);
$stmt_upcoming->execute();
$upcoming_appointments = [];
$result_upcoming = $stmt_upcoming->get_result();
while ($row = $result_upcoming->fetch_assoc()) {
    $upcoming_appointments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - Bác sĩ Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
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
        @media (max-width: 991.98px) {
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
        @media (max-width: 767.98px) {
            .stats-card {
                margin-bottom: 15px;
            }
            .appointment-item .row {
                flex-direction: column;
            }
            .appointment-item .col-md-2,
            .appointment-item .col-md-3,
            .appointment-item .col-md-4 {
                margin-bottom: 10px;
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
            <main class="col main-content p-4">
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
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stats-number"><?php echo $total_patients; ?></div>
                            <div class="stats-title">Bệnh nhân đã khám</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card bg-warning-gradient">
                            <div class="stats-icon">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div class="stats-number"><?php echo $pending_appointments; ?></div>
                            <div class="stats-title">Lịch hẹn chờ xác nhận</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stats-card bg-danger-gradient">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-number"><?php echo $completed_appointments; ?></div>
                            <div class="stats-title">Lịch hẹn đã hoàn thành</div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="graph-card">
                            <h5 class="chart-title">Thống kê lịch hẹn theo tháng</h5>
                            <div class="chart-container">
                                <canvas id="monthlyAppointmentsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="appointments-card">
                            <div class="appointments-title d-flex justify-content-between align-items-center flex-wrap">
                                <span>Lịch hẹn sắp tới</span>
                                <a href="lichhen.php" class="btn btn-sm btn-outline-primary mt-2 mt-sm-0">Xem tất cả</a>
                            </div>
                            
                            <?php if (count($upcoming_appointments) > 0): ?>
                                <?php foreach ($upcoming_appointments as $appointment): ?>
                                    <div class="appointment-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 col-sm-6">
                                                <div><strong class="text-primary"><?php echo $appointment['ma_lichhen']; ?></strong></div>
                                                <div class="small text-muted">
                                                    <?php echo date('d/m/Y', strtotime($appointment['ngay_hen'])); ?> - 
                                                    <?php echo $appointment['gio_hen']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div><i class="fas fa-user me-1"></i> <?php echo $appointment['ten_benhnhan']; ?></div>
                                                <?php if (!empty($appointment['ly_do'])): ?>
                                                <div class="small text-muted mt-1"><i class="fas fa-comment-medical me-1"></i> <?php echo htmlspecialchars($appointment['ly_do']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-3 col-sm-6">
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
                                                        default:
                                                            $status_class = "bg-secondary";
                                                            $status_text = ucfirst($appointment['trang_thai']);
                                                    }
                                                ?>
                                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                            </div>
                                            <div class="col-md-2 col-sm-6 text-md-end mt-2 mt-md-0">
                                                <a href="lichhen.php?action=view&id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="p-4 text-center text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>Không có lịch hẹn sắp tới</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if(sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }
            
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }
            
            // Monthly appointments chart
            const monthlyData = <?php echo json_encode(array_values($monthly_data)); ?>;
            const monthLabels = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
                                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
            
            const monthlyCtx = document.getElementById('monthlyAppointmentsChart').getContext('2d');
            const monthlyAppointmentsChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Số lượng lịch hẹn',
                        data: monthlyData,
                        backgroundColor: 'rgba(13, 110, 253, 0.5)',
                        borderColor: 'rgb(13, 110, 253)',
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
        });
    </script>
</body>
</html>