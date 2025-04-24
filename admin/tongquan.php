<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Lấy số liệu thống kê cho trang tổng quan
// Tổng số bác sĩ
$sql_doctors = "SELECT COUNT(*) as count FROM bacsi";
$result_doctors = $conn->query($sql_doctors);
$total_doctors = $result_doctors->fetch_assoc()['count'];

// Tổng số bệnh nhân
$sql_patients = "SELECT COUNT(*) as count FROM benhnhan";
$result_patients = $conn->query($sql_patients);
$total_patients = $result_patients->fetch_assoc()['count'];

// Tổng số chuyên khoa
$sql_specialties = "SELECT COUNT(*) as count FROM chuyenkhoa";
$result_specialties = $conn->query($sql_specialties);
$total_specialties = $result_specialties->fetch_assoc()['count'];

// Tổng số dịch vụ
$sql_services = "SELECT COUNT(*) as count FROM dichvu";
$result_services = $conn->query($sql_services);
$total_services = $result_services->fetch_assoc()['count'];

// Lấy dữ liệu thống kê bác sĩ theo chuyên khoa
$sql_by_specialty = "SELECT ck.ten_chuyenkhoa, COUNT(bs.id) as count 
                 FROM bacsi bs 
                 JOIN chuyenkhoa ck ON bs.chuyenkhoa_id = ck.id 
                 GROUP BY ck.id";
$result_by_specialty = $conn->query($sql_by_specialty);
$specialty_labels = [];
$specialty_data = [];
while ($row = $result_by_specialty->fetch_assoc()) {
    $specialty_labels[] = $row['ten_chuyenkhoa'];
    $specialty_data[] = $row['count'];
}

// Lấy dữ liệu thống kê bệnh nhân theo giới tính
$sql_gender = "SELECT gioi_tinh, COUNT(*) as count FROM benhnhan GROUP BY gioi_tinh";
$result_gender = $conn->query($sql_gender);
$gender_data = [0, 0, 0]; // Nam, Nữ, Khác
while ($row = $result_gender->fetch_assoc()) {
    if (strtolower($row['gioi_tinh']) == 'nam') {
        $gender_data[0] = $row['count'];
    } elseif (strtolower($row['gioi_tinh']) == 'nữ' || strtolower($row['gioi_tinh']) == 'nu') {
        $gender_data[1] = $row['count'];
    } else {
        $gender_data[2] = $row['count'];
    }
}

// Lấy dữ liệu thống kê bệnh nhân theo độ tuổi
$current_year = date('Y');
$sql_age = "SELECT 
             SUM(CASE WHEN ($current_year - nam_sinh) < 18 THEN 1 ELSE 0 END) as under_18,
             SUM(CASE WHEN ($current_year - nam_sinh) BETWEEN 18 AND 30 THEN 1 ELSE 0 END) as from_18_to_30,
             SUM(CASE WHEN ($current_year - nam_sinh) BETWEEN 31 AND 45 THEN 1 ELSE 0 END) as from_31_to_45,
             SUM(CASE WHEN ($current_year - nam_sinh) BETWEEN 46 AND 60 THEN 1 ELSE 0 END) as from_46_to_60,
             SUM(CASE WHEN ($current_year - nam_sinh) > 60 THEN 1 ELSE 0 END) as over_60
             FROM benhnhan";
$result_age = $conn->query($sql_age);
$age_data = $result_age->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - Phòng khám Lộc Bình</title>
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

        .chart-container {
            height: 300px;
        }

        .chart-title {
            font-weight: 600;
            margin-bottom: 15px;
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

        .bg-info-gradient {
            background: linear-gradient(45deg, #0dcaf0, #0b96cc);
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
            <div class="col-md-12 main-content mt-5">
                <div class="content-wrapper">

                    <div class="content-header d-flex justify-content-between align-items-center">
                        <h2 class="page-title">Tổng quan</h2>
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
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="stats-number"><?php echo $total_doctors; ?></div>
                                <div class="stats-title">Bác sĩ</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stats-card bg-success-gradient">
                                <div class="stats-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stats-number"><?php echo $total_patients; ?></div>
                                <div class="stats-title">Bệnh nhân</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stats-card bg-warning-gradient">
                                <div class="stats-icon">
                                    <i class="fas fa-stethoscope"></i>
                                </div>
                                <div class="stats-number"><?php echo $total_specialties; ?></div>
                                <div class="stats-title">Chuyên khoa</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="stats-card bg-info-gradient">
                                <div class="stats-icon">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <div class="stats-number"><?php echo $total_services; ?></div>
                                <div class="stats-title">Dịch vụ</div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row">
                        <!-- Doctors by Specialty Chart -->
                        <div class="col-lg-6">
                            <div class="graph-card">
                                <h5 class="chart-title">Bác sĩ theo chuyên khoa</h5>
                                <div class="chart-container">
                                    <canvas id="doctorsBySpecialtyChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Patients by Gender Chart -->
                        <div class="col-lg-6">
                            <div class="graph-card">
                                <h5 class="chart-title">Phân bố bệnh nhân theo giới tính</h5>
                                <div class="chart-container">
                                    <canvas id="patientsByGenderChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Age Distribution Chart -->
                    <div class="row">
                        <div class="col-12">
                            <div class="graph-card">
                                <h5 class="chart-title">Phân bố bệnh nhân theo độ tuổi</h5>
                                <div class="chart-container">
                                    <canvas id="patientsByAgeChart"></canvas>
                                </div>
                            </div>
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
        // Dữ liệu biểu đồ bác sĩ theo chuyên khoa
        const specialtyLabels = <?php echo json_encode($specialty_labels); ?>;
        const specialtyData = <?php echo json_encode($specialty_data); ?>;

        // Dữ liệu biểu đồ bệnh nhân theo giới tính
        const genderData = <?php echo json_encode($gender_data); ?>;
        const genderLabels = ['Nam', 'Nữ', 'Khác'];

        // Dữ liệu biểu đồ phân bố độ tuổi
        const ageData = [
            <?php echo $age_data['under_18'] ?? 0; ?>,
            <?php echo $age_data['from_18_to_30'] ?? 0; ?>,
            <?php echo $age_data['from_31_to_45'] ?? 0; ?>,
            <?php echo $age_data['from_46_to_60'] ?? 0; ?>,
            <?php echo $age_data['over_60'] ?? 0; ?>
        ];
        const ageLabels = ['<18', '18-30', '31-45', '46-60', '>60'];

        // Khởi tạo biểu đồ khi trang đã tải xong
        document.addEventListener('DOMContentLoaded', function () {
            // Biểu đồ bác sĩ theo chuyên khoa
            const specialtyCtx = document.getElementById('doctorsBySpecialtyChart').getContext('2d');
            const doctorsBySpecialtyChart = new Chart(specialtyCtx, {
                type: 'pie',
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
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });

            // Biểu đồ bệnh nhân theo giới tính
            const genderCtx = document.getElementById('patientsByGenderChart').getContext('2d');
            const patientsByGenderChart = new Chart(genderCtx, {
                type: 'doughnut',
                data: {
                    labels: genderLabels,
                    datasets: [{
                        data: genderData,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgb(54, 162, 235)',
                            'rgb(255, 99, 132)',
                            'rgb(153, 102, 255)'
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
                        },
                        title: {
                            display: false
                        }
                    }
                }
            });

            // Biểu đồ phân bố độ tuổi
            const ageCtx = document.getElementById('patientsByAgeChart').getContext('2d');
            const patientsByAgeChart = new Chart(ageCtx, {
                type: 'bar',
                data: {
                    labels: ageLabels,
                    datasets: [{
                        label: 'Số bệnh nhân',
                        data: ageData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgb(75, 192, 192)',
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