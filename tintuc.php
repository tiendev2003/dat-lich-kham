<?php
// Kết nối database
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';
require_once 'admin/crud/tintuc_crud.php';

// Xử lý phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 6;
$offset = ($page - 1) * $items_per_page;

// Lấy tổng số tin tức
global $conn;
$count_query = "SELECT COUNT(*) as total FROM tintuc WHERE trang_thai = 'published'";
$count_result = $conn->query($count_query);
$count_data = $count_result->fetch_assoc();
$total_items = $count_data['total'];
$total_pages = ceil($total_items / $items_per_page);

// Lấy danh sách tin tức đã xuất bản với phân trang
$filter = [
    'status' => 'published'
];
$news_list = getAllNews($filter);
$paginated_news = array_slice($news_list, $offset, $items_per_page);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức y tế - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/tintuc.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- News Section -->
    <section class="news">
        <div class="container">
            <h1 class="page-title">Tin tức y tế</h1>
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
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page-1) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= ($page+1) ?>" aria-label="Next">
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