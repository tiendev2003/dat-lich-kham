<?php
// Thiết lập tiêu đề trang cho head.php
// Start the session before any output
session_start();

// Kết nối database và load functions
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';
require_once 'admin/crud/tintuc_crud.php';
require_once 'includes/functions.php';

// Lấy ID bác sĩ từ tham số URL
$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Nếu không có ID hợp lệ, chuyển hướng về trang danh sách bác sĩ
if ($doctor_id <= 0) {
    header('Location: bacsi.php');
    exit;
}

// Lấy thông tin chi tiết bác sĩ
$doctor_sql = "SELECT b.*, ck.ten_chuyenkhoa 
               FROM bacsi b 
               LEFT JOIN chuyenkhoa ck ON b.chuyenkhoa_id = ck.id 
               WHERE b.id = $doctor_id";
$doctor_result = $conn->query($doctor_sql);

if (!$doctor_result || $doctor_result->num_rows == 0) {
    header('Location: bacsi.php');
    exit;
}

$doctor = $doctor_result->fetch_assoc();

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = $doctor['ho_ten'];

// Lấy thông số từ cài đặt
$site_name = get_setting('site_name', 'Phòng Khám Lộc Bình');

// Lấy các bài viết của bác sĩ (nếu có liên kết với người dùng)
$articles = [];
if (!empty($doctor['nguoidung_id'])) {
    $articles_sql = "SELECT * FROM tintuc WHERE nguoi_tao = {$doctor['nguoidung_id']} AND trang_thai = 'published' ORDER BY ngay_dang DESC LIMIT 4";
    $articles_result = $conn->query($articles_sql);
    
    if ($articles_result && $articles_result->num_rows > 0) {
        while ($row = $articles_result->fetch_assoc()) {
            $articles[] = $row;
        }
    }
}

// Lấy lịch làm việc của bác sĩ trong tuần này
$schedule = [];
$current_date = date('Y-m-d');
for ($i = 0; $i < 5; $i++) {
    $date = date('Y-m-d', strtotime("$current_date +$i day"));
    $date_formatted = date('d/m/Y', strtotime($date));
    $day_name = date('l', strtotime($date));
    
    // Chuyển tên thứ sang tiếng Việt
    $day_names = [
        'Monday' => 'Thứ hai',
        'Tuesday' => 'Thứ ba',
        'Wednesday' => 'Thứ tư',
        'Thursday' => 'Thứ năm',
        'Friday' => 'Thứ sáu',
        'Saturday' => 'Thứ bảy',
        'Sunday' => 'Chủ nhật'
    ];
    
    $day_vn = $day_names[$day_name];
    
    // Lấy các khung giờ đã đặt trong ngày
    $booked_slots_sql = "SELECT gio_hen FROM lichhen WHERE bacsi_id = $doctor_id AND ngay_hen = '$date' AND trang_thai != 'cancelled'";
    $booked_slots_result = $conn->query($booked_slots_sql);
    $booked_slots = [];
    
    if ($booked_slots_result && $booked_slots_result->num_rows > 0) {
        while ($row = $booked_slots_result->fetch_assoc()) {
            $booked_slots[] = $row['gio_hen'];
        }
    }
    
    // Tạo danh sách các khung giờ (giả sử bác sĩ làm việc từ 8h đến 17h)
    $all_slots = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00', '16:00:00'];
    $day_slots = [];
    
    foreach ($all_slots as $slot) {
        $availability = in_array($slot, $booked_slots) ? 'booked' : 'available';
        $day_slots[] = [
            'time' => substr($slot, 0, 5), // Format HH:MM
            'availability' => $availability
        ];
    }
    
    $schedule[] = [
        'date' => $date,
        'date_formatted' => $date_formatted,
        'day_name' => $day_vn,
        'slots' => $day_slots
    ];
}

// Format date helper
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d/m/Y', strtotime($date));
}

// Tính tuổi từ năm sinh
function calculateAge($birth_year) {
    return date('Y') - $birth_year;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/doctors.css">
    <style>
        .doctor-profile {
            padding: 40px 0;
        }
        .doctor-image-container {
            position: relative;
            margin-bottom: 20px;
        }
        .doctor-image {
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            width: 100%;
            object-fit: cover;
        }
        .doctor-badge {
            position: absolute;
            bottom: -15px;
            right: 20px;
            background-color: var(--primary-color);
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .doctor-info {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
        }
        .doctor-description {
            margin-top: 25px;
            line-height: 1.8;
            color: #555;
        }
        .booking-button {
            margin-top: 30px;
        }
        .qualification-item {
            margin-bottom: 25px;
            padding-left: 20px;
            position: relative;
        }
        .qualification-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            height: calc(100% - 8px);
            width: 3px;
            background-color: var(--primary-color);
            border-radius: 5px;
        }
        .contact-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .contact-info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(var(--primary-color-rgb), 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            margin-right: 15px;
        }
        .contact-info-text {
            font-size: 16px;
        }
        .doctor-stats {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 25px 0;
        }
        .doctor-stat-item {
            text-align: center;
        }
        .doctor-stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .doctor-stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        .tab-content {
            padding: 25px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-top: 0;
            border-radius: 0 0 10px 10px;
        }
        .schedule-day {
            margin-bottom: 20px;
        }
        .day-title {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .time-slot {
            display: inline-block;
            padding: 5px 15px;
            margin: 5px;
            border-radius: 20px;
            background-color: #e9ecef;
            cursor: pointer;
            transition: all 0.3s;
        }
        .time-slot.available {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .time-slot.available:hover {
            background-color: #0f5132;
            color: white;
        }
        .time-slot.booked {
            background-color: #f8d7da;
            color: #721c24;
            cursor: not-allowed;
        }
        .article-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .article-card:hover {
            transform: translateY(-5px);
        }
        .article-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .article-content {
            padding: 20px;
        }
        .article-title {
            margin-bottom: 10px;
            font-weight: 600;
        }
        .article-date {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        :root {
            --primary-color-rgb: <?php 
                $hex = ltrim(get_setting('primary_color', '#005bac'), '#');
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                echo "$r,$g,$b";
            ?>;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <div class="container doctor-profile">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="bacsi.php">Bác sĩ</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $doctor['ho_ten'] ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Cột trái: Ảnh và thông tin liên hệ -->
            <div class="col-lg-4">
                <div class="doctor-image-container">
                    <?php if (!empty($doctor['hinh_anh'])): ?>
                        <img src="<?= $doctor['hinh_anh'] ?>" alt="<?= $doctor['ho_ten'] ?>" class="img-fluid doctor-image">
                    <?php else: ?>
                        <img src="assets/img/doctor-default.jpg" alt="<?= $doctor['ho_ten'] ?>" class="img-fluid doctor-image">
                    <?php endif; ?>
                    <div class="doctor-badge">Chuyên khoa <?= $doctor['ten_chuyenkhoa'] ?></div>
                </div>
                
                <div class="doctor-info">
                    <h4 class="text-primary mb-4">Thông tin liên hệ</h4>
                    
                    <?php if(!empty($doctor['email'])): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-text"><?= $doctor['email'] ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($doctor['dien_thoai'])): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-info-text"><?= $doctor['dien_thoai'] ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <div class="contact-info-text"><?php echo htmlspecialchars($site_name); ?></div>
                    </div>

                    <?php if(!empty($doctor['dia_chi'])): ?>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-text"><?= $doctor['dia_chi'] ?></div>
                    </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div class="doctor-stats">
                        <div class="doctor-stat-item">
                            <div class="doctor-stat-number">
                                <?= !empty($doctor['nam_sinh']) ? (date('Y') - $doctor['nam_sinh']) : '?' ?>+
                            </div>
                            <div class="doctor-stat-label">Năm kinh nghiệm</div>
                        </div>
                        
                        <div class="doctor-stat-item">
                            <div class="doctor-stat-number">
                                <?php
                                // Đếm số lịch hẹn đã hoàn thành
                                $appointments_sql = "SELECT COUNT(*) as count FROM lichhen WHERE bacsi_id = $doctor_id AND trang_thai = 'completed'";
                                $appointments_result = $conn->query($appointments_sql);
                                $appointments_count = 0;
                                if ($appointments_result && $appointments_result->num_rows > 0) {
                                    $appointments_count = $appointments_result->fetch_assoc()['count'];
                                }
                                echo $appointments_count > 0 ? $appointments_count.'+' : '0+';
                                ?>
                            </div>
                            <div class="doctor-stat-label">Bệnh nhân</div>
                        </div>
                        
                        <div class="doctor-stat-item">
                            <div class="doctor-stat-number">
                                <?php
                                // Có thể thêm bảng đánh giá bác sĩ trong tương lai
                                echo '5.0';
                                ?>
                            </div>
                            <div class="doctor-stat-label">Đánh giá</div>
                        </div>
                    </div>
                    
                    <div class="booking-button">
                        <a href="datlich.php?doctor_id=<?= $doctor_id ?>" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Đặt lịch khám
                        </a>
                    </div>
                </div>
                
                <!-- Chuyên khoa -->
                <div class="doctor-info mt-4">
                    <h4 class="text-primary mb-3">Chuyên khoa</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark p-2">
                            <i class="fas fa-stethoscope me-1"></i> 
                            <?= $doctor['ten_chuyenkhoa'] ?? 'Đa khoa' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Thông tin chi tiết -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="mb-2"><?= $doctor['ho_ten'] ?></h2>
                        <p class="text-primary mb-4">
                            <i class="fas fa-stethoscope me-2"></i>
                            Chuyên khoa <?= $doctor['ten_chuyenkhoa'] ?? 'Đa khoa' ?>
                            <?php if(!empty($doctor['bang_cap'])): ?>
                            - <?= $doctor['bang_cap'] ?>
                            <?php endif; ?>
                        </p>

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="doctorTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                                    <i class="fas fa-user-md me-1"></i> Thông tin
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="false">
                                    <i class="fas fa-calendar-alt me-1"></i> Lịch khám
                                </button>
                            </li>
                            <?php if(count($articles) > 0): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles" type="button" role="tab" aria-controls="articles" aria-selected="false">
                                    <i class="fas fa-file-medical-alt me-1"></i> Bài viết
                                </button>
                            </li>
                            <?php endif; ?>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content" id="doctorTabContent">
                            <!-- Tab Thông tin -->
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                <?php if(!empty($doctor['mo_ta'])): ?>
                                <div class="doctor-description mb-4">
                                    <h5 class="mb-3">Giới thiệu</h5>
                                    <?= $doctor['mo_ta'] ?>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($doctor['bang_cap'])): ?>
                                <div class="qualification-item">
                                    <h5 class="mb-3">Chức danh</h5>
                                    <p><?= $doctor['bang_cap'] ?></p>
                                </div>
                                <?php endif; ?>

                                <?php if(!empty($doctor['kinh_nghiem'])): ?>
                                <div class="qualification-item">
                                    <h5 class="mb-3">Kinh nghiệm làm việc</h5>
                                    <?= $doctor['kinh_nghiem'] ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Tab Lịch khám -->
                            <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                                <h5 class="mb-4">Lịch làm việc trong tuần</h5>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Nhấp vào thời gian còn trống để đặt lịch khám với bác sĩ
                                </div>
                                
                                <!-- Lịch theo ngày -->
                                <?php foreach($schedule as $day): ?>
                                <div class="schedule-day">
                                    <div class="day-title">
                                        <i class="far fa-calendar-alt me-2"></i> 
                                        <?= $day['day_name'] ?>, <?= $day['date_formatted'] ?>
                                    </div>
                                    <div class="time-slots">
                                        <?php foreach($day['slots'] as $slot): ?>
                                        <span class="time-slot <?= $slot['availability'] ?>" 
                                              <?php if($slot['availability'] == 'available'): ?>
                                              data-date="<?= $day['date'] ?>" 
                                              data-time="<?= $slot['time'] ?>"
                                              <?php endif; ?>>
                                            <?= $slot['time'] ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                
                                <div class="alert alert-light mt-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="time-slot available me-2">00:00</span>
                                        <span>Còn trống</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="time-slot booked me-2">00:00</span>
                                        <span>Đã đặt</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Bài viết -->
                            <?php if(count($articles) > 0): ?>
                            <div class="tab-pane fade" id="articles" role="tabpanel" aria-labelledby="articles-tab">
                                <h5 class="mb-4">Bài viết của bác sĩ</h5>
                                
                                <div class="row">
                                    <?php foreach($articles as $article): ?>
                                    <div class="col-md-6 mb-4">
                                        <div class="article-card">
                                            <?php if(!empty($article['hinh_anh'])): ?>
                                            <img src="<?= $article['hinh_anh'] ?>" alt="<?= $article['tieu_de'] ?>" class="article-image">
                                            <?php else: ?>
                                            <img src="assets/img/blog-1.png" alt="<?= $article['tieu_de'] ?>" class="article-image">
                                            <?php endif; ?>
                                            <div class="article-content">
                                                <h5 class="article-title"><?= $article['tieu_de'] ?></h5>
                                                <div class="article-date"><i class="far fa-calendar-alt me-1"></i> <?= formatDate($article['ngay_dang']) ?></div>
                                                <p>
                                                    <?= !empty($article['meta_description']) 
                                                        ? $article['meta_description'] 
                                                        : substr(strip_tags($article['noi_dung']), 0, 100).'...' ?>
                                                </p>
                                                <a href="chitiet_tintuc.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary">Đọc tiếp</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
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
            // Xử lý các slot thời gian
            const availableSlots = document.querySelectorAll('.time-slot.available');
            availableSlots.forEach(slot => {
                slot.addEventListener('click', function() {
                    const time = this.textContent.trim();
                    const date = this.dataset.date;
                    
                    // Redirect to booking page with doctor and time info
                    window.location.href = `datlich.php?doctor_id=<?= $doctor_id ?>&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`;
                });
            });

            // Xử lý tab navigation từ URL
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            
            if (tab) {
                const triggerEl = document.querySelector(`#${tab}-tab`);
                if (triggerEl) {
                    const tabTrigger = new bootstrap.Tab(triggerEl);
                    tabTrigger.show();
                }
            }
        });
    </script>
</body>
</html>