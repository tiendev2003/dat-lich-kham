<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Lấy thông tin bác sĩ đang đăng nhập
$user = get_logged_in_user();
$doctor_id = null;
$success_message = '';
$error_message = '';

// Nếu có thông báo từ session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Lấy thông tin chi tiết của bác sĩ
$stmt = $conn->prepare("SELECT b.*, ck.ten_chuyenkhoa FROM bacsi b 
                        LEFT JOIN chuyenkhoa ck ON b.chuyenkhoa_id = ck.id 
                        WHERE b.nguoidung_id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $doctor_id = $doctor['id'];
}

// Lấy danh sách chuyên khoa
$stmt = $conn->prepare("SELECT id, ten_chuyenkhoa FROM chuyenkhoa ORDER BY ten_chuyenkhoa");
$stmt->execute();
$specialties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Xử lý cập nhật thông tin cá nhân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = $_POST['action'];
    
    // Cập nhật thông tin cá nhân
    if ($post_action === 'update_profile') {
        $ho_ten = isset($_POST['ho_ten']) ? trim($_POST['ho_ten']) : '';
        $dien_thoai = isset($_POST['dien_thoai']) ? trim($_POST['dien_thoai']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $nam_sinh = isset($_POST['nam_sinh']) && !empty($_POST['nam_sinh']) ? intval($_POST['nam_sinh']) : null;
        $gioi_tinh = isset($_POST['gioi_tinh']) ? trim($_POST['gioi_tinh']) : 'Nam';
        $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
        $bang_cap = isset($_POST['bang_cap']) ? trim($_POST['bang_cap']) : '';
        $kinh_nghiem = isset($_POST['kinh_nghiem']) ? trim($_POST['kinh_nghiem']) : '';
        $mo_ta = isset($_POST['mo_ta']) ? trim($_POST['mo_ta']) : '';
        $chuyenkhoa_id = isset($_POST['chuyenkhoa_id']) && !empty($_POST['chuyenkhoa_id']) ? intval($_POST['chuyenkhoa_id']) : null;
        
        // Validate dữ liệu đầu vào
        if (empty($ho_ten)) {
            $error_message = "Vui lòng nhập họ tên";
        } elseif (empty($dien_thoai)) {
            $error_message = "Vui lòng nhập số điện thoại";
        } elseif (empty($email)) {
            $error_message = "Vui lòng nhập email";
        } elseif ($nam_sinh && ($nam_sinh < 1900 || $nam_sinh > date("Y"))) {
            $error_message = "Năm sinh không hợp lệ";
        } else {
            // Xử lý upload hình ảnh nếu có
            $hinh_anh = $doctor['hinh_anh']; // Giữ nguyên hình ảnh cũ nếu không upload mới
            
            if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $file_type = $_FILES['hinh_anh']['type'];
                
                if (!in_array($file_type, $allowed_types)) {
                    $error_message = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)";
                } else {
                    $file_name = $_FILES['hinh_anh']['name'];
                    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $new_file_name = 'bacsi_' . $doctor_id . '_' . time() . '.' . $file_ext;
                    $upload_dir = '../assets/img/bacsi/';
                    
                    // Tạo thư mục nếu chưa tồn tại
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $upload_path = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $upload_path)) {
                        $hinh_anh = 'assets/img/bacsi/' . $new_file_name;
                        
                        // Xóa file ảnh cũ nếu có
                        if (!empty($doctor['hinh_anh']) && file_exists('../' . $doctor['hinh_anh'])) {
                            unlink('../' . $doctor['hinh_anh']);
                        }
                    } else {
                        $error_message = "Không thể upload hình ảnh. Vui lòng thử lại!";
                    }
                }
            }
            
            if (empty($error_message)) {
                // Cập nhật thông tin bác sĩ
                // Sửa lại hoàn toàn phần xử lý parameter để tránh lỗi đếm số lượng tham số
                try {
                    // Tạo một câu query linh hoạt dựa trên tình trạng của nam_sinh và chuyenkhoa_id
                    $query = "UPDATE bacsi SET ho_ten = ?, dien_thoai = ?, email = ?, 
                            gioi_tinh = ?, dia_chi = ?, hinh_anh = ?, mo_ta = ?, bang_cap = ?, kinh_nghiem = ?";
                    
                    // Mảng chứa các tham số và loại tham số
                    $params = [$ho_ten, $dien_thoai, $email, $gioi_tinh, $dia_chi, $hinh_anh, $mo_ta, $bang_cap, $kinh_nghiem];
                    $types = "sssssssss"; // 9 chuỗi
                    
                    // Xử lý nam_sinh
                    if ($nam_sinh === null) {
                        $query .= ", nam_sinh = NULL";
                    } else {
                        $query .= ", nam_sinh = ?";
                        $params[] = $nam_sinh;
                        $types .= "i";
                    }
                    
                    // Xử lý chuyenkhoa_id
                    if ($chuyenkhoa_id === null) {
                        $query .= ", chuyenkhoa_id = NULL";
                    } else {
                        $query .= ", chuyenkhoa_id = ?";
                        $params[] = $chuyenkhoa_id;
                        $types .= "i";
                    }
                    
                    // Hoàn thành câu lệnh với WHERE clause
                    $query .= " WHERE id = ?";
                    $params[] = $doctor_id;
                    $types .= "i";
                    
                    // Chuẩn bị và thực thi truy vấn
                    $stmt = $conn->prepare($query);
                    
                    // Áp dụng bind_param động sử dụng mảng tham số
                    $stmt->bind_param($types, ...$params);
                    
                    if ($stmt->execute()) {
                        $success_message = "Cập nhật thông tin cá nhân thành công!";
                        
                        // Cập nhật lại thông tin bác sĩ sau khi cập nhật
                        $stmt = $conn->prepare("SELECT b.*, ck.ten_chuyenkhoa FROM bacsi b 
                                               LEFT JOIN chuyenkhoa ck ON b.chuyenkhoa_id = ck.id 
                                               WHERE b.id = ?");
                        $stmt->bind_param('i', $doctor_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $doctor = $result->fetch_assoc();
                        }
                    } else {
                        $error_message = "Lỗi khi cập nhật thông tin: " . $conn->error;
                    }
                } catch (Exception $e) {
                    $error_message = "Lỗi ngoại lệ: " . $e->getMessage();
                }
            }
        }
    }
    // Đổi mật khẩu
    elseif ($post_action === 'change_password') {
        $old_password = isset($_POST['old_password']) ? $_POST['old_password'] : '';
        $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validate dữ liệu đầu vào
        if (empty($old_password)) {
            $error_message = "Vui lòng nhập mật khẩu hiện tại";
        } elseif (empty($new_password)) {
            $error_message = "Vui lòng nhập mật khẩu mới";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "Mật khẩu xác nhận không khớp với mật khẩu mới";
        } elseif (strlen($new_password) < 6) {
            $error_message = "Mật khẩu mới phải có ít nhất 6 ký tự";
        } else {
            // Kiểm tra mật khẩu cũ
            $stmt = $conn->prepare("SELECT mat_khau FROM nguoidung WHERE id = ?");
            $stmt->bind_param('i', $user['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            
            if (password_verify($old_password, $user_data['mat_khau'])) {
                // Mật khẩu cũ đúng, tiến hành cập nhật mật khẩu mới
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE nguoidung SET mat_khau = ? WHERE id = ?");
                $stmt->bind_param('si', $hashed_password, $user['id']);
                
                if ($stmt->execute()) {
                    $success_message = "Đổi mật khẩu thành công!";
                } else {
                    $error_message = "Lỗi khi đổi mật khẩu: " . $conn->error;
                }
            } else {
                $error_message = "Mật khẩu hiện tại không đúng";
            }
        }
    }
}

 ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Bác sĩ Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .profile-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .profile-heading {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 20px;
            border: 3px solid #0d6efd;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-stats {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        .profile-stat-item {
            display: flex;
            align-items: center;
        }
        .profile-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 1.2rem;
            color: white;
        }
        .profile-detail-card {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }
        .profile-detail-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
        }
        .profile-detail-body {
            padding: 20px;
        }
        .profile-detail-item {
            margin-bottom: 15px;
        }
        .profile-detail-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .profile-detail-value {
            font-size: 16px;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        .tab-content {
            margin-top: 20px;
        }
        @media (max-width: 767.98px) {
            .profile-heading {
                flex-direction: column;
                text-align: center;
            }
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }
            .profile-stats {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col main-content p-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Thông tin cá nhân</h1>
                </div>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Đang tải...</span>
                    </div>
                    <p class="mt-2">Đang tải thông tin...</p>
                </div>

                <!-- Profile Overview -->
                <div id="profileContent" class="d-none">
                    <div class="profile-card">
                        <div class="profile-heading">
                            <div class="profile-avatar">
                                <img id="doctorAvatar" src="../assets/img/doctor-default.jpg" alt="Doctor Avatar">
                            </div>
                            <div>
                                <h3 class="mb-1" id="doctorName">Đang tải...</h3>
                                <p class="text-muted mb-2" id="doctorSpecialty">Đang tải...</p>
                                <div class="profile-stats">
                                    <div class="profile-stat-item">
                                        <div class="profile-stat-icon bg-primary">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0"><strong id="appointmentsCount">0</strong></p>
                                            <p class="mb-0 small text-muted">Lịch hẹn</p>
                                        </div>
                                    </div>
                                    <div class="profile-stat-item">
                                        <div class="profile-stat-icon bg-success">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0"><strong id="patientsCount">0</strong></p>
                                            <p class="mb-0 small text-muted">Bệnh nhân</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Tabs -->
                    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">
                                <i class="fas fa-user me-1"></i> Thông tin cá nhân
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab" aria-controls="edit" aria-selected="false">
                                <i class="fas fa-edit me-1"></i> Chỉnh sửa thông tin
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                                <i class="fas fa-key me-1"></i> Đổi mật khẩu
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabsContent">
                        <!-- Details Tab -->
                        <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="profile-detail-card">
                                        <div class="profile-detail-header">
                                            <i class="fas fa-info-circle me-2"></i> Thông tin cơ bản
                                        </div>
                                        <div class="profile-detail-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="profile-detail-item">
                                                        <div class="profile-detail-label">Họ và tên</div>
                                                        <div class="profile-detail-value" id="infoName">Đang tải...</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="profile-detail-item">
                                                        <div class="profile-detail-label">Chuyên khoa</div>
                                                        <div class="profile-detail-value" id="infoSpecialty">Đang tải...</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="profile-detail-item">
                                                        <div class="profile-detail-label">Giới tính</div>
                                                        <div class="profile-detail-value" id="infoGender">Đang tải...</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="profile-detail-item">
                                                        <div class="profile-detail-label">Năm sinh</div>
                                                        <div class="profile-detail-value" id="infoBirthYear">Đang tải...</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="profile-detail-item">
                                                        <div class="profile-detail-label">Số điện thoại</div>
                                                        <div class="profile-detail-value" id="infoPhone">Đang tải...</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="profile-detail-item">
                                                        <div class="profile-detail-label">Email</div>
                                                        <div class="profile-detail-value" id="infoEmail">Đang tải...</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="profile-detail-item">
                                                        <div class="profile-detail-label">Địa chỉ</div>
                                                        <div class="profile-detail-value" id="infoAddress">Đang tải...</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="professionalInfo" class="profile-detail-card mt-4 d-none">
                                        <div class="profile-detail-header">
                                            <i class="fas fa-user-md me-2"></i> Thông tin chuyên môn
                                        </div>
                                        <div class="profile-detail-body">
                                            <div id="infoBioContainer" class="profile-detail-item d-none">
                                                <div class="profile-detail-label">Giới thiệu</div>
                                                <div class="profile-detail-value" id="infoBio">Đang tải...</div>
                                            </div>

                                            <div id="infoDegreeContainer" class="profile-detail-item d-none">
                                                <div class="profile-detail-label">Bằng cấp, chứng chỉ</div>
                                                <div class="profile-detail-value" id="infoDegree">Đang tải...</div>
                                            </div>

                                            <div id="infoExperienceContainer" class="profile-detail-item d-none">
                                                <div class="profile-detail-label">Kinh nghiệm làm việc</div>
                                                <div class="profile-detail-value" id="infoExperience">Đang tải...</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-info-circle me-2"></i> Lưu ý</h5>
                                        <p class="mb-0">Cập nhật đầy đủ thông tin cá nhân và chuyên môn giúp bệnh nhân hiểu rõ hơn về bạn khi đặt lịch khám.</p>
                                    </div>
                                    
                                    <!-- Có thể thêm các thông tin khác ở đây -->
                                </div>
                            </div>
                        </div>

                        <!-- Edit Profile Tab -->
                        <div class="tab-pane fade" id="edit" role="tabpanel" aria-labelledby="edit-tab">
                            <div class="profile-detail-card">
                                <div class="profile-detail-header">
                                    <i class="fas fa-user-edit me-2"></i> Cập nhật thông tin cá nhân
                                </div>
                                <div class="profile-detail-body">
                                    <form action="" method="POST" enctype="multipart/form-data" id="updateProfileForm">
                                        <input type="hidden" name="action" value="update_profile">
                                        
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="avatar" class="form-label d-block">Hình đại diện</label>
                                                <div class="d-flex align-items-center">
                                                    <img id="previewAvatar" src="../assets/img/doctor-default.jpg" 
                                                         alt="Avatar" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                                    <div class="flex-grow-1">
                                                        <input type="file" class="form-control" id="hinh_anh" name="hinh_anh" accept="image/*">
                                                        <small class="text-muted">Chấp nhận file: JPG, PNG, GIF, WEBP. Tối đa 2MB.</small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label for="ho_ten" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="chuyenkhoa_id" class="form-label">Chuyên khoa</label>
                                                <select class="form-select" id="chuyenkhoa_id" name="chuyenkhoa_id">
                                                    <option value="">-- Chọn chuyên khoa --</option>
                                                    <?php foreach ($specialties as $specialty): ?>
                                                    <option value="<?php echo $specialty['id']; ?>">
                                                        <?php echo $specialty['ten_chuyenkhoa']; ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="dien_thoai" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                                <input type="tel" class="form-control" id="dien_thoai" name="dien_thoai" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="nam_sinh" class="form-label">Năm sinh</label>
                                                <input type="number" class="form-control" id="nam_sinh" name="nam_sinh" min="1900" max="<?php echo date('Y'); ?>">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="gioi_tinh" class="form-label">Giới tính</label>
                                                <select class="form-select" id="gioi_tinh" name="gioi_tinh">
                                                    <option value="Nam">Nam</option>
                                                    <option value="Nữ">Nữ</option>
                                                    <option value="Khác">Khác</option>
                                                </select>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="dia_chi" class="form-label">Địa chỉ</label>
                                                <input type="text" class="form-control" id="dia_chi" name="dia_chi">
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="mo_ta" class="form-label">Giới thiệu bản thân</label>
                                                <textarea class="form-control summernote" id="mo_ta" name="mo_ta" rows="3"></textarea>
                                                <small class="text-muted">Mô tả ngắn gọn về bản thân, quá trình học tập và công tác.</small>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="bang_cap" class="form-label">Bằng cấp, chứng chỉ</label>
                                                <textarea class="form-control summernote" id="bang_cap" name="bang_cap" rows="3"></textarea>
                                                <small class="text-muted">Liệt kê các bằng cấp, chứng chỉ quan trọng.</small>
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label for="kinh_nghiem" class="form-label">Kinh nghiệm làm việc</label>
                                                <textarea class="form-control summernote" id="kinh_nghiem" name="kinh_nghiem" rows="3"></textarea>
                                                <small class="text-muted">Liệt kê kinh nghiệm làm việc.</small>
                                            </div>

                                            <div class="col-12 text-end">
                                                <button type="reset" class="btn btn-secondary me-2">Hủy</button>
                                                <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                            <div class="profile-detail-card">
                                <div class="profile-detail-header">
                                    <i class="fas fa-key me-2"></i> Đổi mật khẩu
                                </div>
                                <div class="profile-detail-body">
                                    <form action="" method="POST">
                                        <input type="hidden" name="action" value="change_password">
                                        
                                        <div class="mb-3">
                                            <label for="old_password" class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i> Mật khẩu phải có ít nhất 6 ký tự. Nên sử dụng kết hợp chữ in hoa, chữ thường, số và ký tự đặc biệt để tăng tính bảo mật.
                                            </div>
                                        </div>
                                        
                                        <div class="text-end">
                                            <button type="reset" class="btn btn-secondary me-2">Hủy</button>
                                            <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote JS dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if(sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }
            
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }

            // Initialize Summernote editor
            $('.summernote').summernote({
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            // Activate tab based on URL hash if present
            const hash = window.location.hash;
            if (hash) {
                const tab = document.querySelector(`#profileTabs a[href="${hash}"]`);
                if (tab) {
                    const bsTab = new bootstrap.Tab(tab);
                    bsTab.show();
                }
            }

            // Update URL when tab changes
            const tabLinks = document.querySelectorAll('#profileTabs button[data-bs-toggle="tab"]');
            tabLinks.forEach(tabLink => {
                tabLink.addEventListener('shown.bs.tab', function (event) {
                    const tabId = event.target.getAttribute('aria-controls');
                    window.location.hash = tabId;
                });
            });

            // Load doctor information
            loadDoctorInfo();
            
            // Preview image when changed
            document.getElementById('hinh_anh').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('previewAvatar').src = event.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        function loadDoctorInfo() {
            // Use jQuery for AJAX as we're already including it for Summernote
            $.ajax({
                url: 'ajax/get_doctor_info.php',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        displayDoctorInfo(data.doctor);
                    } else {
                        showError(data.message || 'Không thể tải thông tin bác sĩ');
                    }
                },
                error: function(xhr, status, error) {
                    showError('Đã xảy ra lỗi: ' + error);
                },
                complete: function() {
                    // Hide spinner and show content
                    $('#loadingSpinner').addClass('d-none');
                    $('#profileContent').removeClass('d-none');
                }
            });
        }

        function displayDoctorInfo(doctor) {
            // Update general information
            $('#doctorName').text('BS. ' + doctor.ho_ten);
            $('#doctorSpecialty').text(doctor.ten_chuyenkhoa || 'Chưa cập nhật chuyên khoa');
            $('#appointmentsCount').text(doctor.appointments_count);
            $('#patientsCount').text(doctor.patients_count);
            
            // Update avatar
            if (doctor.hinh_anh) {
                $('#doctorAvatar').attr('src', '../' + doctor.hinh_anh);
                $('#previewAvatar').attr('src', '../' + doctor.hinh_anh);
            }
            
            // Update basic information
            $('#infoName').text(doctor.ho_ten);
            $('#infoSpecialty').text(doctor.ten_chuyenkhoa || 'Chưa cập nhật');
            $('#infoGender').text(doctor.gioi_tinh);
            $('#infoBirthYear').text(doctor.nam_sinh || 'Chưa cập nhật');
            $('#infoPhone').text(doctor.dien_thoai || 'Chưa cập nhật');
            $('#infoEmail').text(doctor.email || 'Chưa cập nhật');
            $('#infoAddress').text(doctor.dia_chi || 'Chưa cập nhật');
            
            // Update professional information
            let hasProInfo = false;
            
            if (doctor.mo_ta) {
                $('#infoBioContainer').removeClass('d-none');
                $('#infoBio').html(doctor.mo_ta);
                hasProInfo = true;
            }
            
            if (doctor.bang_cap) {
                $('#infoDegreeContainer').removeClass('d-none');
                $('#infoDegree').html(doctor.bang_cap);
                hasProInfo = true;
            }
            
            if (doctor.kinh_nghiem) {
                $('#infoExperienceContainer').removeClass('d-none');
                $('#infoExperience').html(doctor.kinh_nghiem);
                hasProInfo = true;
            }
            
            if (hasProInfo) {
                $('#professionalInfo').removeClass('d-none');
            }
            
            // Fill form data
            $('#ho_ten').val(doctor.ho_ten);
            $('#dien_thoai').val(doctor.dien_thoai || '');
            $('#email').val(doctor.email || '');
            $('#nam_sinh').val(doctor.nam_sinh || '');
            $('#gioi_tinh').val(doctor.gioi_tinh || 'Nam');
            $('#dia_chi').val(doctor.dia_chi || '');
            
            // Update Summernote editors
            $('#mo_ta').summernote('code', doctor.mo_ta || '');
            $('#bang_cap').summernote('code', doctor.bang_cap || '');
            $('#kinh_nghiem').summernote('code', doctor.kinh_nghiem || '');
            
            // Set specialty dropdown
            if (doctor.chuyenkhoa_id) {
                $('#chuyenkhoa_id').val(doctor.chuyenkhoa_id);
            }
        }

        function showError(message) {
            const alertHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            $('main').prepend(alertHTML);
        }
    </script>
</body>
</html>