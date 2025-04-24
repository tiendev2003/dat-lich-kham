<?php
// Kết nối database
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';
require_once 'admin/crud/tintuc_crud.php';

// Lấy ID tin tức từ tham số URL
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Nếu không có ID hợp lệ, chuyển hướng về trang tin tức
if ($news_id <= 0) {
    header('Location: tintuc.php');
    exit;
}

// Lấy thông tin chi tiết tin tức
$news_detail = getNewsById($news_id);

// Nếu không tìm thấy tin tức hoặc tin chưa được xuất bản, chuyển hướng về trang tin tức
if (!$news_detail || $news_detail['trang_thai'] !== 'published') {
    header('Location: tintuc.php');
    exit;
}

// Cập nhật lượt xem
global $conn;
$conn->query("UPDATE tintuc SET luot_xem = luot_xem + 1 WHERE id = {$news_id}");

// Lấy tin tức liên quan (cùng danh mục)
$related_filter = [
    'category' => $news_detail['danh_muc'],
    'status' => 'published'
];
$related_news = getAllNews($related_filter);

// Loại bỏ tin tức hiện tại khỏi danh sách liên quan
$related_news = array_filter($related_news, function($item) use ($news_id) {
    return $item['id'] != $news_id;
});

// Giới hạn số tin liên quan hiển thị
$related_news = array_slice($related_news, 0, 2);

// Lấy các tin tức mới nhất
$latest_filter = [
    'status' => 'published'
];
$latest_news = getAllNews($latest_filter);
$latest_news = array_slice($latest_news, 0, 5);

// Format date
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d/m/Y', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $news_detail['tieu_de'] ?> - Hệ thống đặt lịch khám bệnh</title>
    <?php if (!empty($news_detail['meta_description'])): ?>
    <meta name="description" content="<?= $news_detail['meta_description'] ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/chitiet_tintuc.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- News Detail Section -->
    <div class="news-detail">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="tintuc.php">Tin tức</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= $news_detail['tieu_de'] ?></li>
                </ol>
            </nav>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <article class="news-content">
                        <h1 class="news-title"><?= $news_detail['tieu_de'] ?></h1>
                        
                        <div class="news-meta">
                            <span class="date"><i class="far fa-calendar-alt"></i> <?= formatDate($news_detail['ngay_dang']) ?></span>
                            <span class="category"><i class="far fa-folder"></i> <?= ucfirst($news_detail['danh_muc']) ?></span>
                            <span class="views"><i class="far fa-eye"></i> <?= $news_detail['luot_xem'] ?? 0 ?> lượt xem</span>
                        </div>

                        <?php if (!empty($news_detail['hinh_anh'])): ?>
                        <div class="news-featured-image">
                            <img src="<?= $news_detail['hinh_anh'] ?>" alt="<?= $news_detail['tieu_de'] ?>">
                        </div>
                        <?php endif; ?>

                        <div class="news-text">
                            <?= $news_detail['noi_dung'] ?>
                        </div>

                        <?php if (!empty($news_detail['tags'])): ?>
                        <div class="news-tags">
                            <i class="fas fa-tags"></i>
                            <?php 
                            $tags = explode(',', $news_detail['tags']);
                            foreach($tags as $tag): 
                                $tag = trim($tag);
                                if(!empty($tag)):
                            ?>
                                <a href="tintuc.php?tag=<?= urlencode($tag) ?>"><?= $tag ?></a>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        <?php endif; ?>
                    </article>

                    <!-- Related News -->
                    <?php if (count($related_news) > 0): ?>
                    <div class="related-news">
                        <h3>Tin tức liên quan</h3>
                        <div class="row">
                            <?php foreach($related_news as $related): ?>
                            <div class="col-md-6">
                                <div class="related-news-item">
                                    <?php if(!empty($related['hinh_anh'])): ?>
                                    <img src="<?= $related['hinh_anh'] ?>" alt="<?= $related['tieu_de'] ?>">
                                    <?php else: ?>
                                    <img src="assets/img/news2.jpg" alt="<?= $related['tieu_de'] ?>">
                                    <?php endif; ?>
                                    <h4><a href="chitiet_tintuc.php?id=<?= $related['id'] ?>"><?= $related['tieu_de'] ?></a></h4>
                                    <span class="date"><?= formatDate($related['ngay_dang']) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Latest News -->
                    <div class="sidebar-widget latest-news">
                        <h3>Tin mới nhất</h3>
                        <ul>
                            <?php foreach($latest_news as $latest): ?>
                                <?php if($latest['id'] == $news_id) continue; // Skip current article ?>
                                <li>
                                    <a href="chitiet_tintuc.php?id=<?= $latest['id'] ?>">
                                        <?php if(!empty($latest['hinh_anh'])): ?>
                                            <img src="<?= $latest['hinh_anh'] ?>" alt="<?= $latest['tieu_de'] ?>">
                                        <?php else: ?>
                                            <img src="assets/img/news4.jpg" alt="<?= $latest['tieu_de'] ?>">
                                        <?php endif; ?>
                                        <div class="news-info">
                                            <h4><?= $latest['tieu_de'] ?></h4>
                                            <span class="date"><?= formatDate($latest['ngay_dang']) ?></span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Health Categories -->
                    <div class="sidebar-widget categories">
                        <h3>Chuyên mục</h3>
                        <ul>
                            <?php
                            // Lấy danh sách các danh mục và đếm số tin tức trong mỗi danh mục
                            global $conn;
                            $categories_query = "SELECT danh_muc, COUNT(*) as count FROM tintuc WHERE trang_thai = 'published' GROUP BY danh_muc";
                            $categories_result = $conn->query($categories_query);
                            while($category = $categories_result->fetch_assoc()):
                            ?>
                                <li>
                                    <a href="tintuc.php?category=<?= urlencode($category['danh_muc']) ?>">
                                        <?= ucfirst($category['danh_muc']) ?> <span>(<?= $category['count'] ?>)</span>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Subscribe Newsletter -->
                    <div class="sidebar-widget newsletter">
                        <h3>Đăng ký nhận tin</h3>
                        <p>Nhận thông tin sức khỏe mới nhất qua email</p>
                        <form id="newsletterForm">
                            <input type="email" placeholder="Email của bạn" required>
                            <button type="submit" class="btn btn-primary">Đăng ký</button>
                        </form>
                    </div>
                </div>
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