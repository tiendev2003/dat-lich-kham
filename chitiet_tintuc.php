<?php
// Thiết lập tiêu đề trang cho head.php
// Start the session before any output
session_start();

// Kết nối database và load functions
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';
require_once 'admin/crud/tintuc_crud.php';
require_once 'includes/functions.php';

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

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = $news_detail['tieu_de'];

// Lấy thông số từ cài đặt
$site_name = get_setting('site_name', 'Phòng Khám Lộc Bình');

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

// Lấy thông tin người tạo bài viết
$author_info = null;
if (!empty($news_detail['nguoi_tao'])) {
    // Kiểm tra nếu người tạo là bác sĩ
    $author_query = "SELECT b.*, 'doctor' as type FROM bacsi b 
                     LEFT JOIN users u ON b.nguoidung_id = u.id
                     WHERE u.id = {$news_detail['nguoi_tao']} LIMIT 1";
    $author_result = $conn->query($author_query);
    
    if ($author_result && $author_result->num_rows > 0) {
        $author_info = $author_result->fetch_assoc();
    } else {
        // Nếu không phải bác sĩ, lấy thông tin từ bảng users
        $user_query = "SELECT u.*, 'admin' as type FROM users u 
                       WHERE u.id = {$news_detail['nguoi_tao']} LIMIT 1";
        $user_result = $conn->query($user_query);
        
        if ($user_result && $user_result->num_rows > 0) {
            $author_info = $user_result->fetch_assoc();
        }
    }
}

// Format date
function formatDate($date) {
    if (!$date) return 'N/A';
    return date('d/m/Y', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/chitiet_tintuc.css">
    <style>
        .news-detail {
            padding: 40px 0;
        }
        .news-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        .news-meta {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
            color: #6c757d;
            font-size: 14px;
        }
        .news-meta span {
            margin-right: 20px;
            margin-bottom: 5px;
        }
        .news-meta i {
            margin-right: 5px;
        }
        .news-featured-image {
            margin-bottom: 25px;
            border-radius: 8px;
            overflow: hidden;
        }
        .news-featured-image img {
            width: 100%;
            height: auto;
        }
        .news-text {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 25px;
        }
        .news-tags {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .news-tags a {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
            padding: 5px 12px;
            background-color: #f8f9fa;
            color: #6c757d;
            border-radius: 20px;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        .news-tags a:hover {
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
        }
        .related-news {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }
        .related-news h3 {
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        .related-news-item {
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .related-news-item:hover {
            transform: translateY(-5px);
        }
        .related-news-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .related-news-item h4 {
            padding: 15px;
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        .related-news-item h4 a {
            color: #333;
            text-decoration: none;
        }
        .related-news-item h4 a:hover {
            color: var(--primary-color);
        }
        .related-news-item .date {
            padding: 0 15px 15px;
            display: block;
            color: #6c757d;
            font-size: 12px;
        }
        .sidebar-widget {
            margin-bottom: 40px;
            padding: 25px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .sidebar-widget h3 {
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-color);
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .latest-news ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .latest-news li {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        .latest-news li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .latest-news li a {
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .latest-news img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        .latest-news .news-info {
            flex: 1;
        }
        .latest-news h4 {
            margin: 0 0 5px;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .latest-news .date {
            font-size: 12px;
            color: #6c757d;
        }
        .categories ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .categories li {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        .categories li:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .categories li a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .categories li a:hover {
            color: var(--primary-color);
        }
        .categories span {
            color: #6c757d;
            font-size: 12px;
        }
        .newsletter p {
            margin-bottom: 15px;
        }
        .newsletter input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }
        .newsletter button {
            width: 100%;
        }
        .author-box {
            margin-top: 30px;
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        .author-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 20px;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .author-info {
            flex: 1;
        }
        .author-info h4 {
            margin: 0 0 5px;
            font-weight: 600;
            color: var(--primary-color);
        }
        .author-info p.position {
            font-style: italic;
            margin-bottom: 10px;
            color: #6c757d;
        }
        .author-info p.bio {
            margin-bottom: 10px;
            font-size: 14px;
        }
        .author-social {
            margin-top: 10px;
        }
        .author-social a {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background-color: #e9ecef;
            color: #6c757d;
            border-radius: 50%;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .author-social a:hover {
            background-color: var(--primary-color);
            color: white;
        }
        :root {
            --primary-color-rgb: <?php 
                $hex = ltrim(get_setting('primary_color', '#005bac'), '#');
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                echo "$r,$g,$b";
            ?>;
        }
    </style>
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
                            <?php if($author_info): ?>
                            <span class="author"><i class="far fa-user"></i> 
                                <?php if($author_info['type'] == 'doctor'): ?>
                                    BS. <?= $author_info['ho_ten'] ?>
                                <?php else: ?>
                                    <?= $author_info['name'] ?? $author_info['username'] ?>
                                <?php endif; ?>
                            </span>
                            <?php endif; ?>
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

                        <?php if($author_info && $author_info['type'] == 'doctor'): ?>
                        <div class="author-box">
                            <div class="author-avatar">
                                <?php if(!empty($author_info['hinh_anh'])): ?>
                                <img src="<?= $author_info['hinh_anh'] ?>" alt="<?= $author_info['ho_ten'] ?>">
                                <?php else: ?>
                                <img src="assets/img/doctor-default.jpg" alt="<?= $author_info['ho_ten'] ?>">
                                <?php endif; ?>
                            </div>
                            <div class="author-info">
                                <h4>BS. <?= $author_info['ho_ten'] ?></h4>
                                <?php
                                // Lấy tên chuyên khoa nếu có
                                $specialty_name = '';
                                if(!empty($author_info['chuyenkhoa_id'])) {
                                    $specialty_query = "SELECT ten_chuyenkhoa FROM chuyenkhoa WHERE id = {$author_info['chuyenkhoa_id']} LIMIT 1";
                                    $specialty_result = $conn->query($specialty_query);
                                    if($specialty_result && $specialty_result->num_rows > 0) {
                                        $specialty_name = $specialty_result->fetch_assoc()['ten_chuyenkhoa'];
                                    }
                                }
                                ?>
                                <p class="position">Bác sĩ <?= !empty($specialty_name) ? "Chuyên khoa " . $specialty_name : "" ?></p>
                                <?php if(!empty($author_info['gioi_thieu'])): ?>
                                <p class="bio"><?= substr($author_info['gioi_thieu'], 0, 150) ?>...</p>
                                <?php endif; ?>
                                <div class="author-social">
                                    <a href="chitiet_bacsi.php?id=<?= $author_info['id'] ?>" title="Xem thông tin bác sĩ"><i class="fas fa-user-md"></i></a>
                                    <?php if(!empty($author_info['email'])): ?>
                                    <a href="mailto:<?= $author_info['email'] ?>" title="Email"><i class="far fa-envelope"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
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
    
    <script>
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Đăng ký nhận tin thành công! Cảm ơn bạn đã đăng ký.');
        this.reset();
    });
    </script>
</body>
</html>