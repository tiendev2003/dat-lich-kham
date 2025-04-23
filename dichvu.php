<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đội ngũ bác sĩ - Hệ thống đặt lịch khám bệnh</title>
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
            <div class="row">
                <!-- Khám tổng quát -->
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h3>Khám sức khỏe tổng quát</h3>
                        <p>Kiểm tra sức khỏe toàn diện, phát hiện sớm các bệnh lý tiềm ẩn.</p>
                        <div class="service-features">
                            <p><i class="fas fa-check"></i> Khám tổng quát các cơ quan</p>
                            <p><i class="fas fa-check"></i> Xét nghiệm máu cơ bản</p>
                            <p><i class="fas fa-check"></i> Tư vấn kết quả chi tiết</p>
                        </div>
                        <div class="service-price">
                            <span>Từ 500.000đ</span>
                        </div>
                        <a href="chitiet_dichvu.php" class="btn btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- Khám thai -->
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-baby"></i>
                        </div>
                        <h3>Khám thai - Sản phụ khoa</h3>
                        <p>Theo dõi thai kỳ và chăm sóc sức khỏe cho mẹ và bé.</p>
                        <div class="service-features">
                            <p><i class="fas fa-check"></i> Siêu âm thai 4D</p>
                            <p><i class="fas fa-check"></i> Xét nghiệm Double Test</p>
                            <p><i class="fas fa-check"></i> Tư vấn dinh dưỡng</p>
                        </div>
                        <div class="service-price">
                            <span>Từ 400.000đ</span>
                        </div>
                        <a href="chitiet_dichvu.php" class="btn btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- Nội soi -->
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h3>Nội soi</h3>
                        <p>Chẩn đoán sớm các bệnh lý đường tiêu hóa.</p>
                        <div class="service-features">
                            <p><i class="fas fa-check"></i> Nội soi dạ dày</p>
                            <p><i class="fas fa-check"></i> Nội soi đại tràng</p>
                            <p><i class="fas fa-check"></i> Gây mê an toàn</p>
                        </div>
                        <div class="service-price">
                            <span>Từ 800.000đ</span>
                        </div>
                        <a href="chitiet_dichvu.php" class="btn btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- Khám răng -->
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-tooth"></i>
                        </div>
                        <h3>Nha khoa</h3>
                        <p>Chăm sóc và điều trị các vấn đề về răng miệng.</p>
                        <div class="service-features">
                            <p><i class="fas fa-check"></i> Tẩy trắng răng</p>
                            <p><i class="fas fa-check"></i> Điều trị tủy</p>
                            <p><i class="fas fa-check"></i> Bọc răng sứ</p>
                        </div>
                        <div class="service-price">
                            <span>Từ 300.000đ</span>
                        </div>
                        <a href="chitiet_dichvu.php" class="btn btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- Xét nghiệm -->
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-vial"></i>
                        </div>
                        <h3>Xét nghiệm</h3>
                        <p>Các dịch vụ xét nghiệm máu, nước tiểu, vi sinh.</p>
                        <div class="service-features">
                            <p><i class="fas fa-check"></i> Xét nghiệm máu</p>
                            <p><i class="fas fa-check"></i> Sinh hóa máu</p>
                            <p><i class="fas fa-check"></i> Vi sinh</p>
                        </div>
                        <div class="service-price">
                            <span>Từ 200.000đ</span>
                        </div>
                        <a href="chitiet_dichvu.php" class="btn btn-primary">
                            Xem chi tiết
                        </a>
                    </div>
                </div>

                <!-- Chụp X-quang -->
                <div class="col-md-4 mb-4">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-x-ray"></i>
                        </div>
                        <h3>Chụp X-quang & CT</h3>
                        <p>Chẩn đoán hình ảnh chuyên nghiệp.</p>
                        <div class="service-features">
                            <p><i class="fas fa-check"></i> X-quang kỹ thuật số</p>
                            <p><i class="fas fa-check"></i> CT Scanner</p>
                            <p><i class="fas fa-check"></i> Siêu âm 4D</p>
                        </div>
                        <div class="service-price">
                            <span>Từ 250.000đ</span>
                        </div>
                        <a href="chitiet_dichvu.php" class="btn btn-primary">
                            Xem chi tiết
                        </a>
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