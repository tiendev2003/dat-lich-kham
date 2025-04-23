<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ Bác sĩ - Hệ thống đặt lịch khám bệnh</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .profile-container {
            margin-top: 20px;
            margin-bottom: 40px;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .profile-header {
            background-color: #007bff;
            color: #fff;
            padding: 30px 20px;
            position: relative;
            text-align: center;
        }
        .profile-avatar {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 15px;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid rgba(255, 255, 255, 0.5);
        }
        .avatar-edit {
            position: absolute;
            right: 5px;
            bottom: 5px;
            width: 30px;
            height: 30px;
            background-color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #007bff;
            cursor: pointer;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        }
        .doctor-name {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .doctor-specialty {
            font-size: 16px;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        .doctor-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 15px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-item .number {
            font-size: 20px;
            font-weight: bold;
        }
        .stat-item .label {
            font-size: 14px;
            opacity: 0.8;
        }
        .nav-pills .nav-link {
            color: #495057;
            font-weight: 500;
        }
        .nav-pills .nav-link.active {
            background-color: #007bff;
            color: #fff;
        }
        .tab-content {
            padding: 30px 0;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        .schedule-item {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #e9ecef;
        }
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .day-name {
            font-weight: 600;
        }
        .time-slot {
            display: flex;
            align-items: center;
            padding: 8px 15px;
            background-color: #fff;
            border-radius: 5px;
            margin-bottom: 8px;
            border: 1px solid #e9ecef;
        }
        .time-slot-toggle {
            margin-right: 10px;
        }
        .certificate-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            border: 1px solid #e9ecef;
        }
        .certificate-icon {
            font-size: 20px;
            color: #6c757d;
            margin-right: 15px;
        }
        .certificate-info {
            flex-grow: 1;
        }
        .certificate-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .certificate-date {
            font-size: 14px;
            color: #6c757d;
        }
        .settings-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .settings-item:last-child {
            border-bottom: none;
        }
        .settings-icon {
            font-size: 20px;
            color: #6c757d;
            margin-right: 15px;
        }
        .settings-info {
            flex-grow: 1;
        }
        .settings-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .settings-description {
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
              <!-- Sidebar -->
              <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 profile-container">
                <div class="card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="../assets/img/bsi_rang.jpg" alt="Doctor Avatar">
                            <div class="avatar-edit" title="Thay đổi ảnh đại diện">
                                <i class="fas fa-camera"></i>
                            </div>
                        </div>
                        <h2 class="doctor-name">BS. Nguyễn Thế Lâm</h2>
                        <div class="doctor-specialty">Răng Hàm Mặt</div>
                        <div><i class="fas fa-check-circle me-1"></i>Đã xác thực</div>
                        
                        <div class="doctor-stats">
                            <div class="stat-item">
                                <div class="number">1250</div>
                                <div class="label">Bệnh nhân</div>
                            </div>
                            <div class="stat-item">
                                <div class="number">4.8</div>
                                <div class="label">Đánh giá</div>
                            </div>
                            <div class="stat-item">
                                <div class="number">5</div>
                                <div class="label">Năm kinh nghiệm</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-info-tab" data-bs-toggle="pill" data-bs-target="#pills-info" type="button" role="tab">Thông tin cá nhân</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-schedule-tab" data-bs-toggle="pill" data-bs-target="#pills-schedule" type="button" role="tab">Lịch làm việc</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-certificates-tab" data-bs-toggle="pill" data-bs-target="#pills-certificates" type="button" role="tab">Chứng chỉ & Bằng cấp</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-settings-tab" data-bs-toggle="pill" data-bs-target="#pills-settings" type="button" role="tab">Cài đặt tài khoản</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Thông tin cá nhân -->
                            <div class="tab-pane fade show active" id="pills-info" role="tabpanel">
                                <form>
                                    <div class="form-section">
                                        <h5 class="form-section-title">Thông tin cơ bản</h5>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Họ và tên</label>
                                                <input type="text" class="form-control" value="Nguyễn Thế Lâm" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" value="nguyenthelam@example.com" readonly>
                                                <small class="form-text text-muted">Email không thể thay đổi</small>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Số điện thoại</label>
                                                <input type="tel" class="form-control" value="0123456789" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Ngày sinh</label>
                                                <input type="date" class="form-control" value="1985-03-15">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Giới tính</label>
                                                <select class="form-select">
                                                    <option value="Nam" selected>Nam</option>
                                                    <option value="Nữ">Nữ</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Chuyên khoa</label>
                                                <input type="text" class="form-control" value="Răng Hàm Mặt" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-section">
                                        <h5 class="form-section-title">Thông tin chuyên môn</h5>
                                        <div class="mb-3">
                                            <label class="form-label">Giới thiệu bản thân</label>
                                            <textarea class="form-control" rows="4">Tôi là bác sĩ chuyên khoa Răng Hàm Mặt với hơn 5 năm kinh nghiệm trong lĩnh vực nha khoa. Tôi đã tốt nghiệp Đại học Y Hà Nội và có chuyên môn sâu về các vấn đề răng miệng, từ điều trị cơ bản đến phẫu thuật nha khoa phức tạp.</textarea>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nơi công tác hiện tại</label>
                                                <input type="text" class="form-control" value="Phòng khám Lộc Bình">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Chức danh</label>
                                                <input type="text" class="form-control" value="Bác sĩ chuyên khoa 1">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Số năm kinh nghiệm</label>
                                                <input type="number" class="form-control" value="5">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Lĩnh vực chuyên sâu</label>
                                                <input type="text" class="form-control" value="Implant, chỉnh nha, phục hình răng">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary px-4">Lưu thông tin</button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Lịch làm việc -->
                            <div class="tab-pane fade" id="pills-schedule" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5>Cài đặt lịch làm việc</h5>
                                    <div>
                                        <button class="btn btn-outline-primary me-2">
                                            <i class="fas fa-copy me-1"></i> Sao chép lịch
                                        </button>
                                        <button class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Lưu lịch làm việc
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i> Thiết lập khung giờ làm việc để bệnh nhân có thể đặt lịch khám với bạn.
                                </div>
                                
                                <!-- Monday Schedule -->
                                <div class="schedule-item">
                                    <div class="day-header">
                                        <div class="day-name">Thứ 2</div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="mondayEnable" checked>
                                            <label class="form-check-label" for="mondayEnable">Hoạt động</label>
                                        </div>
                                    </div>
                                    <div class="time-slots">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ bắt đầu</label>
                                                <select class="form-select">
                                                    <option value="08:00">08:00</option>
                                                    <option value="08:30">08:30</option>
                                                    <option value="09:00" selected>09:00</option>
                                                    <option value="09:30">09:30</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ kết thúc</label>
                                                <select class="form-select">
                                                    <option value="16:00">16:00</option>
                                                    <option value="16:30">16:30</option>
                                                    <option value="17:00" selected>17:00</option>
                                                    <option value="17:30">17:30</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="mondayBreak" checked>
                                            <label class="form-check-label" for="mondayBreak">Nghỉ trưa</label>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ bắt đầu nghỉ</label>
                                                <select class="form-select">
                                                    <option value="12:00" selected>12:00</option>
                                                    <option value="12:30">12:30</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ kết thúc nghỉ</label>
                                                <select class="form-select">
                                                    <option value="13:30">13:30</option>
                                                    <option value="14:00" selected>14:00</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tuesday Schedule -->
                                <div class="schedule-item">
                                    <div class="day-header">
                                        <div class="day-name">Thứ 3</div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="tuesdayEnable" checked>
                                            <label class="form-check-label" for="tuesdayEnable">Hoạt động</label>
                                        </div>
                                    </div>
                                    <div class="time-slots">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ bắt đầu</label>
                                                <select class="form-select">
                                                    <option value="08:00">08:00</option>
                                                    <option value="08:30" selected>08:30</option>
                                                    <option value="09:00">09:00</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ kết thúc</label>
                                                <select class="form-select">
                                                    <option value="16:00">16:00</option>
                                                    <option value="16:30" selected>16:30</option>
                                                    <option value="17:00">17:00</option>
                                                    <!-- More options -->
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="tuesdayBreak" checked>
                                            <label class="form-check-label" for="tuesdayBreak">Nghỉ trưa</label>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ bắt đầu nghỉ</label>
                                                <select class="form-select">
                                                    <option value="12:00" selected>12:00</option>
                                                    <option value="12:30">12:30</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Giờ kết thúc nghỉ</label>
                                                <select class="form-select">
                                                    <option value="13:30">13:30</option>
                                                    <option value="14:00" selected>14:00</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- More days would follow the same pattern -->
                                <div class="text-center mt-4">
                                    <button class="btn btn-primary px-4">
                                        <i class="fas fa-save me-1"></i> Lưu lịch làm việc
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Chứng chỉ & Bằng cấp -->
                            <div class="tab-pane fade" id="pills-certificates" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5>Chứng chỉ & Bằng cấp</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCertificateModal">
                                        <i class="fas fa-plus me-1"></i> Thêm chứng chỉ
                                    </button>
                                </div>
                                
                                <div class="certificate-list">
                                    <div class="certificate-item">
                                        <div class="certificate-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="certificate-info">
                                            <div class="certificate-title">Bác sĩ Chuyên khoa I - Răng Hàm Mặt</div>
                                            <div class="certificate-issuer">Đại học Y Hà Nội</div>
                                            <div class="certificate-date">2015 - 2018</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="certificate-item">
                                        <div class="certificate-icon">
                                            <i class="fas fa-graduation-cap"></i>
                                        </div>
                                        <div class="certificate-info">
                                            <div class="certificate-title">Bác sĩ Răng Hàm Mặt</div>
                                            <div class="certificate-issuer">Đại học Y Hà Nội</div>
                                            <div class="certificate-date">2010 - 2015</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="certificate-item">
                                        <div class="certificate-icon">
                                            <i class="fas fa-certificate"></i>
                                        </div>
                                        <div class="certificate-info">
                                            <div class="certificate-title">Chứng chỉ Implant Nha khoa</div>
                                            <div class="certificate-issuer">Hiệp hội Implant Nha khoa Việt Nam</div>
                                            <div class="certificate-date">2017</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="certificate-item">
                                        <div class="certificate-icon">
                                            <i class="fas fa-certificate"></i>
                                        </div>
                                        <div class="certificate-info">
                                            <div class="certificate-title">Chứng chỉ Chỉnh Nha</div>
                                            <div class="certificate-issuer">Trung tâm đào tạo và phát triển Nha khoa Quốc tế</div>
                                            <div class="certificate-date">2019</div>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Cài đặt tài khoản -->
                            <div class="tab-pane fade" id="pills-settings" role="tabpanel">
                                <div class="form-section">
                                    <h5 class="form-section-title">Đổi mật khẩu</h5>
                                    <form>
                                        <div class="mb-3">
                                            <label class="form-label">Mật khẩu hiện tại</label>
                                            <input type="password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Mật khẩu mới</label>
                                            <input type="password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Xác nhận mật khẩu mới</label>
                                            <input type="password" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                                    </form>
                                </div>
                                
                                <div class="form-section">
                                    <h5 class="form-section-title">Cài đặt thông báo</h5>
                                    <div class="settings-item">
                                        <div class="d-flex align-items-center">
                                            <div class="settings-icon">
                                                <i class="fas fa-bell"></i>
                                            </div>
                                            <div class="settings-info">
                                                <div class="settings-title">Thông báo lịch hẹn</div>
                                                <div class="settings-description">Nhận thông báo khi có lịch hẹn mới</div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notifyAppointment" checked>
                                        </div>
                                    </div>
                                    <div class="settings-item">
                                        <div class="d-flex align-items-center">
                                            <div class="settings-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="settings-info">
                                                <div class="settings-title">Thông báo qua email</div>
                                                <div class="settings-description">Nhận thông báo qua email</div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notifyEmail" checked>
                                        </div>
                                    </div>
                                    <div class="settings-item">
                                        <div class="d-flex align-items-center">
                                            <div class="settings-icon">
                                                <i class="fas fa-mobile-alt"></i>
                                            </div>
                                            <div class="settings-info">
                                                <div class="settings-title">Thông báo qua SMS</div>
                                                <div class="settings-description">Nhận thông báo qua tin nhắn SMS</div>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="notifySMS">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <h5 class="form-section-title">Cài đặt khác</h5>
                                    <div class="settings-item">
                                        <div class="d-flex align-items-center">
                                            <div class="settings-icon">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <div class="settings-info">
                                                <div class="settings-title">Thời gian khám trung bình</div>
                                                <div class="settings-description">Thời gian trung bình cho mỗi ca khám</div>
                                            </div>
                                        </div>
                                        <div>
                                            <select class="form-select">
                                                <option value="15">15 phút</option>
                                                <option value="30" selected>30 phút</option>
                                                <option value="45">45 phút</option>
                                                <option value="60">60 phút</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="settings-item">
                                        <div class="d-flex align-items-center">
                                            <div class="settings-icon">
                                                <i class="fas fa-language"></i>
                                            </div>
                                            <div class="settings-info">
                                                <div class="settings-title">Ngôn ngữ</div>
                                                <div class="settings-description">Ngôn ngữ hiển thị trong hệ thống</div>
                                            </div>
                                        </div>
                                        <div>
                                            <select class="form-select">
                                                <option value="vi" selected>Tiếng Việt</option>
                                                <option value="en">English</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Certificate Modal -->
    <div class="modal fade" id="addCertificateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm chứng chỉ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tên chứng chỉ/bằng cấp</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Đơn vị cấp</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Năm bắt đầu</label>
                                <input type="number" class="form-control" min="1970" max="2025" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Năm kết thúc</label>
                                <input type="number" class="form-control" min="1970" max="2025">
                                <small class="form-text text-muted">Để trống nếu không có</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả (tùy chọn)</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh chứng chỉ (tùy chọn)</label>
                            <input type="file" class="form-control" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Thêm chứng chỉ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle avatar update
            document.querySelector('.avatar-edit').addEventListener('click', function() {
                // You can implement file upload logic here
                alert('Chức năng đang được phát triển');
            });

            // Toggle break time fields based on checkbox state
            document.getElementById('mondayBreak').addEventListener('change', function() {
                // Logic to show/hide break time fields
            });

            document.getElementById('tuesdayBreak').addEventListener('change', function() {
                // Logic to show/hide break time fields
            });

            // Other initialization or event handlers can be added here
        });
    </script>
</body>
</html>