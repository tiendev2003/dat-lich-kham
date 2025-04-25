<?php
// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = 'Chuyên khoa';
require_once 'includes/functions.php';
include_once 'includes/page_banner.php';

// Kết nối database
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';

// Lấy thông số từ cài đặt
$specialties_title = get_setting('specialties_title', 'Chuyên khoa');
$specialties_subtitle = get_setting('specialties_subtitle', 'Các chuyên khoa khám và điều trị tại phòng khám');
$specialties_banner = get_setting('specialties_banner_image', '');

// Lấy danh sách chuyên khoa
$sql = "SELECT * FROM chuyenkhoa ORDER BY ten_chuyenkhoa";
$result = $conn->query($sql);
$specialties = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $specialties[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/chuyenkhoa.css">
    <style>
        .specialty-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            height: 100%;
            padding: 25px;
        }
        
        .specialty-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .specialty-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(var(--primary-color-rgb), 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .specialty-icon img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .specialty-icon i {
            font-size: 32px;
            color: var(--primary-color);
        }
        
        .specialty-card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner -->
    <?php display_page_banner(
        htmlspecialchars($specialties_title), 
        htmlspecialchars($specialties_subtitle), 
        !empty($specialties_banner) ? $specialties_banner : ''
    ); ?>

    <!-- Specialties Section -->
    <section class="specialties py-5">
        <div class="container">
            <?php if (count($specialties) > 0): ?>
            <div class="row">
                <?php foreach ($specialties as $specialty): ?>
                <div class="col-md-4 mb-4">
                    <div class="specialty-card text-center">
                        <div class="specialty-icon mb-3">
                            <?php if (!empty($specialty['hinh_anh'])): ?>
                                <img src="<?= $specialty['hinh_anh'] ?>" alt="<?= $specialty['ten_chuyenkhoa'] ?>">
                            <?php else: ?>
                                <i class="fas <?= !empty($specialty['icon']) ? $specialty['icon'] : 'fa-stethoscope' ?>"></i>
                            <?php endif; ?>
                        </div>
                        <h3><?= $specialty['ten_chuyenkhoa'] ?></h3>
                        <p><?= !empty($specialty['mota']) ? substr(strip_tags($specialty['mota']), 0, 100) . '...' : 'Chăm sóc và điều trị các bệnh lý liên quan.' ?></p>
                        <a href="chuyenkhoa_chitiet.php?id=<?= $specialty['id'] ?>" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center">
                Hiện chưa có thông tin về chuyên khoa. Vui lòng quay lại sau.
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>