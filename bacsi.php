<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đội ngũ bác sĩ - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="assets/css/pages/doctors.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Doctor List Section -->
    <section class="doctor-list">
        <div class="container">
            <h1 class="page-title">Đội ngũ bác sĩ</h1>
            <div class="row">
                <!-- Doctor 1 -->
                <div class="col-md-4 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_rang.jpg" alt="Bác sĩ Nguyễn Thế Lâm">
                        </div>
                        <div class="doctor-info">
                            <h3>BS. Nguyễn Thế Lâm</h3>
                            <p class="specialty">Chuyên khoa Răng Hàm Mặt</p>
                            <p class="experience">Hơn 15 năm kinh nghiệm</p>
                            <div class="doctor-actions">
                                <a href="chitiet_bacsi.php.php" class="btn btn-outline-primary">Xem chi tiết</a>
                                <a href="datlich.php" class="btn btn-primary">Đặt lịch khám</a>
                                </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor 2 -->
                <div class="col-md-4 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_hohap.jpg" alt="Bác sĩ Trần Thị B">
                        </div>
                        <div class="doctor-info">
                            <h3>BS. Trần Thị B</h3>
                            <p class="specialty">Chuyên khoa Hô Hấp</p>
                            <p class="experience">Hơn 12 năm kinh nghiệm</p>
                            <div class="doctor-actions">
                            <a href="chitiet_bacsi.php" class="btn btn-outline-primary">Xem chi tiết</a>
                            <a href="datlich.php" class="btn btn-primary">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor 3 -->
                <div class="col-md-4 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_timmach.jpg" alt="Bác sĩ Lê Văn C">
                        </div>
                        <div class="doctor-info">
                            <h3>BS. Lê Văn C</h3>
                            <p class="specialty">Chuyên khoa Tim Mạch</p>
                            <p class="experience">Hơn 20 năm kinh nghiệm</p>
                            <div class="doctor-actions">
                            <a href="chitiet_bacsi.php" class="btn btn-outline-primary">Xem chi tiết</a>
                            <a href="datlich.php" class="btn btn-primary">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor 4 -->
                <div class="col-md-4 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_dalieu.jpg" alt="Bác sĩ Phạm Thị D">
                        </div>
                        <div class="doctor-info">
                            <h3>BS. Phạm Thị D</h3>
                            <p class="specialty">Chuyên khoa Da Liễu</p>
                            <p class="experience">Hơn 10 năm kinh nghiệm</p>
                            <div class="doctor-actions">
                            <a href="chitiet_bacsi.php" class="btn btn-outline-primary">Xem chi tiết</a>
                            <a href="datlich.php" class="btn btn-primary">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor 5 -->
                <div class="col-md-4 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_xetnghiem.png" alt="Bác sĩ Hoàng Văn E">
                        </div>
                        <div class="doctor-info">
                            <h3>BS. Hoàng Văn E</h3>
                            <p class="specialty">Chuyên khoa Xét Nghiệm</p>
                            <p class="experience">Hơn 8 năm kinh nghiệm</p>
                            <div class="doctor-actions">
                            <a href="chitiet_bacsi.php" class="btn btn-outline-primary">Xem chi tiết</a>
                            <a href="datlich.php" class="btn btn-primary">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor 6 -->
                <div class="col-md-4 mb-4">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_mat.jpg" alt="Bác sĩ Vũ Thị F">
                        </div>
                        <div class="doctor-info">
                            <h3>BS. Vũ Thị F</h3>
                            <p class="specialty">Chuyên khoa Mắt</p>
                            <p class="experience">Hơn 15 năm kinh nghiệm</p>
                            <div class="doctor-actions">
                            <a href="chitiet_bacsi.php" class="btn btn-outline-primary">Xem chi tiết</a>
                            <a href="datlich.php" class="btn btn-primary">Đặt lịch khám</a>
                            </div>
                        </div>
                    </div>
                </div>
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