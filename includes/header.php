<?php
// Only start session if one hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = '/dat-lich-kham/'; // Thay đổi theo đường dẫn thực tế của dự án của bạn
require_once 'functions.php';

// Lấy các thông số cài đặt
$site_name = get_setting('site_name', 'Phòng Khám Lộc Bình');
$primary_color = get_setting('primary_color', '#005bac');
$header_bg_color = get_setting('header_bg_color', '#ffffff');

// Tạo CSS custom từ cài đặt
$custom_css = "
<style>
    :root {
        --primary-color: {$primary_color};
        --secondary-color: " . get_setting('secondary_color', '#6c757d') . ";
        --accent-color: " . get_setting('accent_color', '#28a745') . ";
    }
    .navbar-brand {
        color: var(--primary-color) !important;
    }
    .btn-primary, .bg-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }
    .btn-outline-primary {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }
    .btn-outline-primary:hover {
        background-color: var(--primary-color) !important;
        color: white !important;
    }
    .text-primary {
        color: var(--primary-color) !important;
    }
    /* Các biến CSS và tùy chỉnh khác từ cài đặt */
</style>
";

?>
<?php echo $custom_css; ?>
<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm" style="background-color: <?php echo $header_bg_color; ?>">
        <div class="container">
            <a class="navbar-brand" href="index.php" style="font-size: 20px; font-weight: bold;">
                <?php 
                    echo $site_name;
                    
                ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dichvuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Dịch vụ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dichvuDropdown">
                            <li><a class="dropdown-item" href="dichvu.php">Tất cả dịch vụ</a></li>
                            <li><a class="dropdown-item" href="datlich.php">Đặt lịch khám</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="bacsiDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Bác sĩ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="bacsiDropdown">
                            <li><a class="dropdown-item" href="bacsi.php">Danh sách bác sĩ</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="chuyenkhoaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Chuyên khoa
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="chuyenkhoaDropdown">
                            <li><a class="dropdown-item" href="chuyenkhoa.php">Tất cả chuyên khoa</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tintuc.php">Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tracuu.php">Tra cứu</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="thongTinDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Thông tin
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="thongTinDropdown">
                            <li><a class="dropdown-item" href="about.php">Giới thiệu</a></li>
                            <li><a class="dropdown-item" href="contact.php">Liên hệ</a></li>
                            <li><a class="dropdown-item" href="faq.php">FAQ</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="nav-buttons">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> Tài khoản
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="user_profile.php">Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="lichsu_datlich.php">Lịch sử đặt khám</a></li>
                                <li><a class="dropdown-item" href="medical_records.php">Hồ sơ y tế</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="dangnhap.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                     <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>