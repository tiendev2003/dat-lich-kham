<?php
// Kết nối database
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';

// Xử lý lọc theo chuyên khoa
$specialty_id = isset($_GET['specialty']) ? (int)$_GET['specialty'] : 0;

// Xử lý phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 9;
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
$where_clause = "WHERE trangthai = 1";
if ($specialty_id > 0) {
    $where_clause .= " AND chuyenkhoa_id = $specialty_id";
}

// Lấy tổng số dịch vụ
$count_sql = "SELECT COUNT(*) as total FROM dichvu $where_clause";
$count_result = $conn->query($count_sql);
$count_data = $count_result->fetch_assoc();
$total_items = $count_data['total'];
$total_pages = ceil($total_items / $items_per_page);

// Lấy danh sách dịch vụ với phân trang
$services_sql = "SELECT d.*, ck.ten_chuyenkhoa 
                FROM dichvu d 
                LEFT JOIN chuyenkhoa ck ON d.chuyenkhoa_id = ck.id 
                $where_clause 
                ORDER BY d.ten_dichvu 
                LIMIT $offset, $items_per_page";
$services_result = $conn->query($services_sql);
$services = [];

if ($services_result && $services_result->num_rows > 0) {
    while ($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Helper function to format price
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch vụ y tế - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/dichvu.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner Section -->
    <div class="service-banner">
        <div class="container">
            <h1 class="text-center">Dịch vụ y tế</h1>
            <p class="text-center">Chăm sóc sức khỏe toàn diện với đội ngũ bác sĩ chuyên nghiệp</p>
        </div>
    </div>

    <!-- Services Section -->
    <div class="services-section">
        <div class="container">
            <!-- Filters -->
            <div class="service-filters mb-4">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label for="specialty" class="form-label">Chuyên khoa</label>
                        <select name="specialty" id="specialty" class="form-select" onchange="this.form.submit()">
                            <option value="0">Tất cả dịch vụ</option>
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
                <?php if (count($services) > 0): ?>
                    <?php foreach ($services as $service): ?>
                    <div class="col-md-4 mb-4">
                        <div class="service-card">
                            <div class="service-icon">
                                <?php if (!empty($service['hinh_anh'])): ?>
                                    <img src="<?= $service['hinh_anh'] ?>" alt="<?= $service['ten_dichvu'] ?>">
                                <?php else: ?>
                                    <?php
                                    // Default icons based on specialty if available
                                    $icon_class = 'fa-stethoscope';
                                    switch($service['chuyenkhoa_id']) {
                                        case 1: $icon_class = 'fa-heart'; break; // Tim mạch
                                        case 2: $icon_class = 'fa-baby'; break; // Nhi khoa
                                        case 3: $icon_class = 'fa-allergies'; break; // Da liễu
                                        case 5: $icon_class = 'fa-eye'; break; // Mắt
                                        default: $icon_class = 'fa-stethoscope';
                                    }
                                    ?>
                                    <i class="fas <?= $icon_class ?>"></i>
                                <?php endif; ?>
                            </div>
                            <h3><?= $service['ten_dichvu'] ?></h3>
                            <p><?= !empty($service['mota_ngan']) ? $service['mota_ngan'] : 'Dịch vụ chăm sóc sức khỏe chuyên nghiệp.' ?></p>
                            
                            <div class="service-features">
                                <?php
                                // Extract features from detailed description if available
                                if (!empty($service['chi_tiet'])) {
                                    $features = explode("\n", $service['chi_tiet']);
                                    $count = 0;
                                    foreach ($features as $feature) {
                                        $feature = trim($feature);
                                        if (!empty($feature) && $count < 3) {
                                            echo "<p><i class=\"fas fa-check\"></i> " . $feature . "</p>";
                                            $count++;
                                        }
                                    }
                                } else {
                                    // Default features
                                    echo "<p><i class=\"fas fa-check\"></i> Dịch vụ chuyên nghiệp</p>";
                                    echo "<p><i class=\"fas fa-check\"></i> Đội ngũ y bác sĩ giàu kinh nghiệm</p>";
                                    echo "<p><i class=\"fas fa-check\"></i> Trang thiết bị hiện đại</p>";
                                }
                                ?>
                            </div>
                            
                            <div class="service-price">
                                <span>Từ <?= formatPrice($service['gia_coban']) ?></span>
                            </div>
                            
                            <a href="chitiet_dichvu.php?id=<?= $service['id'] ?>" class="btn btn-primary">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-info">
                            Không tìm thấy dịch vụ phù hợp. Vui lòng thử lại với tiêu chí khác.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Service pagination" class="my-4">
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
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>