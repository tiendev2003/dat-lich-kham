<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống đặt lịch khám bệnh</title>
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
                    <li class="breadcrumb-item active" aria-current="page">Phòng ngừa các bệnh hô hấp mùa nắng nóng</li>
                </ol>
            </nav>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <article class="news-content">
                        <h1 class="news-title">Phòng ngừa các bệnh hô hấp mùa nắng nóng</h1>
                        
                        <div class="news-meta">
                            <span class="date"><i class="far fa-calendar-alt"></i> 15/03/2024</span>
                            <span class="author"><i class="far fa-user"></i> Bs. Nguyễn Văn A</span>
                            <span class="category"><i class="far fa-folder"></i> Sức khỏe</span>
                        </div>

                        <div class="news-featured-image">
                            <img src="assets/img/blog_hohap.jpg" alt="Phòng ngừa bệnh hô hấp">
                        </div>

                        <div class="news-text">
                            <p class="lead">
                                Mùa nắng nóng là thời điểm các bệnh về đường hô hấp có nguy cơ bùng phát cao. 
                                Bài viết này sẽ cung cấp những thông tin hữu ích giúp bạn và gia đình phòng ngừa hiệu quả.
                            </p>

                            <h2>1. Nguyên nhân gây bệnh hô hấp mùa nắng nóng</h2>
                            <p>
                                Thời tiết nắng nóng có thể gây ra nhiều vấn đề về đường hô hấp do:
                            </p>
                            <ul>
                                <li>Sự chênh lệch nhiệt độ giữa trong nhà và ngoài trời</li>
                                <li>Không khí ô nhiễm và bụi bẩn</li>
                                <li>Virus và vi khuẩn phát triển mạnh</li>
                            </ul>

                            <h2>2. Các biện pháp phòng ngừa</h2>
                            <p>
                                Để phòng ngừa các bệnh hô hấp trong mùa nắng nóng, bạn nên:
                            </p>
                            <ul>
                                <li>Giữ nhiệt độ phòng ổn định</li>
                                <li>Uống đủ nước</li>
                                <li>Đeo khẩu trang khi ra ngoài</li>
                                <li>Vệ sinh mũi họng thường xuyên</li>
                            </ul>

                            <div class="news-quote">
                                <blockquote>
                                    "Phòng bệnh hơn chữa bệnh. Việc thực hiện các biện pháp phòng ngừa đơn giản 
                                    nhưng hiệu quả sẽ giúp bảo vệ sức khỏe của bạn và gia đình."
                                </blockquote>
                                <cite>- TS.BS Nguyễn Văn A -</cite>
                            </div>

                            <h2>3. Khi nào cần đến bác sĩ?</h2>
                            <p>
                                Bạn nên đến gặp bác sĩ khi có các triệu chứng sau:
                            </p>
                            <ul>
                                <li>Ho kéo dài trên 2 tuần</li>
                                <li>Khó thở, tức ngực</li>
                                <li>Sốt cao không giảm</li>
                            </ul>
                        </div>

                        <div class="news-tags">
                            <i class="fas fa-tags"></i>
                            <a href="#">Sức khỏe</a>
                            <a href="#">Hô hấp</a>
                            <a href="#">Mùa nắng</a>
                            <a href="#">Phòng bệnh</a>
                        </div>
                    </article>

                    <!-- Related News -->
                    <div class="related-news">
                        <h3>Tin tức liên quan</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="related-news-item">
                                    <img src="assets/img/news2.jpg" alt="Tin liên quan">
                                    <h4><a href="#">Chế độ ăn tốt cho người bệnh hô hấp</a></h4>
                                    <span class="date">14/03/2024</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="related-news-item">
                                    <img src="assets/img/news3.jpg" alt="Tin liên quan">
                                    <h4><a href="#">Tập thể dục đúng cách trong mùa nắng</a></h4>
                                    <span class="date">13/03/2024</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Latest News -->
                    <div class="sidebar-widget latest-news">
                        <h3>Tin mới nhất</h3>
                        <ul>
                            <li>
                                <a href="#">
                                    <img src="assets/img/news4.jpg" alt="Tin mới">
                                    <div class="news-info">
                                        <h4>Dinh dưỡng cho trẻ mùa nắng nóng</h4>
                                        <span class="date">12/03/2024</span>
                                    </div>
                                </a>
                            </li>
                            <!-- Thêm các tin tức khác -->
                        </ul>
                    </div>

                    <!-- Health Categories -->
                    <div class="sidebar-widget categories">
                        <h3>Chuyên mục</h3>
                        <ul>
                            <li><a href="#">Sức khỏe tổng quát <span>(15)</span></a></li>
                            <li><a href="#">Dinh dưỡng <span>(8)</span></a></li>
                            <li><a href="#">Bệnh mùa nắng <span>(12)</span></a></li>
                            <li><a href="#">Tư vấn sức khỏe <span>(10)</span></a></li>
                        </ul>
                    </div>

                    <!-- Subscribe Newsletter -->
                    <div class="sidebar-widget newsletter">
                        <h3>Đăng ký nhận tin</h3>
                        <p>Nhận thông tin sức khỏe mới nhất qua email</p>
                        <form>
                            <input type="email" placeholder="Email của bạn">
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