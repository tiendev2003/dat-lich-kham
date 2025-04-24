<?php
// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = 'Đội ngũ bác sĩ';
require_once 'includes/functions.php';

// Kết nối database
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';

// Lấy thông số từ cài đặt
$doctors_title = get_setting('doctors_title', 'Đội ngũ bác sĩ');
$doctors_subtitle = get_setting('doctors_subtitle', 'Các bác sĩ chuyên nghiệp và giàu kinh nghiệm của chúng tôi');
$doctors_banner = get_setting('doctors_banner_image', '');

// Xử lý lọc theo chuyên khoa
$specialty_id = isset($_GET['specialty']) ? (int)$_GET['specialty'] : 0;

// Xử lý phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 6;
$offset = ($page - 1) * $items_per_page;

// Lấy danh sách chuyên khoa cho bộ lọc
$specialties_sql = "SELECT * FROM chuyenkhoa ORDER BY ten_chuyenkhoa";
$specialties_result = $conn->query($specialties_sql);
$specialties = [];

if ($specialties_result && $specialties_result->num_rows > 0) {
    while ($row = $specialties_result->fetch_assoc()) {
        $specialties[] = $row;
    }
}

// Điều kiện lọc theo chuyên khoa
$where_clause = "";
if ($specialty_id > 0) {
    $where_clause = "WHERE chuyenkhoa_id = $specialty_id";
}

// Lấy tổng số bác sĩ
$count_sql = "SELECT COUNT(*) as total FROM bacsi $where_clause";
$count_result = $conn->query($count_sql);
$count_data = $count_result->fetch_assoc();
$total_items = $count_data['total'];
$total_pages = ceil($total_items / $items_per_page);

// Lấy danh sách bác sĩ với phân trang
$doctors_sql = "SELECT b.*, ck.ten_chuyenkhoa 
                FROM bacsi b 
                LEFT JOIN chuyenkhoa ck ON b.chuyenkhoa_id = ck.id 
                $where_clause 
                ORDER BY b.ho_ten 
                LIMIT $offset, $items_per_page";
$doctors_result = $conn->query($doctors_sql);
$doctors = [];

if ($doctors_result && $doctors_result->num_rows > 0) {
    while ($row = $doctors_result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/doctors.css">
    <style>
        .doctor-banner {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('<?php echo !empty($doctors_banner) ? $doctors_banner : 'assets/img/doctors-banner.jpg'; ?>');
            background-size: cover;
            background-position: center;
            padding: 80px 0;
            margin-bottom: 40px;
            color: #fff;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner Section -->
    <div class="doctor-banner">
        <div class="container">
            <h1 class="display-4"><?php echo htmlspecialchars($doctors_title); ?></h1>
            <p class="lead"><?php echo htmlspecialchars($doctors_subtitle); ?></p>
        </div>
    </div>

    <!-- Doctor List Section -->
    <section class="doctor-list">
        <div class="container">
            <!-- Filters -->
            <div class="doctor-filters mb-4">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="specialty" class="form-label">Chuyên khoa</label>
                        <select name="specialty" id="specialty" class="form-select" onchange="this.form.submit()">
                            <option value="0">Tất cả chuyên khoa</option>
                            <?php foreach ($specialties as $specialty): ?>
                                <option value="<?= $specialty['id'] ?>" <?= $specialty_id == $specialty['id'] ? 'selected' : '' ?>>
                                    <?= $specialty['ten_chuyenkhoa'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            
            <div class="row">
                <?php if (count($doctors) > 0): ?>
                    <?php foreach ($doctors as $doctor): ?>
                    <div class="col-md-4 mb-4">
                        <div class="doctor-card">
                            <div class="doctor-image">
                                <?php if (!empty($doctor['hinh_anh'])): ?>
                                    <img src="<?= $doctor['hinh_anh'] ?>" alt="<?= $doctor['ho_ten'] ?>">
                                <?php else: ?>
                                    <img src="assets/img/doctor-default.jpg" alt="<?= $doctor['ho_ten'] ?>">
                                <?php endif; ?>
                            </div>
                            <div class="doctor-info">
                                <h3><?= $doctor['ho_ten'] ?></h3>
                                <p class="specialty">Chuyên khoa <?= $doctor['ten_chuyenkhoa'] ?? 'Đa khoa' ?></p>
                                <p class="experience">
                                    <?php 
                                    echo !empty($doctor['kinh_nghiem']) 
                                        ? $doctor['kinh_nghiem'] 
                                        : 'Bác sĩ chuyên khoa';
                                    ?>
                                </p>
                                <div class="doctor-actions">
                                    <a href="chitiet_bacsi.php?id=<?= $doctor['id'] ?>" class="btn btn-outline-primary">Xem chi tiết</a>
                                    <a href="datlich.php?doctor_id=<?= $doctor['id'] ?>" class="btn btn-primary">Đặt lịch khám</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Không tìm thấy bác sĩ phù hợp với tiêu chí tìm kiếm. Vui lòng thử lại với tiêu chí khác.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page-1) ?>&specialty=<?= $specialty_id ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&specialty=<?= $specialty_id ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page+1) ?>&specialty=<?= $specialty_id ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
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