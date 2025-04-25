<?php
// Kết nối database
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
include_once 'includes/page_banner.php';

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = 'Tin tức y tế';

// Lấy các tham số lọc
$category = isset($_GET['category']) ? $_GET['category'] : '';
$tag = isset($_GET['tag']) ? $_GET['tag'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Xử lý phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 6;
$offset = ($page - 1) * $items_per_page;

// Lấy tổng số tin tức
$whereClause = "WHERE trang_thai = 'published'";

if (!empty($category)) {
    $category = $conn->real_escape_string($category);
    $whereClause .= " AND danh_muc = '$category'";
}

if (!empty($tag)) {
    $tag = $conn->real_escape_string($tag);
    $whereClause .= " AND tags LIKE '%$tag%'";
}

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $whereClause .= " AND (tieu_de LIKE '%$search%' OR noi_dung LIKE '%$search%' OR meta_description LIKE '%$search%')";
}

$count_query = "SELECT COUNT(*) as total FROM tintuc $whereClause";
$count_result = $conn->query($count_query);
$count_data = $count_result->fetch_assoc();
$total_items = $count_data['total'];
$total_pages = ceil($total_items / $items_per_page);

// Lấy danh sách tin tức đã xuất bản với phân trang
$news_query = "SELECT * FROM tintuc $whereClause ORDER BY ngay_dang DESC LIMIT $offset, $items_per_page";
$news_result = $conn->query($news_query);
$paginated_news = [];
if ($news_result->num_rows > 0) {
    while($row = $news_result->fetch_assoc()) {
        $paginated_news[] = $row;
    }
}

// Lấy danh sách danh mục
$categories_query = "SELECT danh_muc, COUNT(*) as count FROM tintuc WHERE trang_thai = 'published' GROUP BY danh_muc";
$categories_result = $conn->query($categories_query);
$categories = [];
if ($categories_result->num_rows > 0) {
    while($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/tintuc.css">
    <style>
        .filters-container {
            margin-top: 30px;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    
    <!-- Banner -->
    <?php display_page_banner('Tin tức y tế', 'Cập nhật những thông tin y tế mới nhất và hữu ích'); ?>

    <!-- News Section -->
    <section class="news">
        <div class="container">
            <!-- Filters -->
            <div class="filters-container">
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <select name="category" class="form-select">
                                <option value="">-- Tất cả danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['danh_muc'] ?>" <?= ($category == $cat['danh_muc']) ? 'selected' : '' ?>>
                                        <?= ucfirst($cat['danh_muc']) ?> (<?= $cat['count'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tin tức..." value="<?= htmlspecialchars($search) ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Tìm kiếm
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <?php if (!empty($category) || !empty($search) || !empty($tag)): ?>
                                <a href="tintuc.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i> Xóa bộ lọc
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($tag)): ?>
                    <div class="mt-2 mb-3">
                        <div class="d-flex align-items-center">
                            <span class="me-2">Đang lọc theo thẻ:</span>
                            <span class="badge bg-secondary p-2"><?= htmlspecialchars($tag) ?> 
                                <a href="tintuc.php<?= !empty($category) ? '?category=' . urlencode($category) : '' ?><?= !empty($search) ? (!empty($category) ? '&' : '?') . 'search=' . urlencode($search) : '' ?>" class="text-white ms-2">
                                    <i class="fas fa-times"></i>
                                </a>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                </form>

                <?php if (count($paginated_news) == 0 && (!empty($search) || !empty($category) || !empty($tag))): ?>
                <div class="alert alert-info mb-4">
                    <p>Không tìm thấy tin tức phù hợp với bộ lọc. <a href="tintuc.php">Xem tất cả tin tức</a></p>
                </div>
                <?php endif; ?>
            </div>

            <div class="row">
                <?php if (count($paginated_news) > 0): ?>
                    <?php foreach($paginated_news as $news): ?>
                        <div class="col-md-4 mb-4">
                            <div class="news-card">
                                <?php if(!empty($news['hinh_anh'])): ?>
                                    <img src="<?= $news['hinh_anh'] ?>" alt="<?= $news['tieu_de'] ?>" class="news-image">
                                <?php else: ?>
                                    <img src="assets/img/blog-1.png" alt="<?= $news['tieu_de'] ?>" class="news-image">
                                <?php endif; ?>
                                <div class="news-content">
                                    <h3><?= $news['tieu_de'] ?></h3>
                                    <p>
                                        <?php
                                        $description = !empty($news['meta_description']) 
                                            ? $news['meta_description'] 
                                            : (strlen(strip_tags($news['noi_dung'])) > 100 
                                                ? substr(strip_tags($news['noi_dung']), 0, 100) . '...' 
                                                : strip_tags($news['noi_dung']));
                                        echo $description;
                                        ?>
                                    </p>
                                    <a href="chitiet_tintuc.php?id=<?= $news['id'] ?>" class="btn btn-outline-primary">Đọc thêm</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p>Không có tin tức nào.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <!-- First page -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=1<?= !empty($category) ? '&category=' . urlencode($category) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($tag) ? '&tag=' . urlencode($tag) : '' ?>" aria-label="First">
                            <span aria-hidden="true">Đầu</span>
                        </a>
                    </li>
                    
                    <!-- Previous page -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page-1) ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($tag) ? '&tag=' . urlencode($tag) : '' ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <!-- Page numbers -->
                    <?php
                    // Hiển thị số giới hạn các trang (tối đa 5)
                    $start_page = max(1, min($page - 2, $total_pages - 4));
                    $end_page = min($total_pages, max(5, $page + 2));
                    
                    // Hiển thị dấu "..." nếu không bắt đầu từ trang 1
                    if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1<?= !empty($category) ? '&category=' . urlencode($category) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($tag) ? '&tag=' . urlencode($tag) : '' ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#">...</a>
                        </li>
                        <?php endif; 
                    endif;
                    
                    // Hiển thị các số trang
                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($tag) ? '&tag=' . urlencode($tag) : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor;
                    
                    // Hiển thị dấu "..." nếu không kết thúc ở trang cuối
                    if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled">
                            <a class="page-link" href="#">...</a>
                        </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $total_pages ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($tag) ? '&tag=' . urlencode($tag) : '' ?>"><?= $total_pages ?></a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Next page -->
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page+1) ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($tag) ? '&tag=' . urlencode($tag) : '' ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                    
                    <!-- Last page -->
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $total_pages ?><?= !empty($category) ? '&category=' . urlencode($category) : '' ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($tag) ? '&tag=' . urlencode($tag) : '' ?>" aria-label="Last">
                            <span aria-hidden="true">Cuối</span>
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