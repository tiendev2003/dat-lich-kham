<?php
session_start();
$base_url = '/dat-lich-kham/'; // Thay đổi theo đường dẫn thực tế của dự án của bạn

?>
<header class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php" style="font-size: 20px; font-weight: bold; color: #005bac;">
                Phòng Khám Lộc Bình
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dichvu.php">Dịch Vụ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bacsi.php">Bác sĩ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="chuyenkhoa.php">Chuyên khoa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tintuc.php">Tin tức</a>
                    </li>
                </ul>
                <div class="nav-buttons">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> Tài khoản
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="appointments.php">Lịch sử đặt khám</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Thành -->
                        <a href="dangnhap.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                        <a href="dangky.php" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header> 