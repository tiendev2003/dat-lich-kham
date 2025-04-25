<?php
// Thiết lập tiêu đề trang cho head.php
// Start the session before any output
session_start();

// Kết nối database và load functions
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';
require_once 'includes/functions.php';
include_once 'includes/page_banner.php';

// Lấy ID chuyên khoa từ tham số URL
$specialty_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Nếu không có ID hợp lệ, chuyển hướng về trang chuyên khoa
if ($specialty_id <= 0) {
    header('Location: chuyenkhoa.php');
    exit;
}

// Lấy thông tin chi tiết chuyên khoa
$specialty_sql = "SELECT * FROM chuyenkhoa WHERE id = $specialty_id";
$specialty_result = $conn->query($specialty_sql);

if (!$specialty_result || $specialty_result->num_rows == 0) {
    header('Location: chuyenkhoa.php');
    exit;
}

$specialty = $specialty_result->fetch_assoc();

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = $specialty['ten_chuyenkhoa'];

// Lấy thông số từ cài đặt
$site_name = get_setting('site_name', 'Phòng Khám Lộc Bình');

// Lấy danh sách bác sĩ thuộc chuyên khoa
$doctors_sql = "SELECT * FROM bacsi WHERE chuyenkhoa_id = $specialty_id";
$doctors_result = $conn->query($doctors_sql);
$doctors = [];

if ($doctors_result && $doctors_result->num_rows > 0) {
    while ($row = $doctors_result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

// Lấy danh sách dịch vụ thuộc chuyên khoa
$services_sql = "SELECT * FROM dichvu WHERE chuyenkhoa_id = $specialty_id AND trangthai = 1";
$services_result = $conn->query($services_sql);
$services = [];

if ($services_result && $services_result->num_rows > 0) {
    while ($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Format giá tiền
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

// Hàm định dạng văn bản
function formatDescription($text) {
    if (empty($text)) return '';
    return nl2br(htmlspecialchars($text));
}

// Prepare subtitle for banner
$banner_subtitle = "Chăm sóc sức khỏe toàn diện với đội ngũ bác sĩ giàu kinh nghiệm";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/chuyenkhoa_chitiet.css">
    <style>
        .specialty-content {
            padding: 40px 0;
        }
        .specialty-description {
            margin-bottom: 40px;
        }
        .specialty-feature {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .specialty-feature:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        .doctor-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
        }
        .doctor-image {
            height: 250px;
            overflow: hidden;
        }
        .doctor-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .doctor-info {
            padding: 20px;
        }
        .doctor-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .doctor-specialty {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 12px;
        }
        .doctor-credentials {
            margin-bottom: 15px;
            font-size: 14px;
        }
        .doctor-rating {
            color: #ffc107;
            margin-bottom: 15px;
        }
        .service-card {
            padding: 30px;
            border-radius: 10px;
            background-color: #f8f9fa;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-5px);
            background-color: #e9f0ff;
        }
        .service-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: rgba(var(--primary-color-rgb), 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .service-icon i {
            font-size: 30px;
            color: var(--primary-color);
        }
        .service-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .service-price {
            font-weight: 600;
            margin-top: 15px;
            font-size: 16px;
        }
        .faq-section {
            padding: 40px 0;
            background-color: #f8f9fa;
        }
        .accordion-button:not(.collapsed) {
            background-color: rgba(var(--primary-color-rgb), 0.1);
            color: var(--primary-color);
        }
        .accordion-item {
            margin-bottom: 10px;
            border-radius: 8px;
            overflow: hidden;
        }
        .book-consultation {
            background-color: var(--primary-color);
            color: white;
            padding: 40px 0;
            text-align: center;
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
        "Chuyên khoa " . $specialty['ten_chuyenkhoa'], 
        $banner_subtitle,
        !empty($specialty['hinh_anh']) ? $specialty['hinh_anh'] : ''
    ); ?>

    <!-- Specialty Content -->
    <section class="specialty-content">
        <div class="container">
            <div class="specialty-description">
                <h2 class="mb-4">Giới thiệu Chuyên khoa <?= $specialty['ten_chuyenkhoa'] ?></h2>
                <div class="row">
                    <div class="col-lg-8">
                        <?php if (!empty($specialty['mota'])): ?>
                            <?= $specialty['mota'] ?>
                        <?php else: ?>
                            <p>Chuyên khoa <?= $specialty['ten_chuyenkhoa'] ?> tại <?= htmlspecialchars($site_name) ?> là đơn vị khám và điều trị hàng đầu, được trang bị hệ thống máy móc và thiết bị hiện đại. Với đội ngũ bác sĩ chuyên khoa có trình độ chuyên môn cao, giàu kinh nghiệm, chúng tôi cam kết mang đến cho bệnh nhân dịch vụ chăm sóc sức khỏe chất lượng cao.</p>
                            <p>Mục tiêu của chúng tôi là giúp bệnh nhân phòng ngừa và điều trị hiệu quả các bệnh lý liên quan đến <?= strtolower($specialty['ten_chuyenkhoa']) ?>.</p>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-4">
                        <?php if (!empty($specialty['hinh_anh'])): ?>
                            <img src="<?= $specialty['hinh_anh'] ?>" alt="<?= $specialty['ten_chuyenkhoa'] ?>" class="img-fluid rounded">
                        <?php else: ?>
                            <img src="assets/img/specialty-default.jpg" alt="<?= $specialty['ten_chuyenkhoa'] ?>" class="img-fluid rounded">
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Specialty Features -->
            <h2 class="mb-4">Tại sao chọn Chuyên khoa <?= $specialty['ten_chuyenkhoa'] ?> của chúng tôi?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="specialty-feature">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4>Đội ngũ bác sĩ giàu kinh nghiệm</h4>
                        <p>Các bác sĩ của chúng tôi đều được đào tạo chuyên sâu và có nhiều năm kinh nghiệm trong lĩnh vực <?= strtolower($specialty['ten_chuyenkhoa']) ?>, đảm bảo chất lượng điều trị tốt nhất.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-feature">
                        <div class="feature-icon">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h4>Trang thiết bị hiện đại</h4>
                        <p>Chúng tôi đầu tư các trang thiết bị hiện đại, giúp chẩn đoán chính xác và điều trị hiệu quả, giảm thiểu đau đớn cho bệnh nhân.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-feature">
                        <div class="feature-icon">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <h4>Phương pháp điều trị tiên tiến</h4>
                        <p>Áp dụng các phương pháp điều trị mới nhất, kết hợp với các phác đồ điều trị chuẩn quốc tế, đảm bảo kết quả điều trị tốt nhất.</p>
                    </div>
                </div>
            </div>

            <!-- Doctors -->
            <?php if (count($doctors) > 0): ?>
            <h2 class="mt-5 mb-4">Đội ngũ bác sĩ</h2>
            <div class="row">
                <?php foreach ($doctors as $doctor): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <?php if (!empty($doctor['hinh_anh'])): ?>
                                <img src="<?= $doctor['hinh_anh'] ?>" alt="<?= $doctor['ho_ten'] ?>">
                            <?php else: ?>
                                <img src="assets/img/doctor-default.jpg" alt="<?= $doctor['ho_ten'] ?>">
                            <?php endif; ?>
                        </div>
                        <div class="doctor-info">
                            <h3 class="doctor-name"><?= $doctor['ho_ten'] ?></h3>
                            <div class="doctor-specialty">Chuyên khoa <?= $specialty['ten_chuyenkhoa'] ?></div>
                            <?php if (!empty($doctor['bang_cap'])): ?>
                            <div class="doctor-credentials">
                                <i class="fas fa-graduation-cap me-2"></i> <?= $doctor['bang_cap'] ?>
                            </div>
                            <?php endif; ?>
                            <a href="chitiet_bacsi.php?id=<?= $doctor['id'] ?>" class="btn btn-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Services -->
            <?php if (count($services) > 0): ?>
            <h2 class="mt-5 mb-4">Dịch vụ cung cấp</h2>
            <div class="row">
                <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas <?= !empty($specialty['icon']) ? $specialty['icon'] : 'fa-stethoscope' ?>"></i>
                        </div>
                        <h3 class="service-title"><?= $service['ten_dichvu'] ?></h3>
                        <p><?= !empty($service['mota_ngan']) ? $service['mota_ngan'] : 'Dịch vụ chăm sóc sức khỏe chuyên nghiệp.' ?></p>
                        <div class="service-price">
                            Giá: <?= formatPrice($service['gia_coban']) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- FAQ Section -->
            <section class="faq-section mt-5">
                <div class="container">
                    <h2 class="mb-4 text-center">Câu hỏi thường gặp</h2>
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="accordion" id="faqAccordion">
                                <!-- FAQ Item 1 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Khi nào tôi nên khám chuyên khoa <?= $specialty['ten_chuyenkhoa'] ?>?
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Bạn nên đến khám chuyên khoa <?= $specialty['ten_chuyenkhoa'] ?> khi có các triệu chứng bất thường liên quan đến lĩnh vực này, hoặc trong các đợt khám sức khỏe định kỳ để phòng ngừa bệnh. Việc khám sớm giúp phát hiện và điều trị kịp thời các vấn đề sức khỏe.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- FAQ Item 2 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingTwo">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            Cần chuẩn bị những gì khi đến khám?
                                        </button>
                                    </h2>
                                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Khi đến khám, bạn nên mang theo các giấy tờ cá nhân (CMND/CCCD, thẻ BHYT nếu có), các kết quả khám và xét nghiệm trước đây nếu liên quan, danh sách thuốc đang sử dụng. Bạn cũng nên ghi chú các triệu chứng, thời gian xuất hiện để cung cấp thông tin chính xác cho bác sĩ.
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- FAQ Item 3 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingThree">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Thời gian khám và điều trị mất bao lâu?
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            Thời gian khám và điều trị phụ thuộc vào tình trạng bệnh của từng người. Buổi khám đầu tiên thường kéo dài từ 30-45 phút. Thời gian điều trị sẽ được bác sĩ tư vấn cụ thể sau khi có kết quả chẩn đoán.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <!-- Book Consultation -->
    <section class="book-consultation">
        <div class="container">
            <h2 class="mb-4">Đặt lịch khám ngay hôm nay</h2>
            <p class="lead mb-4">Hãy đặt lịch để được các bác sĩ chuyên khoa <?= $specialty['ten_chuyenkhoa'] ?> tư vấn và điều trị.</p>
            <a href="datlich.php?specialty=<?= $specialty_id ?>" class="btn btn-light btn-lg">Đặt lịch ngay</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>