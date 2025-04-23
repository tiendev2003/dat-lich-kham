<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner Section -->
    <section class="banner">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h1>Đặt lịch khám trực tuyến</h1>
                    <p>Dễ dàng - Nhanh chóng - Tiện lợi</p>
                    <a href="datlich.php" class="btn btn-primary">Đặt lịch ngay</a>
                </div>
                <div class="col-md-6">
                    <!-- Quick Search Form -->
                    <div class="search-form">
                        <h3>Tìm kiếm nhanh</h3>
                        <form action="search.php" method="GET">
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Tìm bác sĩ hoặc chuyên khoa...">
                                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                            </div>
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
                <div class="col-md-4">
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <i class="fas fa-tooth"></i>
                        </div>
                        <h3>Răng Hàm Mặt</h3>
                        <p>Điều trị các bệnh lý về răng miệng</p>
                        <a href="chuyenkhoa_chitiet.php" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <i class="fas fa-lungs"></i>
                        </div>
                        <h3>Hô Hấp</h3>
                        <p>Chẩn đoán và điều trị các bệnh về đường hô hấp</p>
                        <a href="chuyenkhoa_chitiet.php" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3>Tim Mạch</h3>
                        <p>Chăm sóc và điều trị các bệnh lý về tim mạch</p>
                        <a href="chuyenkhoa_chitiet.php" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <h3>Da liễu</h3>
                        <p>Chăm sóc và điều trị các bệnh lý về da liễu</p>
                        <a href="chuyenkhoa_chitiet.php" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h3>Xét Nghiệm</h3>
                        <p>Xét nghiệm máu, xét nghiệm nước tiểu, xét nghiệm bạch cầu, hổng cầu,...</p>
                        <a href="chuyenkhoa_chitiet.php" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-card">
                        <div class="specialty-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Mắt</h3>
                        <p>Chăm sóc và điều trị các bệnh lý về Mắt</p>
                        <a href="chuyenkhoa_chitiet.php" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tin Tức -->
    <section class="health-news">
        <div class="container">
            <h2 class="section-title">Tin tức sức khỏe</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="news-card">
                        <img src="assets/img/hohap.webp" alt="Tin tức 1" class="news-image">
                        <div class="news-content">
                            <div class="news-date">
                                <i class="far fa-calendar-alt"></i> 15/03/2024
                            </div>
                            <h3>Phòng ngừa các bệnh hô hấp mùa nắng nóng</h3>
                            <p>Tìm hiểu các biện pháp phòng ngừa bệnh hô hấp hiệu quả trong thời tiết nắng nóng...</p>
                            <a href="chitiet_tintuc.php" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="news-card">
                        <img src="assets/img/tintuc_timmach.jpg" alt="Tin tức 2" class="news-image">
                        <div class="news-content">
                            <div class="news-date">
                                <i class="far fa-calendar-alt"></i> 14/03/2024
                            </div>
                            <h3>Chế độ ăn cho người bệnh tim mạch</h3>
                            <p>Những thực phẩm tốt và chế độ ăn uống phù hợp cho người mắc bệnh tim mạch...</p>
                            <a href="chitiet_tintuc.php" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="news-card">
                        <img src="assets/img/rang.jpg" alt="Tin tức 3" class="news-image">
                        <div class="news-content">
                            <div class="news-date">
                                <i class="far fa-calendar-alt"></i> 13/03/2024
                            </div>
                            <h3>Chăm sóc răng miệng đúng cách</h3>
                            <p>Hướng dẫn chi tiết cách chăm sóc răng miệng hàng ngày để có hàm răng khỏe mạnh...</p>
                            <a href="chitiet_tintuc.php" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
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