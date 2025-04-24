<?php
// Kết nối đến database
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = 'Trang chủ';

// Lấy danh sách chuyên khoa
$specialties_query = "SELECT * FROM chuyenkhoa ORDER BY id LIMIT 6";
$specialties_result = $conn->query($specialties_query);
$specialties = [];
if ($specialties_result && $specialties_result->num_rows > 0) {
    while ($row = $specialties_result->fetch_assoc()) {
        $specialties[] = $row;
    }
}

// Lấy danh sách tất cả chuyên khoa cho dropdown tìm kiếm
$all_specialties_query = "SELECT * FROM chuyenkhoa ORDER BY ten_chuyenkhoa ASC";
$all_specialties_result = $conn->query($all_specialties_query);
$all_specialties = [];
if ($all_specialties_result && $all_specialties_result->num_rows > 0) {
    while ($row = $all_specialties_result->fetch_assoc()) {
        $all_specialties[] = $row;
    }
}

// Lấy tin tức mới nhất
$news_query = "SELECT * FROM tintuc WHERE trang_thai = 'published' ORDER BY ngay_dang DESC LIMIT 3";
$news_result = $conn->query($news_query);
$news_items = [];
if ($news_result && $news_result->num_rows > 0) {
    while ($row = $news_result->fetch_assoc()) {
        $news_items[] = $row;
    }
}

// Lấy danh sách bác sĩ nổi bật
$doctors_query = "SELECT b.*, c.ten_chuyenkhoa 
                 FROM bacsi b 
                 JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id 
                 ORDER BY b.id LIMIT 4";
$doctors_result = $conn->query($doctors_query);
$doctors = [];
if ($doctors_result && $doctors_result->num_rows > 0) {
    while ($row = $doctors_result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

// Lấy thông số từ cài đặt
$banner_title = get_setting('banner_title', 'Đặt lịch khám trực tuyến');
$banner_subtitle = get_setting('banner_subtitle', 'Dễ dàng - Nhanh chóng - Tiện lợi');
$banner_img = get_setting('banner_image', 'assets/img/banner.jpg');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner Section -->
    <section class="banner" style="background-image: url('<?php echo $banner_img; ?>');">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1><?php echo htmlspecialchars($banner_title); ?></h1>
                    <p><?php echo htmlspecialchars($banner_subtitle); ?></p>
                    <a href="datlich.php" class="btn btn-primary">Đặt lịch ngay</a>
                </div>
                <div class="col-md-6">
                    <!-- Quick Search Form -->
                    <div class="search-form">
                        <h3>Tìm kiếm nhanh</h3>
                        <form action="search.php" method="GET">
                            <div class="form-group mb-3">
                                <input type="text" name="doctor_name" class="form-control" placeholder="Tìm bác sĩ...">
                            </div>
                            <div class="form-group mb-3">
                                <select name="specialty_id" class="form-control">
                                    <option value="">Chọn chuyên khoa</option>
                                    <?php foreach ($all_specialties as $specialty): ?>
                                        <option value="<?php echo $specialty['id']; ?>"><?php echo htmlspecialchars($specialty['ten_chuyenkhoa']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Specialties -->
    <section class="specialties">
        <div class="container">
            <h2 class="section-title">Chuyên khoa nổi bật</h2>
            <div class="row">
                <?php if (empty($specialties)): ?>
                    <div class="col-12 text-center">
                        <p>Chưa có chuyên khoa nào được thêm vào hệ thống.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($specialties as $specialty): ?>
                    <div class="col-md-4 mb-4">
                        <div class="specialty-card">
                            <div class="specialty-icon">
                                <?php if (!empty($specialty['icon'])): ?>
                                    <i class="fas <?php echo $specialty['icon']; ?>"></i>
                                <?php else: ?>
                                    <i class="fas fa-stethoscope"></i>
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($specialty['ten_chuyenkhoa']); ?></h3>
                            <p><?php echo !empty($specialty['mota']) ? htmlspecialchars(substr($specialty['mota'], 0, 100)) . '...' : 'Chuyên khoa y tế chất lượng cao'; ?></p>
                            <a href="chuyenkhoa_chitiet.php?id=<?php echo $specialty['id']; ?>" class="btn btn-outline-primary">Xem chi tiết</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="chuyenkhoa.php" class="btn btn-primary">Xem tất cả chuyên khoa</a>
            </div>
        </div>
    </section>

    <!-- Featured Doctors -->
    <section class="featured-doctors">
        <div class="container">
            <h2 class="section-title">Bác sĩ nổi bật</h2>
            <div class="row">
                <?php if (empty($doctors)): ?>
                    <div class="col-12 text-center">
                        <p>Chưa có bác sĩ nào được thêm vào hệ thống.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($doctors as $doctor): ?>
                    <div class="col-md-3 mb-4">
                        <div class="doctor-card">
                            <div class="doctor-image">
                                <?php if (!empty($doctor['hinh_anh'])): ?>
                                    <img src="<?php echo htmlspecialchars($doctor['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($doctor['ho_ten']); ?>" class="img-fluid">
                                <?php else: ?>
                                    <img src="assets/img/bacsi/default-doctor.png" alt="Default Doctor Image" class="img-fluid">
                                <?php endif; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($doctor['ho_ten']); ?></h3>
                            <p>Chuyên khoa: <?php echo htmlspecialchars($doctor['ten_chuyenkhoa']); ?></p>
                            <a href="chitiet_bacsi.php?id=<?php echo $doctor['id']; ?>" class="btn btn-outline-primary">Xem chi tiết</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Tin Tức -->
    <section class="health-news">
        <div class="container">
            <h2 class="section-title">Tin tức sức khỏe</h2>
            <div class="row">
                <?php if (empty($news_items)): ?>
                    <div class="col-12 text-center">
                        <p>Chưa có tin tức nào được đăng.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($news_items as $news): ?>
                    <div class="col-md-4 mb-4">
                        <div class="news-card">
                            <?php if (!empty($news['hinh_anh'])): ?>
                                <img src="<?php echo htmlspecialchars($news['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($news['tieu_de']); ?>" class="news-image">
                            <?php else: ?>
                                <img src="assets/img/blog-1.png" alt="Default News Image" class="news-image">
                            <?php endif; ?>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($news['ngay_dang'])); ?>
                                </div>
                                <h3><?php echo htmlspecialchars($news['tieu_de']); ?></h3>
                                <?php 
                                    // Tạo mô tả ngắn từ nội dung
                                    $excerpt = strip_tags($news['noi_dung']);
                                    $excerpt = substr($excerpt, 0, 150) . '...';
                                ?>
                                <p><?php echo $excerpt; ?></p>
                                <a href="chitiet_tintuc.php?id=<?php echo $news['id']; ?>" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="tintuc.php" class="btn btn-primary">Xem tất cả tin tức</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>