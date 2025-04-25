<?php
// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = 'Dịch vụ y tế';
require_once 'includes/functions.php';
include_once 'includes/page_banner.php';

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

// Lấy các thông số từ cài đặt
$service_banner = get_setting('service_banner_image', '');
$service_title = get_setting('service_title', 'Dịch vụ y tế');
$service_subtitle = get_setting('service_subtitle', 'Chăm sóc sức khỏe toàn diện với đội ngũ bác sĩ chuyên nghiệp');

// Helper function to format price
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/dichvu.css">
    <style>
        .service-filters {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .service-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 30px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(var(--primary-color-rgb), 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .service-icon i {
            font-size: 32px;
            color: var(--primary-color);
        }
        
        .service-icon img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .service-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .service-card p {
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .service-features {
            margin-top: auto;
            text-align: left;
            margin-bottom: 20px;
        }
        
        .service-features p {
            margin-bottom: 8px;
            color: #495057;
        }
        
        .service-features i {
            color: var(--primary-color);
            margin-right: 8px;
        }
        
        .service-price {
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 18px;
        }
        
        .service-card .btn {
            border-radius: 5px;
        }
        
        .pagination-container {
            margin-top: 40px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner -->
    <?php display_page_banner(
        htmlspecialchars($service_title),
        htmlspecialchars($service_subtitle),
        !empty($service_banner) ? $service_banner : ''
    ); ?>

    <!-- Services Section -->
    <div class="services-section py-5">
        <div class="container">
            <!-- Filters -->
            <div class="service-filters">
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
                                        case 1: $icon_class = 'fa-heart-pulse'; break; // Tim mạch
                                        case 2: $icon_class = 'fa-baby'; break; // Nhi khoa
                                        case 3: $icon_class = 'fa-allergies'; break; // Da liễu
                                        case 5: $icon_class = 'fa-eye'; break; // Mắt
                                        case 6: $icon_class = 'fa-tooth'; break; // Răng
                                        case 7: $icon_class = 'fa-lungs'; break; // Hô hấp
                                        case 8: $icon_class = 'fa-brain'; break; // Thần kinh
                                        case 9: $icon_class = 'fa-bone'; break; // Xương khớp
                                        case 10: $icon_class = 'fa-flask-vial'; break; // Xét nghiệm
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
                                            echo "<p><i class=\"fas fa-check-circle\"></i> " . $feature . "</p>";
                                            $count++;
                                        }
                                    }
                                } else {
                                    // Default features
                                    echo "<p><i class=\"fas fa-check-circle\"></i> Dịch vụ chuyên nghiệp</p>";
                                    echo "<p><i class=\"fas fa-check-circle\"></i> Đội ngũ y bác sĩ giàu kinh nghiệm</p>";
                                    echo "<p><i class=\"fas fa-check-circle\"></i> Trang thiết bị hiện đại</p>";
                                }
                                ?>
                            </div>
                            
                            <div class="service-price">
                                <span><?= formatPrice($service['gia_coban']) ?></span>
                            </div>
                            
                            <a href="chitiet_dichvu.php?id=<?= $service['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-info-circle me-2"></i>Xem chi tiết
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Không tìm thấy dịch vụ phù hợp. Vui lòng thử lại với tiêu chí khác.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-container mt-5">
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Service pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= ($page-1) ?><?= ($specialty_id > 0) ? '&specialty='.$specialty_id : '' ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        <?php
                        // Show limited page numbers with ellipsis
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        // Show first page and ellipsis if needed
                        if ($start_page > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=1' . (($specialty_id > 0) ? '&specialty='.$specialty_id : '') . '">1</a></li>';
                            if ($start_page > 2) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                        }
                        
                        // Show middle pages
                        for($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= ($specialty_id > 0) ? '&specialty='.$specialty_id : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor;
                        
                        // Show last page and ellipsis if needed
                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }
                            echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . (($specialty_id > 0) ? '&specialty='.$specialty_id : '') . '">' . $total_pages . '</a></li>';
                        }
                        ?>
                        
                        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= ($page+1) ?><?= ($specialty_id > 0) ? '&specialty='.$specialty_id : '' ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="text-center mt-2 text-muted">
                    <small>Hiển thị <?= count($services) ?> dịch vụ (trang <?= $page ?>/<?= $total_pages ?>)</small>
                </div>
                <?php endif; ?>
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