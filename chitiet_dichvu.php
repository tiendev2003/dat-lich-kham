<?php
// Thiết lập tiêu đề trang cho head.php
// Start the session before any output
session_start();

// Kết nối database và load functions
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';
require_once 'includes/functions.php';
include_once 'includes/page_banner.php';

// Lấy ID dịch vụ từ tham số URL
$service_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Nếu không có ID hợp lệ, chuyển hướng về trang dịch vụ
if ($service_id <= 0) {
    header('Location: dichvu.php');
    exit;
}

// Lấy thông tin chi tiết dịch vụ
$service_sql = "SELECT d.*, ck.ten_chuyenkhoa 
                FROM dichvu d 
                LEFT JOIN chuyenkhoa ck ON d.chuyenkhoa_id = ck.id 
                WHERE d.id = $service_id AND d.trangthai = 1";
$service_result = $conn->query($service_sql);

if (!$service_result || $service_result->num_rows == 0) {
    header('Location: dichvu.php');
    exit;
}

$service = $service_result->fetch_assoc();

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = $service['ten_dichvu'];

// Lấy thông số từ cài đặt
$site_name = get_setting('site_name', 'Phòng Khám Lộc Bình');
$site_working_hours = get_setting('site_working_hours', 'Thứ 2 - Thứ 6: 8:00 - 17:00');

// Lấy các gói dịch vụ liên quan (giả sử có bảng goi_dichvu liên kết với dichvu)
$packages = [];

// Kiểm tra xem có bảng goi_dichvu không
$table_check = $conn->query("SHOW TABLES LIKE 'goi_dichvu'");
if ($table_check->num_rows > 0) {
    // Nếu có bảng, lấy dữ liệu từ bảng
    $packages_sql = "SELECT * FROM goi_dichvu WHERE dichvu_id = $service_id ORDER BY gia ASC";
    $packages_result = $conn->query($packages_sql);
    
    if ($packages_result && $packages_result->num_rows > 0) {
        while ($row = $packages_result->fetch_assoc()) {
            $packages[] = $row;
        }
    }
} 

// Nếu không có gói dịch vụ trong DB, tạo các gói mẫu dựa trên giá cơ bản
if (empty($packages)) {
    $base_price = $service['gia_coban'];
    
    // Tạo gói cơ bản
    $packages[] = [
        'ten_goi' => 'Gói cơ bản',
        'gia' => $base_price,
        'mo_ta' => 'Dịch vụ cơ bản với đầy đủ các yêu cầu thiết yếu',
        'chi_tiet' => "Khám tổng quát\nTư vấn kết quả\nTheo dõi điều trị"
    ];
    
    // Tạo gói nâng cao
    $packages[] = [
        'ten_goi' => 'Gói nâng cao',
        'gia' => $base_price * 2,
        'mo_ta' => 'Dịch vụ nâng cao với nhiều quyền lợi',
        'chi_tiet' => "Tất cả dịch vụ của gói cơ bản\nXét nghiệm chuyên sâu\nTham vấn chuyên gia\nTái khám miễn phí",
        'featured' => true
    ];
    
    // Tạo gói cao cấp
    $packages[] = [
        'ten_goi' => 'Gói toàn diện',
        'gia' => $base_price * 4,
        'mo_ta' => 'Dịch vụ toàn diện với đầy đủ quyền lợi',
        'chi_tiet' => "Tất cả dịch vụ của gói nâng cao\nDịch vụ VIP\nChăm sóc đặc biệt\nTư vấn trọn đời\nTheo dõi điều trị liên tục"
    ];
}

// Lấy các dịch vụ liên quan cùng chuyên khoa
$related_services = [];
if (!empty($service['chuyenkhoa_id'])) {
    $related_sql = "SELECT * FROM dichvu 
                    WHERE chuyenkhoa_id = {$service['chuyenkhoa_id']} 
                    AND id != $service_id 
                    AND trangthai = 1 
                    ORDER BY RAND() LIMIT 3";
    $related_result = $conn->query($related_sql);
    
    if ($related_result && $related_result->num_rows > 0) {
        while ($row = $related_result->fetch_assoc()) {
            $related_services[] = $row;
        }
    }
}

// Helper function to format price
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

// Phân tích chuỗi giờ làm việc thành mảng
$working_hours = explode(',', $site_working_hours);
$working_days = '';

if(!empty($working_hours)) {
    foreach($working_hours as $hour) {
        if(strpos($hour, 'Thứ 2') !== false || strpos($hour, 'T2') !== false) {
            $working_days = 'Thứ 2 - Thứ 6';
            break;
        }
    }
    if(empty($working_days)) {
        $working_days = 'Thứ 2 - Thứ 7';
    }
}

// Create subtitle for banner
$banner_subtitle = !empty($service['mota_ngan']) 
    ? $service['mota_ngan'] 
    : 'Dịch vụ chăm sóc sức khỏe chuyên nghiệp tại ' . get_setting('site_name', 'Phòng Khám Lộc Bình');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/chitiet_dichvu.css">
    <style>
        /* Custom styles */
        .service-detail-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 40px;
        }
        .service-header h1 {
            color: var(--primary-color);
            font-weight: 600;
        }
        .service-image {
            max-height: 300px;
            object-fit: cover;
        }
        .highlight-box {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-color);
            padding: 20px;
            border-radius: 5px;
        }
        .highlight-box h3 {
            color: var(--primary-color);
            font-size: 20px;
            margin-bottom: 15px;
        }
        .highlight-box ul {
            padding-left: 20px;
        }
        .highlight-box li {
            margin-bottom: 10px;
        }
        .package-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .package-card:hover {
            transform: translateY(-5px);
        }
        .package-card.featured {
            border: 2px solid var(--primary-color);
            box-shadow: 0 5px 20px rgba(var(--primary-color-rgb), 0.2);
            position: relative;
        }
        .package-card.featured::after {
            content: "Phổ biến";
            position: absolute;
            top: 10px;
            right: -30px;
            background: var(--primary-color);
            color: white;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 30px;
            transform: rotate(45deg);
        }
        .package-header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .package-header h3 {
            margin-bottom: 10px;
            color: #343a40;
            font-size: 22px;
        }
        .package-header .price {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        .package-body {
            padding: 20px;
        }
        .package-body ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 20px;
        }
        .package-body ul li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            position: relative;
            padding-left: 25px;
        }
        .package-body ul li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: var(--primary-color);
            font-weight: 600;
        }
        .process-steps {
            margin-top: 40px;
        }
        .step-item {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px 20px;
            height: 100%;
            transition: transform 0.3s ease;
        }
        .step-item:hover {
            transform: translateY(-5px);
        }
        .step-number {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin: 0 auto 15px;
        }
        .step-icon {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .note-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            height: 100%;
        }
        .note-card h4 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .note-card ul {
            padding-left: 20px;
        }
        .related-service-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
            height: 100%;
        }
        .related-service-card:hover {
            transform: translateY(-5px);
        }
        .related-service-image {
            height: 180px;
            overflow: hidden;
        }
        .related-service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .related-service-content {
            padding: 20px;
        }
        .related-service-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
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

    <!-- Banner -->
    <?php display_page_banner(
        $service['ten_dichvu'],
        $banner_subtitle,
        !empty($service['hinh_anh']) ? $service['hinh_anh'] : ''
    ); ?>

    <!-- Breadcrumb -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="dichvu.php">Dịch vụ</a></li>
                <?php if (!empty($service['chuyenkhoa_id'])): ?>
                <li class="breadcrumb-item">
                    <a href="dichvu.php?specialty=<?= $service['chuyenkhoa_id'] ?>">
                        <?= $service['ten_chuyenkhoa'] ?>
                    </a>
                </li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= $service['ten_dichvu'] ?></li>
            </ol>
        </nav>
    </div>

    <!-- Service Detail Content -->
    <div class="container">
        <div class="service-detail-container">
            <!-- Service Header -->
            <div class="service-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <!-- Removed duplicate title since it's now in the banner -->
                        <?php if (!empty($service['ten_chuyenkhoa'])): ?>
                        <div class="service-category">
                            <span class="badge bg-primary"><i class="fas fa-tag me-1"></i> <?= $service['ten_chuyenkhoa'] ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <!-- Hide image here if using as banner background -->
                    <?php if (!empty($service['hinh_anh']) && false): // Disabled because we're using it in the banner ?>
                    <div class="col-md-4 text-center">
                        <img src="<?= $service['hinh_anh'] ?>" alt="<?= $service['ten_dichvu'] ?>" class="img-fluid rounded service-image">
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Service Description -->
            <div class="service-description mt-3">
                <div class="row">
                    <div class="col-md-8">
                        <h2>Giới thiệu dịch vụ</h2>
                        <?php if (!empty($service['chi_tiet'])): ?>
                            <?= $service['chi_tiet'] ?>
                        <?php else: ?>
                            <p><?= $service['ten_dichvu'] ?> là một trong những dịch vụ y tế quan trọng tại phòng khám của chúng tôi. Với đội ngũ bác sĩ giàu kinh nghiệm và trang thiết bị hiện đại, chúng tôi cam kết mang đến cho bạn dịch vụ chăm sóc sức khỏe tốt nhất.</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="highlight-box">
                            <h3>Điểm nổi bật</h3>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Đội ngũ bác sĩ chuyên môn cao</li>
                                <li><i class="fas fa-check-circle"></i> Trang thiết bị hiện đại</li>
                                <li><i class="fas fa-check-circle"></i> Kết quả nhanh chóng</li>
                                <li><i class="fas fa-check-circle"></i> Tư vấn chi tiết</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Packages -->
            <div class="service-packages mt-5">
                <h2>Các gói dịch vụ</h2>
                <div class="row mt-4">
                    <?php foreach($packages as $index => $package): ?>
                    <div class="col-md-4 mb-4">
                        <div class="package-card <?= isset($package['featured']) && $package['featured'] ? 'featured' : '' ?>">
                            <div class="package-header">
                                <h3><?= $package['ten_goi'] ?></h3>
                                <div class="price"><?= formatPrice($package['gia']) ?></div>
                            </div>
                            <div class="package-body">
                                <ul>
                                    <?php 
                                    $features = explode("\n", $package['chi_tiet']);
                                    foreach($features as $feature): 
                                        $feature = trim($feature);
                                        if(!empty($feature)):
                                    ?>
                                        <li><?= $feature ?></li>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </ul>
                                <a href="datlich.php?service_id=<?= $service_id ?>&package=<?= $index + 1 ?>" class="btn btn-primary w-100">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Process Section -->
            <div class="service-process mt-5">
                <h2>Quy trình thực hiện</h2>
                <div class="process-steps mt-4">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                                <h4>Đăng ký</h4>
                                <p>Đăng ký thông tin và chọn gói dịch vụ phù hợp</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-icon"><i class="fas fa-stethoscope"></i></div>
                                <h4>Khám lâm sàng</h4>
                                <p>Bác sĩ thăm khám và đánh giá tình trạng</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-icon"><i class="fas fa-vial"></i></div>
                                <h4>Xét nghiệm</h4>
                                <p>Thực hiện các xét nghiệm cần thiết</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">4</div>
                                <div class="step-icon"><i class="fas fa-file-medical"></i></div>
                                <h4>Kết quả</h4>
                                <p>Nhận kết quả và tư vấn từ bác sĩ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="service-notes mt-5">
                <h2>Lưu ý khi đến khám</h2>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="note-card">
                            <h4><i class="fas fa-clipboard-list"></i> Chuẩn bị</h4>
                            <ul>
                                <?php if ($service['chuyenkhoa_id'] == 1): // Tim mạch ?>
                                <li>Mang theo các kết quả xét nghiệm trước đây (nếu có)</li>
                                <li>Nhịn ăn 6-8 tiếng trước khi xét nghiệm máu</li>
                                <li>Mang theo các loại thuốc đang sử dụng</li>
                                <li>Mang theo CMND/CCCD và thẻ BHYT</li>
                                <?php elseif ($service['chuyenkhoa_id'] == 5): // Mắt ?>
                                <li>Mang theo kính mắt hiện tại nếu có</li>
                                <li>Không trang điểm mắt khi đến khám</li>
                                <li>Mang theo kết quả khám mắt trước đây</li>
                                <li>Mang theo CMND/CCCD và thẻ BHYT</li>
                                <?php else: ?>
                                <li>Mang theo các kết quả khám trước đây (nếu có)</li>
                                <li>Đối với xét nghiệm: nhịn ăn 6-8 tiếng</li>
                                <li>Mang theo các loại thuốc đang sử dụng</li>
                                <li>Mang theo CMND/CCCD và thẻ BHYT</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="note-card">
                            <h4><i class="fas fa-clock"></i> Thời gian</h4>
                            <ul>
                                <li>Thời gian khám: 1-2 giờ</li>
                                <li>Thời gian có kết quả: 1-3 ngày</li>
                                <?php foreach($working_hours as $index => $hour): ?>
                                    <?php if($index < 2): ?>
                                    <li><?php echo htmlspecialchars(trim($hour)); ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <li>Khám từ <?php echo $working_days; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Services -->
            <?php if (count($related_services) > 0): ?>
            <div class="related-services mt-5">
                <h2>Dịch vụ liên quan</h2>
                <div class="row mt-4">
                    <?php foreach ($related_services as $related): ?>
                    <div class="col-md-4 mb-4">
                        <div class="related-service-card">
                            <div class="related-service-image">
                                <?php if (!empty($related['hinh_anh'])): ?>
                                <img src="<?= $related['hinh_anh'] ?>" alt="<?= $related['ten_dichvu'] ?>">
                                <?php else: ?>
                                <img src="assets/img/service-default.jpg" alt="<?= $related['ten_dichvu'] ?>">
                                <?php endif; ?>
                            </div>
                            <div class="related-service-content">
                                <h3 class="related-service-title"><?= $related['ten_dichvu'] ?></h3>
                                <p><?= !empty($related['mota_ngan']) ? substr($related['mota_ngan'], 0, 80).'...' : 'Dịch vụ chăm sóc sức khỏe chuyên nghiệp.' ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="price"><?= formatPrice($related['gia_coban']) ?></span>
                                    <a href="chitiet_dichvu.php?id=<?= $related['id'] ?>" class="btn btn-outline-primary">Chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Call to Action -->
            <div class="cta-section text-center mt-5 py-4" style="background-color: #f8f9fa; border-radius: 10px;">
                <h2>Đặt lịch khám ngay hôm nay</h2>
                <p class="lead mb-4">Hãy liên hệ với chúng tôi để được tư vấn và đặt lịch khám sớm nhất</p>
                <a href="datlich.php?service_id=<?= $service_id ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar-check me-2"></i> Đặt lịch ngay
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>