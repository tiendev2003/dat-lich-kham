<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đội ngũ bác sĩ - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/chitiet_dichvu.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Breadcrumb -->
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="dichvu.php">Dịch vụ</a></li>
                <li class="breadcrumb-item active">Khám sức khỏe tổng quát</li>
            </ol>
        </nav>
    </div>

    <!-- Service Detail Content -->
    <div class="container my-4">
        <div class="service-detail-container">
            <!-- Service Header -->
            <div class="service-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1>Khám sức khỏe tổng quát</h1>
                        <p class="lead">Dịch vụ khám sức khỏe toàn diện, giúp phát hiện sớm các bệnh lý tiềm ẩn và có biện pháp phòng ngừa kịp thời.</p>
                    </div>
                    <!-- <div class="col-md-4 text-center">
                        <img src="assets/images/kham-tong-quat.jpg" alt="Khám tổng quát" class="img-fluid rounded service-image">
                    </div> -->
                </div>
            </div>

            <!-- Service Description -->
            <div class="service-description mt-5">
                <div class="row">
                    <div class="col-md-8">
                        <h2>Giới thiệu dịch vụ</h2>
                        <p>Khám sức khỏe tổng quát là một trong những dịch vụ y tế quan trọng giúp đánh giá tổng thể tình trạng sức khỏe của người bệnh. Thông qua việc khám tổng quát, bác sĩ có thể:</p>
                        <ul class="service-benefits">
                            <li>Phát hiện sớm các bệnh lý tiềm ẩn</li>
                            <li>Đánh giá các yếu tố nguy cơ sức khỏe</li>
                            <li>Tư vấn phương pháp phòng ngừa bệnh tật</li>
                            <li>Theo dõi diễn biến sức khỏe theo thời gian</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <div class="highlight-box">
                            <h3>Điểm nổi bật</h3>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Đội ngũ bác sĩ chuyên môn cao</li>
                                <li><i class="fas fa-check-circle"></i> Trang thiết bị hiện đại</li>
                                <li><i class="fas fa-check-circle"></i> Kết quả nhanh chóng</li>
                                <li><i class="fas fa-check-circle"></i> Tư vấn chi tiết</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Packages -->
            <div class="service-packages mt-5">
                <h2>Các gói khám</h2>
                <div class="row mt-4">
                    <!-- Basic Package -->
                    <div class="col-md-4 mb-4">
                        <div class="package-card">
                            <div class="package-header">
                                <h3>Gói cơ bản</h3>
                                <div class="price">500.000đ</div>
                            </div>
                            <div class="package-body">
                                <ul>
                                    <li>Khám tổng quát các cơ quan</li>
                                    <li>Đo huyết áp, chiều cao, cân nặng</li>
                                    <li>Xét nghiệm máu cơ bản</li>
                                    <li>Điện tim cơ bản</li>
                                </ul>
                                <a href="datlich.php" class="btn btn-primary w-100">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>

                    <!-- Standard Package -->
                    <div class="col-md-4 mb-4">
                        <div class="package-card featured">
                            <div class="package-header">
                                <h3>Gói nâng cao</h3>
                                <div class="price">1.200.000đ</div>
                            </div>
                            <div class="package-body">
                                <ul>
                                    <li>Tất cả dịch vụ của gói cơ bản</li>
                                    <li>Siêu âm ổ bụng</li>
                                    <li>X-quang ngực</li>
                                    <li>Xét nghiệm chức năng gan, thận</li>
                                    <li>Tư vấn dinh dưỡng</li>
                                </ul>
                                <a href="datlich.php" class="btn btn-primary w-100">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Package -->
                    <div class="col-md-4 mb-4">
                        <div class="package-card">
                            <div class="package-header">
                                <h3>Gói toàn diện</h3>
                                <div class="price">2.500.000đ</div>
                            </div>
                            <div class="package-body">
                                <ul>
                                    <li>Tất cả dịch vụ của gói nâng cao</li>
                                    <li>Đo mật độ xương</li>
                                    <li>Khám chuyên khoa mắt</li>
                                    <li>Khám chuyên khoa tai mũi họng</li>
                                    <li>Tầm soát ung thư cơ bản</li>
                                </ul>
                                <a href="datlich.php" class="btn btn-primary w-100">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Process Section -->
            <div class="service-process mt-5">
                <h2>Quy trình khám</h2>
                <div class="process-steps mt-4">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                                <h4>Đăng ký</h4>
                                <p>Đăng ký thông tin và chọn gói khám phù hợp</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-icon"><i class="fas fa-stethoscope"></i></div>
                                <h4>Khám lâm sàng</h4>
                                <p>Bác sĩ khám tổng quát các cơ quan</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-icon"><i class="fas fa-vial"></i></div>
                                <h4>Xét nghiệm</h4>
                                <p>Thực hiện các xét nghiệm cần thiết</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="step-item">
                                <div class="step-number">4</div>
                                <div class="step-icon"><i class="fas fa-file-medical"></i></div>
                                <h4>Kết quả</h4>
                                <p>Nhận kết quả và tư vấn từ bác sĩ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="service-notes mt-5">
                <h2>Lưu ý khi đến khám</h2>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="note-card">
                            <h4><i class="fas fa-clipboard-list"></i> Chuẩn bị</h4>
                            <ul>
                                <li>Nhịn ăn 6-8 tiếng trước khi xét nghiệm máu</li>
                                <li>Mang theo CMND/CCCD</li>
                                <li>Mang theo các kết quả khám trước đây (nếu có)</li>
                                <li>Đến đúng giờ hẹn</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="note-card">
                            <h4><i class="fas fa-clock"></i> Thời gian</h4>
                            <ul>
                                <li>Thời gian khám: 2-3 giờ</li>
                                <li>Thời gian có kết quả: 1-2 ngày</li>
                                <li>Giờ khám: 7:30 - 16:30</li>
                                <li>Khám từ thứ 2 đến thứ 7</li>
                            </ul>
                        </div>
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