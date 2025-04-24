<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Lấy thông tin bác sĩ đang đăng nhập
$user = get_logged_in_user();
$doctor_id = null;

$stmt = $conn->prepare("SELECT id, ho_ten FROM bacsi WHERE nguoidung_id = ?");
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $doctor = $result->fetch_assoc();
    $doctor_id = $doctor['id'];
}

// Xử lý các hành động
$action = isset($_GET['action']) ? $_GET['action'] : '';
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
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

// Xử lý cập nhật trạng thái lịch hẹn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = $_POST['action'];
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    
    // Xác nhận lịch hẹn
    if ($post_action === 'confirm' && $appointment_id > 0) {
        $stmt = $conn->prepare("UPDATE lichhen SET trang_thai = 'confirmed' WHERE id = ? AND bacsi_id = ?");
        $stmt->bind_param('ii', $appointment_id, $doctor_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $success_message = "Đã xác nhận lịch hẹn thành công!";
        } else {
            $error_message = "Không thể xác nhận lịch hẹn. Vui lòng thử lại!";
        }
    } 
    // Hủy lịch hẹn
    elseif ($post_action === 'cancel' && $appointment_id > 0) {
        $ly_do = isset($_POST['ly_do']) ? trim($_POST['ly_do']) : '';
        if (empty($ly_do)) {
            $error_message = "Vui lòng nhập lý do hủy lịch hẹn!";
        } else {
            // Thêm ghi chú hủy lịch
            $stmt = $conn->prepare("UPDATE lichhen SET trang_thai = 'cancelled', ghi_chu = CONCAT(IFNULL(ghi_chu,''), '\n(', CURDATE(), ') Bác sĩ hủy: ', ?) WHERE id = ? AND bacsi_id = ?");
            $stmt->bind_param('sii', $ly_do, $appointment_id, $doctor_id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $success_message = "Đã hủy lịch hẹn thành công!";
            } else {
                $error_message = "Không thể hủy lịch hẹn. Vui lòng thử lại!";
            }
        }
    }
    // Hoàn thành lịch hẹn và thêm kết quả khám
    elseif ($post_action === 'complete' && $appointment_id > 0) {
        $chan_doan = isset($_POST['chan_doan']) ? trim($_POST['chan_doan']) : '';
        $ket_qua = isset($_POST['ket_qua']) ? trim($_POST['ket_qua']) : '';
        $don_thuoc = isset($_POST['don_thuoc']) ? trim($_POST['don_thuoc']) : '';
        $loi_dan = isset($_POST['loi_dan']) ? trim($_POST['loi_dan']) : '';
        
        if (empty($chan_doan)) {
            $_SESSION['error_message'] = "Vui lòng nhập chẩn đoán!";
        } else {
            // Bắt đầu transaction
            $conn->begin_transaction();
            
            try {
                // Cập nhật trạng thái lịch hẹn - sử dụng thoi_diem_hoanthanh thay vì ngay_kham
                $stmt = $conn->prepare("UPDATE lichhen SET trang_thai = 'completed', thoi_diem_hoanthanh = NOW() WHERE id = ? AND bacsi_id = ?");
                $stmt->bind_param('ii', $appointment_id, $doctor_id);
                $stmt->execute();
                
                if ($stmt->affected_rows === 0) {
                    throw new Exception("Không thể cập nhật trạng thái lịch hẹn");
                }
                
                // Kiểm tra xem đã có kết quả khám chưa
                $stmt = $conn->prepare("SELECT id FROM ketqua_kham WHERE lichhen_id = ?");
                $stmt->bind_param('i', $appointment_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    // Cập nhật kết quả khám hiện có
                    $stmt = $conn->prepare("UPDATE ketqua_kham SET chan_doan = ?, mo_ta = ?, don_thuoc = ?, ghi_chu = ?, ngay_capnhat = NOW() WHERE lichhen_id = ?");
                    $stmt->bind_param('ssssi', $chan_doan, $ket_qua, $don_thuoc, $loi_dan, $appointment_id);
                } else {
                    // Thêm kết quả khám mới - không có cột bacsi_id trong bảng
                    $stmt = $conn->prepare("INSERT INTO ketqua_kham (lichhen_id, chan_doan, mo_ta, don_thuoc, ghi_chu) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param('issss', $appointment_id, $chan_doan, $ket_qua, $don_thuoc, $loi_dan);
                }
                
                if (!$stmt->execute()) {
                    throw new Exception("Không thể lưu kết quả khám: " . $stmt->error);
                }
                
                // Commit transaction
                $conn->commit();
                
                $_SESSION['success_message'] = "Đã cập nhật kết quả khám bệnh thành công!";
            } catch (Exception $e) {
                // Rollback nếu có lỗi
                $conn->rollback();
                $_SESSION['error_message'] = "Đã xảy ra lỗi: " . $e->getMessage();
            }
        }
    }
    
    // Sau khi xử lý, chuyển hướng để refresh trang
    header("Location: lichhen.php" . ($action ? "?action=$action" . ($appointment_id ? "&id=$appointment_id" : "") : ""));
    exit;
}

// Thiết lập bộ lọc và phân trang
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 10;
$filter = [];

// Lọc theo trạng thái
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
if (!empty($status_filter)) {
    $filter['status'] = $status_filter;
}

// Lọc theo ngày
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
if (!empty($date_filter)) {
    $filter['date'] = $date_filter;
}

// Lọc theo tìm kiếm
$search_filter = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($search_filter)) {
    $filter['search'] = $search_filter;
}

// Lấy một lịch hẹn cụ thể nếu được yêu cầu
$appointment = null;
if ($action === 'view' && $appointment_id > 0) {
    $stmt = $conn->prepare(
        "SELECT lh.*, bn.ho_ten AS patient_name, bn.dien_thoai AS patient_phone, 
                bn.email AS patient_email, bn.nam_sinh AS patient_birth_year, bn.gioi_tinh AS patient_gender,
                bn.dia_chi AS patient_address, dv.ten_dichvu AS service_name, dv.gia_coban AS service_price,
                ck.ten_chuyenkhoa AS specialty_name
         FROM lichhen lh
         LEFT JOIN benhnhan bn ON lh.benhnhan_id = bn.id
         LEFT JOIN dichvu dv ON lh.dichvu_id = dv.id
         LEFT JOIN bacsi bs ON lh.bacsi_id = bs.id
         LEFT JOIN chuyenkhoa ck ON bs.chuyenkhoa_id = ck.id
         WHERE lh.id = ? AND lh.bacsi_id = ?"
    );
    $stmt->bind_param('ii', $appointment_id, $doctor_id);
    $stmt->execute();
    $appointment = $stmt->get_result()->fetch_assoc();
    
    // Lấy kết quả khám bệnh nếu có
    if ($appointment) {
        $stmt = $conn->prepare("SELECT * FROM ketqua_kham WHERE lichhen_id = ?");
        $stmt->bind_param('i', $appointment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $medical_result = $result->fetch_assoc();
            $appointment = array_merge($appointment, $medical_result);
        }
    }
}
// Danh sách tất cả các lịch hẹn
else {
    // Xây dựng câu truy vấn SQL với bộ lọc
    $where_clauses = ["lh.bacsi_id = ?"];
    $params = [$doctor_id];
    $types = "i";
    
    if (!empty($filter['status'])) {
        $where_clauses[] = "lh.trang_thai = ?";
        $params[] = $filter['status'];
        $types .= "s";
    }
    
    if (!empty($filter['date'])) {
        $where_clauses[] = "lh.ngay_hen = ?";
        $params[] = $filter['date'];
        $types .= "s";
    }
    
    if (!empty($filter['search'])) {
        $search_term = "%" . $filter['search'] . "%";
        $where_clauses[] = "(bn.ho_ten LIKE ? OR lh.ma_lichhen LIKE ?)";
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "ss";
    }
    
    $where_clause = implode(" AND ", $where_clauses);
    
    // Đếm tổng số lượng lịch hẹn phù hợp với bộ lọc
    $count_sql = "SELECT COUNT(*) as total 
                  FROM lichhen lh 
                  LEFT JOIN benhnhan bn ON lh.benhnhan_id = bn.id
                  WHERE $where_clause";
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $total_items = $stmt->get_result()->fetch_assoc()['total'];
    $total_pages = ceil($total_items / $items_per_page);
    
    // Điều chỉnh trang hiện tại nếu vượt quá tổng số trang
    if ($current_page > $total_pages && $total_pages > 0) {
        $current_page = $total_pages;
    }
    
    // Tính vị trí bắt đầu
    $start = ($current_page - 1) * $items_per_page;
    
    // Lấy danh sách lịch hẹn với phân trang
    $sql = "SELECT lh.*, bn.ho_ten AS patient_name, dv.ten_dichvu AS service_name
            FROM lichhen lh
            LEFT JOIN benhnhan bn ON lh.benhnhan_id = bn.id
            LEFT JOIN dichvu dv ON lh.dichvu_id = dv.id
            WHERE $where_clause
            ORDER BY 
              CASE WHEN lh.ngay_hen = CURDATE() THEN 0 ELSE 1 END,
              lh.ngay_hen ASC, 
              lh.gio_hen ASC
            LIMIT ?, ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types . "ii", ...[...$params, $start, $items_per_page]);
    $stmt->execute();
    $appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'view' ? 'Chi tiết lịch hẹn' : 'Quản lý lịch hẹn'; ?> - Bác sĩ Portal</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .search-filter {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .appointments-table {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .appointment-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .appointment-detail {
            margin-bottom: 15px;
        }
        .appointment-detail-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        .appointment-detail-value {
            font-size: 16px;
        }
        .patient-info-card {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .service-info-card {
            background-color: #f8f9fa;
            border-left: 3px solid #20c997;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .medical-form-card {
            background-color: #f8f9fa;
            border-left: 3px solid #fd7e14;
            padding: 15px;
            border-radius: 5px;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .table-action {
            width: 120px;
            text-align: center;
        }
        .detail-section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .mobile-appointment-card {
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            background-color: #fff;
        }
        @media (max-width: 767.98px) {
            .desktop-only {
                display: none;
            }
            .filter-row .col-md-3 {
                margin-bottom: 10px;
            }
            .appointment-actions {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .appointment-actions .btn {
                width: 100%;
            }
            .appointment-detail-value {
                font-size: 14px;
            }
        }
        @media (min-width: 768px) {
            .mobile-only {
                display: none;
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
                <?php if ($action === 'view' && $appointment): ?>
                    <!-- Chi tiết lịch hẹn -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
                        <h1 class="h2">Chi tiết lịch hẹn</h1>
                        <div>
                            <a href="lichhen.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
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

                    <div class="appointment-card">
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Appointment Status -->
                                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                                    <div class="mb-2 mb-md-0">
                                        <h4 class="mb-1">Mã lịch hẹn: <?php echo $appointment['ma_lichhen']; ?></h4>
                                        <div class="text-muted">
                                            Ngày tạo: <?php echo date('d/m/Y', strtotime($appointment['ngay_tao'])); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                            $status_class = "";
                                            $status_text = "";
                                            switch($appointment['trang_thai']) {
                                                case 'pending':
                                                    $status_class = "bg-warning text-dark";
                                                    $status_text = "Chờ xác nhận";
                                                    break;
                                                case 'confirmed':
                                                    $status_class = "bg-primary";
                                                    $status_text = "Đã xác nhận";
                                                    break;
                                                case 'completed':
                                                    $status_class = "bg-success";
                                                    $status_text = "Đã hoàn thành";
                                                    break;
                                                case 'cancelled':
                                                    $status_class = "bg-danger";
                                                    $status_text = "Đã hủy";
                                                    break;
                                                default:
                                                    $status_class = "bg-secondary";
                                                    $status_text = ucfirst($appointment['trang_thai']);
                                            }
                                        ?>
                                        <span class="badge <?php echo $status_class; ?> p-2"><?php echo $status_text; ?></span>
                                    </div>
                                </div>

                                <!-- Patient Info -->
                                <div class="patient-info-card mb-4">
                                    <h5 class="detail-section-title">
                                        <i class="fas fa-user-injured me-2"></i> Thông tin bệnh nhân
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Họ và tên</div>
                                            <div class="appointment-detail-value"><?php echo $appointment['patient_name']; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Giới tính / Năm sinh</div>
                                            <div class="appointment-detail-value">
                                                <?php echo $appointment['patient_gender']; ?> / 
                                                <?php echo $appointment['patient_birth_year']; ?> 
                                                (<?php echo date('Y') - $appointment['patient_birth_year']; ?> tuổi)
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Số điện thoại</div>
                                            <div class="appointment-detail-value"><?php echo $appointment['patient_phone']; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Email</div>
                                            <div class="appointment-detail-value"><?php echo $appointment['patient_email'] ?: 'Không có'; ?></div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="appointment-detail-label">Địa chỉ</div>
                                            <div class="appointment-detail-value"><?php echo $appointment['patient_address']; ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Appointment Info -->
                                <div class="service-info-card mb-4">
                                    <h5 class="detail-section-title">
                                        <i class="fas fa-calendar-alt me-2"></i> Thông tin lịch hẹn
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Ngày khám</div>
                                            <div class="appointment-detail-value">
                                                <?php echo date('d/m/Y', strtotime($appointment['ngay_hen'])); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Giờ khám</div>
                                            <div class="appointment-detail-value">
                                                <?php echo $appointment['gio_hen']; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Chuyên khoa</div>
                                            <div class="appointment-detail-value"><?php echo $appointment['specialty_name']; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Dịch vụ</div>
                                            <div class="appointment-detail-value"><?php echo $appointment['service_name']; ?></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Giá dịch vụ</div>
                                            <div class="appointment-detail-value"><?php echo number_format($appointment['service_price'], 0, ',', '.'); ?> VNĐ</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="appointment-detail-label">Thanh toán</div>
                                            <div class="appointment-detail-value">
                                                <?php if (isset($appointment['thanh_toan']) && $appointment['thanh_toan'] == 'paid'): ?>
                                                    <span class="badge bg-success">Đã thanh toán</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="appointment-detail-label">Lý do khám</div>
                                            <div class="appointment-detail-value"><?php echo $appointment['ly_do'] ?: 'Không có'; ?></div>
                                        </div>
                                        <?php if (!empty($appointment['ghi_chu'])): ?>
                                        <div class="col-md-12 mt-2">
                                            <div class="appointment-detail-label">Ghi chú</div>
                                            <div class="appointment-detail-value">
                                                <pre class="border p-2 bg-light" style="white-space: pre-wrap;"><?php echo $appointment['ghi_chu']; ?></pre>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if ($appointment['trang_thai'] === 'pending'): ?>
                                    <!-- Các action cho lịch hẹn đang chờ xác nhận -->
                                    <div class="appointment-actions d-flex gap-2">
                                        <form method="POST" class="me-2">
                                            <input type="hidden" name="action" value="confirm">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check-circle me-1"></i> Xác nhận lịch hẹn
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal">
                                            <i class="fas fa-times-circle me-1"></i> Hủy lịch hẹn
                                        </button>
                                    </div>
                                <?php elseif ($appointment['trang_thai'] === 'confirmed'): ?>
                                    <!-- Các action cho lịch hẹn đã xác nhận -->
                                    <div class="appointment-actions d-flex gap-2">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#completeAppointmentModal">
                                            <i class="fas fa-check-double me-1"></i> Hoàn thành khám
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelAppointmentModal">
                                            <i class="fas fa-times-circle me-1"></i> Hủy lịch hẹn
                                        </button>
                                    </div>
                                <?php elseif ($appointment['trang_thai'] === 'completed'): ?>
                                    <!-- Medical Results for completed appointments -->
                                    <div class="medical-form-card">
                                        <h5 class="detail-section-title">
                                            <i class="fas fa-file-medical me-2"></i> Kết quả khám bệnh
                                        </h5>
                                        
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <div class="appointment-detail-label">Chẩn đoán</div>
                                                <div class="appointment-detail-value"><?php echo $appointment['chan_doan']; ?></div>
                                            </div>
                                            <?php if (!empty($appointment['ket_qua'])): ?>
                                            <div class="col-md-12 mb-3">
                                                <div class="appointment-detail-label">Kết quả khám</div>
                                                <div class="appointment-detail-value">
                                                    <pre class="border p-2 bg-light" style="white-space: pre-wrap;"><?php echo $appointment['ket_qua']; ?></pre>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($appointment['don_thuoc'])): ?>
                                            <div class="col-md-12 mb-3">
                                                <div class="appointment-detail-label">Đơn thuốc</div>
                                                <div class="appointment-detail-value">
                                                    <pre class="border p-2 bg-light" style="white-space: pre-wrap;"><?php echo $appointment['don_thuoc']; ?></pre>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($appointment['loi_dan'])): ?>
                                            <div class="col-md-12">
                                                <div class="appointment-detail-label">Lời dặn</div>
                                                <div class="appointment-detail-value">
                                                    <pre class="border p-2 bg-light" style="white-space: pre-wrap;"><?php echo $appointment['loi_dan']; ?></pre>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div class="col-md-12 mt-3">
                                                <a href="includes/view_medical_record.php?id=<?php echo isset($appointment['id']) ? $appointment['id'] : 0; ?>" class="btn btn-primary" target="_blank">
                                                    <i class="fas fa-file-download me-1"></i> Tải xuống kết quả khám
                                                </a>
                                                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editResultModal">
                                                    <i class="fas fa-edit me-1"></i> Chỉnh sửa kết quả
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Danh sách lịch hẹn -->
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3">
                        <h1 class="h2">Quản lý lịch hẹn</h1>
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

                    <div class="search-filter">
                        <form method="GET" action="lichhen.php">
                            <div class="row g-3 filter-row">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" name="search" placeholder="Tìm theo tên bệnh nhân, mã lịch hẹn..." value="<?php echo htmlspecialchars($search_filter); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-calendar-day"></i></span>
                                        <input type="date" class="form-control" name="date" value="<?php echo $date_filter; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-filter"></i></span>
                                        <select class="form-select" name="status">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                            <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                            <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                            <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <?php if (count($appointments) > 0): ?>
                        <!-- Desktop view -->
                        <div class="desktop-only">
                            <div class="appointments-table">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Mã lịch hẹn</th>
                                            <th>Bệnh nhân</th>
                                            <th>Dịch vụ</th>
                                            <th>Ngày - Giờ</th>
                                            <th>Trạng thái</th>
                                            <th class="table-action">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($appointments as $appt): ?>
                                            <tr>
                                                <td><?php echo $appt['ma_lichhen']; ?></td>
                                                <td><?php echo $appt['patient_name']; ?></td>
                                                <td><?php echo $appt['service_name']; ?></td>
                                                <td>
                                                    <?php echo date('d/m/Y', strtotime($appt['ngay_hen'])); ?>
                                                    <br>
                                                    <small class="text-muted"><?php echo $appt['gio_hen']; ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                        $status_class = "";
                                                        $status_text = "";
                                                        switch($appt['trang_thai']) {
                                                            case 'pending':
                                                                $status_class = "bg-warning text-dark";
                                                                $status_text = "Chờ xác nhận";
                                                                break;
                                                            case 'confirmed':
                                                                $status_class = "bg-primary";
                                                                $status_text = "Đã xác nhận";
                                                                break;
                                                            case 'completed':
                                                                $status_class = "bg-success";
                                                                $status_text = "Đã hoàn thành";
                                                                break;
                                                            case 'cancelled':
                                                                $status_class = "bg-danger";
                                                                $status_text = "Đã hủy";
                                                                break;
                                                            default:
                                                                $status_class = "bg-secondary";
                                                                $status_text = ucfirst($appt['trang_thai']);
                                                        }
                                                    ?>
                                                    <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                </td>
                                                <td>
                                                    <a href="lichhen.php?action=view&id=<?php echo $appt['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Mobile view -->
                        <div class="mobile-only">
                            <?php foreach ($appointments as $appt): ?>
                                <div class="mobile-appointment-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong class="text-primary"><?php echo $appt['ma_lichhen']; ?></strong>
                                        </div>
                                        <div>
                                            <?php
                                                $status_class = "";
                                                $status_text = "";
                                                switch($appt['trang_thai']) {
                                                    case 'pending':
                                                        $status_class = "bg-warning text-dark";
                                                        $status_text = "Chờ xác nhận";
                                                        break;
                                                    case 'confirmed':
                                                        $status_class = "bg-primary";
                                                        $status_text = "Đã xác nhận";
                                                        break;
                                                    case 'completed':
                                                        $status_class = "bg-success";
                                                        $status_text = "Đã hoàn thành";
                                                        break;
                                                    case 'cancelled':
                                                        $status_class = "bg-danger";
                                                        $status_text = "Đã hủy";
                                                        break;
                                                    default:
                                                        $status_class = "bg-secondary";
                                                        $status_text = ucfirst($appt['trang_thai']);
                                                }
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div><i class="fas fa-user me-2"></i> <?php echo $appt['patient_name']; ?></div>
                                    <div><i class="fas fa-stethoscope me-2"></i> <?php echo $appt['service_name']; ?></div>
                                    <div><i class="fas fa-calendar-alt me-2"></i> <?php echo date('d/m/Y', strtotime($appt['ngay_hen'])); ?> - <?php echo $appt['gio_hen']; ?></div>
                                    <div class="mt-3">
                                        <a href="lichhen.php?action=view&id=<?php echo $appt['id']; ?>" class="btn btn-sm btn-primary w-100">
                                            <i class="fas fa-eye me-1"></i> Chi tiết
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center flex-wrap">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page - 1; ?>
                                                <?php echo !empty($status_filter) ? '&status=' . htmlspecialchars($status_filter) : ''; ?>
                                                <?php echo !empty($date_filter) ? '&date=' . htmlspecialchars($date_filter) : ''; ?>
                                                <?php echo !empty($search_filter) ? '&search=' . htmlspecialchars($search_filter) : ''; ?>" 
                                               aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php
                                    // Show limited page numbers
                                    $start_page = max(1, $current_page - 2);
                                    $end_page = min($total_pages, $current_page + 2);

                                    // Always show first page
                                    if ($start_page > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?page=1';
                                        echo !empty($status_filter) ? '&status=' . htmlspecialchars($status_filter) : '';
                                        echo !empty($date_filter) ? '&date=' . htmlspecialchars($date_filter) : '';
                                        echo !empty($search_filter) ? '&search=' . htmlspecialchars($search_filter) : '';
                                        echo '">1</a></li>';

                                        if ($start_page > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }

                                    for ($i = $start_page; $i <= $end_page; $i++) {
                                        echo '<li class="page-item ' . ($i == $current_page ? 'active' : '') . '">';
                                        echo '<a class="page-link" href="?page=' . $i;
                                        echo !empty($status_filter) ? '&status=' . htmlspecialchars($status_filter) : '';
                                        echo !empty($date_filter) ? '&date=' . htmlspecialchars($date_filter) : '';
                                        echo !empty($search_filter) ? '&search=' . htmlspecialchars($search_filter) : '';
                                        echo '">' . $i . '</a></li>';
                                    }

                                    // Always show last page
                                    if ($end_page < $total_pages) {
                                        if ($end_page < $total_pages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        
                                        echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages;
                                        echo !empty($status_filter) ? '&status=' . htmlspecialchars($status_filter) : '';
                                        echo !empty($date_filter) ? '&date=' . htmlspecialchars($date_filter) : '';
                                        echo !empty($search_filter) ? '&search=' . htmlspecialchars($search_filter) : '';
                                        echo '">' . $total_pages . '</a></li>';
                                    }
                                    ?>

                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $current_page + 1; ?>
                                                <?php echo !empty($status_filter) ? '&status=' . htmlspecialchars($status_filter) : ''; ?>
                                                <?php echo !empty($date_filter) ? '&date=' . htmlspecialchars($date_filter) : ''; ?>
                                                <?php echo !empty($search_filter) ? '&search=' . htmlspecialchars($search_filter) : ''; ?>" 
                                               aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="text-center p-5 bg-white rounded shadow-sm">
                            <i class="fas fa-calendar-times fa-4x mb-3 text-muted"></i>
                            <h5>Không tìm thấy lịch hẹn nào</h5>
                            <p class="text-muted">Không có lịch hẹn nào phù hợp với điều kiện tìm kiếm</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Modal hủy lịch hẹn -->
    <div class="modal fade" id="cancelAppointmentModal" tabindex="-1" aria-labelledby="cancelAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelAppointmentModalLabel">Hủy lịch hẹn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="cancel">
                        <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                        
                        <div class="mb-3">
                            <label for="ly_do" class="form-label">Lý do hủy <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="ly_do" name="ly_do" rows="3" required></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal hoàn thành lịch hẹn -->
    <div class="modal fade" id="completeAppointmentModal" tabindex="-1" aria-labelledby="completeAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeAppointmentModalLabel">Hoàn thành lịch hẹn & Cập nhật kết quả khám</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="completeAppointmentForm">
                        <input type="hidden" name="action" value="complete">
                        <input type="hidden" name="appointment_id" value="<?php echo isset($appointment['id']) ? $appointment['id'] : 0; ?>">
                        
                        <div class="mb-3">
                            <label for="chan_doan" class="form-label">Chẩn đoán <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="chan_doan" name="chan_doan" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="ket_qua" class="form-label">Kết quả khám</label>
                            <textarea class="form-control" id="ket_qua" name="ket_qua" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="don_thuoc" class="form-label">Đơn thuốc</label>
                            <textarea class="form-control" id="don_thuoc" name="don_thuoc" rows="3" placeholder="VD: 1. Paracetamol 500mg - Uống 1 viên khi sốt trên 38.5 độ"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="loi_dan" class="form-label">Lời dặn</label>
                            <textarea class="form-control" id="loi_dan" name="loi_dan" rows="2"></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <div class="d-flex">
                                <div class="me-2">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <p class="mb-0">Hoàn thành lịch hẹn sẽ cập nhật trạng thái lịch hẹn thành "Đã hoàn thành" và lưu thông tin kết quả khám bệnh.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-success">Hoàn thành khám</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chỉnh sửa kết quả khám -->
    <div class="modal fade" id="editResultModal" tabindex="-1" aria-labelledby="editResultModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editResultModalLabel">Chỉnh sửa kết quả khám</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="editResultForm">
                        <input type="hidden" name="action" value="complete">
                        <input type="hidden" name="appointment_id" value="<?php echo isset($appointment['id']) ? $appointment['id'] : 0; ?>">
                        
                        <div class="mb-3">
                            <label for="edit_chan_doan" class="form-label">Chẩn đoán <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_chan_doan" name="chan_doan" value="<?php echo htmlspecialchars($appointment['chan_doan'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_ket_qua" class="form-label">Kết quả khám</label>
                            <textarea class="form-control" id="edit_ket_qua" name="ket_qua" rows="3"><?php echo htmlspecialchars($appointment['ket_qua'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_don_thuoc" class="form-label">Đơn thuốc</label>
                            <textarea class="form-control" id="edit_don_thuoc" name="don_thuoc" rows="3"><?php echo htmlspecialchars($appointment['don_thuoc'] ?? ''); ?></textarea>
                            <small class="text-muted">VD: 1. Paracetamol 500mg - Uống 1 viên khi sốt trên 38.5 độ</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_loi_dan" class="form-label">Lời dặn</label>
                            <textarea class="form-control" id="edit_loi_dan" name="loi_dan" rows="2"><?php echo htmlspecialchars($appointment['loi_dan'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        });
    </script>
</body>
</html>