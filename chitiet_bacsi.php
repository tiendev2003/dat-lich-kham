<?php 
// Start by including header.php which contains session_start()
include 'includes/header.php'; 
?>
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
        .doctor-image-container {
            position: relative;
            margin-bottom: 20px;
        }
        .doctor-image {
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            width: 100%;
            object-fit: cover;
        }
        .doctor-badge {
            position: absolute;
            bottom: -15px;
            right: 20px;
            background-color: #0d6efd;
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .doctor-info {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            border: 1px solid #f0f0f0;
        }
        .doctor-description {
            margin-top: 25px;
            line-height: 1.8;
            color: #555;
        }
        .booking-button {
            margin-top: 30px;
        }
        .qualification-item {
            margin-bottom: 25px;
            padding-left: 20px;
            position: relative;
        }
        .qualification-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            height: calc(100% - 8px);
            width: 3px;
            background-color: #0d6efd;
            border-radius: 5px;
        }
        .contact-info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .contact-info-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(13, 110, 253, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            margin-right: 15px;
        }
        .contact-info-text {
            font-size: 16px;
        }
        .doctor-stats {
            display: flex;
            justify-content: space-between;
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 25px 0;
        }
        .doctor-stat-item {
            text-align: center;
        }
        .doctor-stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #0d6efd;
        }
        .doctor-stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        .tab-content {
            padding: 25px;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-top: 0;
            border-radius: 0 0 10px 10px;
        }
        .schedule-day {
            margin-bottom: 20px;
        }
        .day-title {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .time-slot {
            display: inline-block;
            padding: 5px 15px;
            margin: 5px;
            border-radius: 20px;
            background-color: #e9ecef;
            cursor: pointer;
            transition: all 0.3s;
        }
        .time-slot.available {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .time-slot.available:hover {
            background-color: #0f5132;
            color: white;
        }
        .time-slot.booked {
            background-color: #f8d7da;
            color: #721c24;
            cursor: not-allowed;
        }
        .review-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .review-user {
            display: flex;
            align-items: center;
        }
        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }
        .user-info {
            display: flex;
            flex-direction: column;
        }
        .user-name {
            font-weight: 600;
        }
        .review-date {
            color: #6c757d;
            font-size: 14px;
        }
        .rating {
            color: #ffc107;
        }
        .article-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .article-card:hover {
            transform: translateY(-5px);
        }
        .article-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .article-content {
            padding: 20px;
        }
        .article-title {
            margin-bottom: 10px;
            font-weight: 600;
        }
        .article-date {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .services-list {
            list-style: none;
            padding: 0;
        }
        .service-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .service-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(13, 110, 253, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0d6efd;
            margin-right: 15px;
        }
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .write-review-btn {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <!-- Main Content -->
    <div class="container doctor-profile">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="bacsi.php">Bác sĩ</a></li>
                <li class="breadcrumb-item active" aria-current="page">PGS.TS Nguyễn Văn A</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Cột trái: Ảnh và thông tin liên hệ -->
            <div class="col-lg-4">
                <div class="doctor-image-container">
                    <img src="assets/img/bsi_rang.jpg" alt="PGS.TS Nguyễn Văn A" class="img-fluid doctor-image">
                    <div class="doctor-badge">Chuyên Gia Hàng Đầu</div>
                </div>
                
                <div class="doctor-info">
                    <h4 class="text-primary mb-4">Thông tin liên hệ</h4>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-info-text">doctor@example.com</div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-info-text">(084) 123 456 789</div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <div class="contact-info-text">Phòng khám số 301</div>
                    </div>

                    <div class="contact-info-item">
                        <div class="contact-info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-info-text">67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                    </div>
                    
                    <hr>
                    
                    <div class="doctor-stats">
                        <div class="doctor-stat-item">
                            <div class="doctor-stat-number">20+</div>
                            <div class="doctor-stat-label">Năm kinh nghiệm</div>
                        </div>
                        
                        <div class="doctor-stat-item">
                            <div class="doctor-stat-number">1000+</div>
                            <div class="doctor-stat-label">Bệnh nhân</div>
                        </div>
                        
                        <div class="doctor-stat-item">
                            <div class="doctor-stat-number">4.9</div>
                            <div class="doctor-stat-label">Đánh giá</div>
                        </div>
                    </div>
                    
                    <div class="booking-button">
                        <a href="datlich.php?doctor_id=1" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Đặt lịch khám
                        </a>
                    </div>
                </div>
                
                <!-- Chuyên khoa -->
                <div class="doctor-info mt-4">
                    <h4 class="text-primary mb-3">Chuyên khoa</h4>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-light text-dark p-2"><i class="fas fa-heartbeat me-1"></i> Tim mạch</span>
                        <span class="badge bg-light text-dark p-2"><i class="fas fa-lungs me-1"></i> Hô hấp</span>
                        <span class="badge bg-light text-dark p-2"><i class="fas fa-stethoscope me-1"></i> Nội khoa</span>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Thông tin chi tiết -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="mb-2">PGS.TS Nguyễn Văn A</h2>
                        <p class="text-primary mb-4">
                            <i class="fas fa-stethoscope me-2"></i>
                            Chuyên khoa Tim mạch - Nội khoa
                        </p>

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="doctorTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">
                                    <i class="fas fa-user-md me-1"></i> Thông tin
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="false">
                                    <i class="fas fa-calendar-alt me-1"></i> Lịch khám
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                                    <i class="fas fa-star me-1"></i> Đánh giá
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="articles-tab" data-bs-toggle="tab" data-bs-target="#articles" type="button" role="tab" aria-controls="articles" aria-selected="false">
                                    <i class="fas fa-file-medical-alt me-1"></i> Bài viết
                                </button>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content" id="doctorTabContent">
                            <!-- Tab Thông tin -->
                            <div class="tab-pane fade show active" id="info" role="tabpanel" aria-labelledby="info-tab">
                                <div class="doctor-description mb-4">
                                    <h5 class="mb-3">Giới thiệu</h5>
                                    <p>
                                        PGS.TS Nguyễn Văn A là một trong những chuyên gia hàng đầu trong lĩnh vực Tim mạch tại Việt Nam. 
                                        Với hơn 20 năm kinh nghiệm, bác sĩ đã điều trị thành công cho hàng nghìn bệnh nhân mắc các 
                                        bệnh lý về tim mạch. Bác sĩ còn là người tiên phong trong việc áp dụng các phương pháp 
                                        điều trị tim mạch tiên tiến tại Việt Nam.
                                    </p>
                                    <p>
                                        PGS.TS Nguyễn Văn A hiện đang công tác tại Bệnh viện Đa khoa Lộc Bình và là Giảng viên cao cấp
                                        tại Đại học Y Hà Nội. Bác sĩ cũng thường xuyên tham gia các hội nghị quốc tế về Tim mạch và
                                        là tác giả của nhiều công trình nghiên cứu khoa học được đăng tải trên các tạp chí y khoa uy tín
                                        trong và ngoài nước.
                                    </p>
                                </div>

                                <div class="qualification-item">
                                    <h5 class="mb-3">Chức danh</h5>
                                    <p>Phó Giáo sư, Tiến sĩ Y khoa</p>
                                    <p>Giảng viên cao cấp Đại học Y Hà Nội</p>
                                    <p>Chủ tịch Hội Tim mạch khu vực Đông Bắc</p>
                                </div>

                                <div class="qualification-item">
                                    <h5 class="mb-3">Chuyên môn</h5>
                                    <ul>
                                        <li>Điều trị các bệnh lý tim mạch</li>
                                        <li>Can thiệp tim mạch</li>
                                        <li>Siêu âm tim</li>
                                        <li>Điện tâm đồ</li>
                                        <li>Theo dõi và quản lý bệnh lý tim mạch mạn tính</li>
                                        <li>Tư vấn phòng ngừa bệnh tim mạch</li>
                                    </ul>
                                </div>

                                <div class="qualification-item">
                                    <h5 class="mb-3">Quá trình đào tạo</h5>
                                    <ul>
                                        <li>2000: Tốt nghiệp Bác sĩ Đa khoa, Đại học Y Hà Nội</li>
                                        <li>2005: Thạc sĩ Y khoa tại Đại học Y Hà Nội</li>
                                        <li>2010: Tiến sĩ Y khoa chuyên ngành Tim mạch tại Đại học Paris, Pháp</li>
                                        <li>2012: Chứng chỉ Can thiệp Tim mạch tại Viện Tim Massachusetts, Hoa Kỳ</li>
                                        <li>2018: Phó Giáo sư chuyên ngành Tim mạch</li>
                                    </ul>
                                </div>

                                <div class="qualification-item">
                                    <h5 class="mb-3">Kinh nghiệm làm việc</h5>
                                    <ul>
                                        <li>2000-2008: Bác sĩ Tim mạch tại Bệnh viện Bạch Mai</li>
                                        <li>2008-2015: Trưởng khoa Tim mạch, Bệnh viện Đa khoa Tỉnh Lạng Sơn</li>
                                        <li>2015-nay: Trưởng khoa Tim mạch, Bệnh viện Đa khoa Lộc Bình</li>
                                        <li>2012-nay: Giảng viên Đại học Y Hà Nội</li>
                                    </ul>
                                </div>

                                <div class="qualification-item">
                                    <h5 class="mb-3">Thành tích và giải thưởng</h5>
                                    <ul>
                                        <li>2015: Thầy thuốc Ưu tú</li>
                                        <li>2018: Giải thưởng Nghiên cứu Y khoa xuất sắc</li>
                                        <li>2020: Huân chương Lao động hạng Ba</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Tab Lịch khám -->
                            <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                                <h5 class="mb-4">Lịch làm việc trong tuần</h5>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Nhấp vào thời gian còn trống để đặt lịch khám với bác sĩ
                                </div>
                                
                                <!-- Lịch theo ngày -->
                                <div class="schedule-day">
                                    <div class="day-title">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ hai, 24/04/2025
                                    </div>
                                    <div class="time-slots">
                                        <span class="time-slot booked">08:00</span>
                                        <span class="time-slot booked">09:00</span>
                                        <span class="time-slot available">10:00</span>
                                        <span class="time-slot available">11:00</span>
                                        <span class="time-slot booked">14:00</span>
                                        <span class="time-slot available">15:00</span>
                                        <span class="time-slot available">16:00</span>
                                    </div>
                                </div>
                                
                                <div class="schedule-day">
                                    <div class="day-title">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ ba, 25/04/2025
                                    </div>
                                    <div class="time-slots">
                                        <span class="time-slot available">08:00</span>
                                        <span class="time-slot booked">09:00</span>
                                        <span class="time-slot booked">10:00</span>
                                        <span class="time-slot available">11:00</span>
                                        <span class="time-slot available">14:00</span>
                                        <span class="time-slot available">15:00</span>
                                        <span class="time-slot booked">16:00</span>
                                    </div>
                                </div>
                                
                                <div class="schedule-day">
                                    <div class="day-title">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ tư, 26/04/2025
                                    </div>
                                    <div class="time-slots">
                                        <span class="time-slot booked">08:00</span>
                                        <span class="time-slot available">09:00</span>
                                        <span class="time-slot available">10:00</span>
                                        <span class="time-slot booked">11:00</span>
                                        <span class="time-slot booked">14:00</span>
                                        <span class="time-slot available">15:00</span>
                                        <span class="time-slot available">16:00</span>
                                    </div>
                                </div>
                                
                                <div class="schedule-day">
                                    <div class="day-title">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ năm, 27/04/2025
                                    </div>
                                    <div class="time-slots">
                                        <span class="time-slot available">08:00</span>
                                        <span class="time-slot available">09:00</span>
                                        <span class="time-slot booked">10:00</span>
                                        <span class="time-slot booked">11:00</span>
                                        <span class="time-slot available">14:00</span>
                                        <span class="time-slot available">15:00</span>
                                        <span class="time-slot booked">16:00</span>
                                    </div>
                                </div>
                                
                                <div class="schedule-day">
                                    <div class="day-title">
                                        <i class="far fa-calendar-alt me-2"></i> Thứ sáu, 28/04/2025
                                    </div>
                                    <div class="time-slots">
                                        <span class="time-slot booked">08:00</span>
                                        <span class="time-slot available">09:00</span>
                                        <span class="time-slot available">10:00</span>
                                        <span class="time-slot available">11:00</span>
                                        <span class="time-slot booked">14:00</span>
                                        <span class="time-slot booked">15:00</span>
                                        <span class="time-slot available">16:00</span>
                                    </div>
                                </div>
                                
                                <div class="alert alert-light mt-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="time-slot available me-2">00:00</span>
                                        <span>Còn trống</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="time-slot booked me-2">00:00</span>
                                        <span>Đã đặt</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tab Đánh giá -->
                            <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0">Đánh giá từ bệnh nhân</h5>
                                    <button class="btn btn-outline-primary write-review-btn" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                        <i class="fas fa-edit me-1"></i> Viết đánh giá
                                    </button>
                                </div>
                                
                                <div class="review-summary mb-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-center">
                                            <h1 class="display-1 fw-bold text-primary">4.9</h1>
                                            <div class="rating mb-2">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                            <p>Dựa trên 128 đánh giá</p>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">5</span>
                                                <div class="progress flex-grow-1" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%"></div>
                                                </div>
                                                <span class="ms-2">85%</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">4</span>
                                                <div class="progress flex-grow-1" style="height: 10px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 10%"></div>
                                                </div>
                                                <span class="ms-2">10%</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">3</span>
                                                <div class="progress flex-grow-1" style="height: 10px;">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 3%"></div>
                                                </div>
                                                <span class="ms-2">3%</span>
                                            </div>
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="me-2">2</span>
                                                <div class="progress flex-grow-1" style="height: 10px;">
                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 1%"></div>
                                                </div>
                                                <span class="ms-2">1%</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">1</span>
                                                <div class="progress flex-grow-1" style="height: 10px;">
                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 1%"></div>
                                                </div>
                                                <span class="ms-2">1%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <!-- Danh sách đánh giá -->
                                <div class="review-list">
                                    <!-- Đánh giá 1 -->
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-user">
                                                <img src="https://via.placeholder.com/50" alt="User" class="user-avatar">
                                                <div class="user-info">
                                                    <div class="user-name">Nguyễn Văn B</div>
                                                    <div class="review-date">15/03/2025</div>
                                                </div>
                                            </div>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                            </div>
                                        </div>
                                        <p class="review-text">
                                            Bác sĩ Nguyễn Văn A là một bác sĩ tuyệt vời. Tôi đã được bác sĩ khám và điều trị 
                                            bệnh tim mạch. Bác sĩ rất tận tình, giải thích rõ ràng về tình trạng bệnh và 
                                            phương pháp điều trị. Sau 3 tháng điều trị, sức khỏe của tôi đã cải thiện rõ rệt.
                                        </p>
                                    </div>
                                    
                                    <!-- Đánh giá 2 -->
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-user">
                                                <img src="https://via.placeholder.com/50" alt="User" class="user-avatar">
                                                <div class="user-info">
                                                    <div class="user-name">Trần Thị C</div>
                                                    <div class="review-date">10/03/2025</div>
                                                </div>
                                            </div>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                            </div>
                                        </div>
                                        <p class="review-text">
                                            Tôi rất hài lòng với dịch vụ khám chữa bệnh của bác sĩ Nguyễn Văn A. Bác sĩ rất 
                                            chuyên nghiệp và tận tâm. Tôi cảm thấy an tâm khi được bác sĩ điều trị.
                                        </p>
                                    </div>
                                    
                                    <!-- Đánh giá 3 -->
                                    <div class="review-item">
                                        <div class="review-header">
                                            <div class="review-user">
                                                <img src="https://via.placeholder.com/50" alt="User" class="user-avatar">
                                                <div class="user-info">
                                                    <div class="user-name">Lê Văn D</div>
                                                    <div class="review-date">05/03/2025</div>
                                                </div>
                                            </div>
                                            <div class="rating">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-half-alt"></i>
                                            </div>
                                        </div>
                                        <p class="review-text">
                                            Bác sĩ Nguyễn Văn A đã điều trị cho tôi bệnh cao huyết áp trong suốt 2 năm qua. 
                                            Nhờ sự tận tâm và chuyên môn cao của bác sĩ, hiện tại huyết áp của tôi đã ổn định. 
                                            Bác sĩ luôn lắng nghe và giải đáp mọi thắc mắc của tôi một cách kiên nhẫn.
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Phân trang -->
                                <nav aria-label="Page navigation" class="pagination-container">
                                    <ul class="pagination">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            
                            <!-- Tab Bài viết -->
                            <div class="tab-pane fade" id="articles" role="tabpanel" aria-labelledby="articles-tab">
                                <h5 class="mb-4">Bài viết của bác sĩ</h5>
                                
                                <div class="row">
                                    <!-- Bài viết 1 -->
                                    <div class="col-md-6">
                                        <div class="article-card">
                                            <img src="assets/img/blog_hohap.jpg" alt="Bài viết" class="article-image">
                                            <div class="article-content">
                                                <h5 class="article-title">Phòng ngừa bệnh tim mạch ở người cao tuổi</h5>
                                                <div class="article-date"><i class="far fa-calendar-alt me-1"></i> 15/03/2025</div>
                                                <p>
                                                    Bệnh tim mạch là một trong những nguyên nhân gây tử vong hàng đầu ở người cao tuổi. 
                                                    Bài viết này chia sẻ các biện pháp phòng ngừa hiệu quả...
                                                </p>
                                                <a href="#" class="btn btn-sm btn-outline-primary">Đọc tiếp</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bài viết 2 -->
                                    <div class="col-md-6">
                                        <div class="article-card">
                                            <img src="assets/img/tintuc_timmach.jpg" alt="Bài viết" class="article-image">
                                            <div class="article-content">
                                                <h5 class="article-title">Chế độ dinh dưỡng cho người bị tăng huyết áp</h5>
                                                <div class="article-date"><i class="far fa-calendar-alt me-1"></i> 10/03/2025</div>
                                                <p>
                                                    Tăng huyết áp là bệnh lý phổ biến và nguy hiểm. Chế độ ăn uống đóng vai trò quan trọng
                                                    trong việc kiểm soát bệnh...
                                                </p>
                                                <a href="#" class="btn btn-sm btn-outline-primary">Đọc tiếp</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bài viết 3 -->
                                    <div class="col-md-6 mt-4">
                                        <div class="article-card">
                                            <img src="assets/img/blog-1.png" alt="Bài viết" class="article-image">
                                            <div class="article-content">
                                                <h5 class="article-title">Các dấu hiệu cảnh báo sớm bệnh tim mạch</h5>
                                                <div class="article-date"><i class="far fa-calendar-alt me-1"></i> 05/03/2025</div>
                                                <p>
                                                    Nhận biết sớm các dấu hiệu của bệnh tim mạch giúp ngăn ngừa các biến chứng nghiêm trọng.
                                                    Hãy cùng tìm hiểu trong bài viết này...
                                                </p>
                                                <a href="#" class="btn btn-sm btn-outline-primary">Đọc tiếp</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Bài viết 4 -->
                                    <div class="col-md-6 mt-4">
                                        <div class="article-card">
                                            <img src="assets/img/blog-2.png" alt="Bài viết" class="article-image">
                                            <div class="article-content">
                                                <h5 class="article-title">Tầm quan trọng của hoạt động thể chất với sức khỏe tim mạch</h5>
                                                <div class="article-date"><i class="far fa-calendar-alt me-1"></i> 01/03/2025</div>
                                                <p>
                                                    Luyện tập thể dục thường xuyên giúp cải thiện sức khỏe tim mạch như thế nào?
                                                    Cùng tìm hiểu các bài tập phù hợp...
                                                </p>
                                                <a href="#" class="btn btn-sm btn-outline-primary">Đọc tiếp</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Phân trang -->
                                <nav aria-label="Page navigation" class="pagination-container">
                                    <ul class="pagination">
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item">
                                            <a class="page-link" href="#" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Đánh giá -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Đánh giá bác sĩ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold">Đánh giá chung</label>
                            <div class="rating-stars fs-2">
                                <i class="far fa-star star-rating" data-rating="1"></i>
                                <i class="far fa-star star-rating" data-rating="2"></i>
                                <i class="far fa-star star-rating" data-rating="3"></i>
                                <i class="far fa-star star-rating" data-rating="4"></i>
                                <i class="far fa-star star-rating" data-rating="5"></i>
                            </div>
                            <input type="hidden" id="ratingValue" name="rating" value="0">
                        </div>

                        <div class="mb-3">
                            <label for="professionalRating" class="form-label">Chuyên môn</label>
                            <select class="form-select" id="professionalRating">
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="attitudeRating" class="form-label">Thái độ phục vụ</label>
                            <select class="form-select" id="attitudeRating">
                                <option value="5">Rất tốt</option>
                                <option value="4">Tốt</option>
                                <option value="3">Bình thường</option>
                                <option value="2">Không hài lòng</option>
                                <option value="1">Rất không hài lòng</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reviewComment" class="form-label">Nhận xét của bạn</label>
                            <textarea class="form-control" id="reviewComment" rows="3" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Gửi đánh giá</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý đánh giá sao
            const stars = document.querySelectorAll('.star-rating');
            const ratingInput = document.getElementById('ratingValue');
            
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = this.dataset.rating;
                    highlightStars(rating);
                });
                
                star.addEventListener('mouseout', function() {
                    const currentRating = ratingInput.value;
                    highlightStars(currentRating);
                });
                
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;
                    highlightStars(rating);
                });
            });
            
            function highlightStars(rating) {
                stars.forEach(star => {
                    if (star.dataset.rating <= rating) {
                        star.classList.remove('far');
                        star.classList.add('fas');
                    } else {
                        star.classList.remove('fas');
                        star.classList.add('far');
                    }
                });
            }

            // Xử lý các slot thời gian
            const availableSlots = document.querySelectorAll('.time-slot.available');
            availableSlots.forEach(slot => {
                slot.addEventListener('click', function() {
                    const time = this.textContent;
                    const day = this.closest('.schedule-day').querySelector('.day-title').textContent.trim();
                    
                    // Redirect to booking page with doctor and time info
                    window.location.href = `datlich.php?doctor_id=1&day=${encodeURIComponent(day)}&time=${encodeURIComponent(time)}`;
                });
            });

            // Xử lý tab navigation từ URL
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            
            if (tab) {
                const triggerEl = document.querySelector(`#${tab}-tab`);
                if (triggerEl) {
                    const tabTrigger = new bootstrap.Tab(triggerEl);
                    tabTrigger.show();
                }
            }
        });
    </script>
</body>
</html>