<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết bác sĩ - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/doctors.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .doctor-profile {
            padding: 40px 0;
        }
        .doctor-image {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .doctor-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .doctor-description {
            margin-top: 20px;
            line-height: 1.6;
        }
        .booking-button {
            margin-top: 30px;
        }
        .qualification-item {
            margin-bottom: 15px;
            padding-left: 20px;
            border-left: 3px solid #0d6efd;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <div class="container doctor-profile">
        <div class="row">
            <!-- Ảnh và thông tin cơ bản -->
            <div class="col-md-4">
                <img src="assets/img/bsi_rang.jpg" alt="Bác sĩ Nguyễn Văn A" class="img-fluid doctor-image">
                <div class="doctor-info mt-4">
                    <h4 class="text-primary mb-3">Thông tin liên hệ</h4>
                    <p><i class="fas fa-envelope me-2"></i> doctor@example.com</p>
                    <p><i class="fas fa-phone-alt me-2"></i> (084) 123 456 789</p>
                    <p><i class="fas fa-hospital me-2"></i> Phòng khám số 301</p>
                </div>
            </div>

            <!-- Thông tin chi tiết -->
            <div class="col-md-8">
                <h2 class="mb-2">PGS.TS Nguyễn Văn A</h2>
                <p class="text-primary mb-4">
                    <i class="fas fa-stethoscope me-2"></i>
                    Chuyên khoa Tim mạch
                </p>

                <div class="qualification-item">
                    <h5>Chức danh</h5>
                    <p>Phó Giáo sư, Tiến sĩ Y khoa</p>
                </div>

                <div class="qualification-item">
                    <h5>Chuyên môn</h5>
                    <ul>
                        <li>Điều trị các bệnh lý tim mạch</li>
                        <li>Can thiệp tim mạch</li>
                        <li>Siêu âm tim</li>
                        <li>Điện tâm đồ</li>
                    </ul>
                </div>

                <div class="qualification-item">
                    <h5>Kinh nghiệm</h5>
                    <p>Hơn 20 năm kinh nghiệm trong lĩnh vực Tim mạch</p>
                </div>

                <div class="doctor-description">
                    <h5>Giới thiệu</h5>
                    <p>
                        PGS.TS Nguyễn Văn A là một trong những chuyên gia hàng đầu trong lĩnh vực Tim mạch tại Việt Nam. 
                        Với hơn 20 năm kinh nghiệm, bác sĩ đã điều trị thành công cho hàng nghìn bệnh nhân mắc các 
                        bệnh lý về tim mạch. Bác sĩ còn là người tiên phong trong việc áp dụng các phương pháp 
                        điều trị tim mạch tiên tiến tại Việt Nam.
                    </p>
                </div>

                <div class="qualification-item">
                    <h5>Quá trình đào tạo</h5>
                    <ul>
                        <li>Tốt nghiệp Đại học Y Hà Nội</li>
                        <li>Tiến sĩ Y khoa tại Đại học Paris, Pháp</li>
                        <li>Phó Giáo sư chuyên ngành Tim mạch</li>
                    </ul>
                </div>

                <div class="booking-button">
                    <a href="datlich.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Đặt lịch khám
                    </a>
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