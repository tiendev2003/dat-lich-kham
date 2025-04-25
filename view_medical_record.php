<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: dangnhap.php');
    exit;
}

// Get user and patient data
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);

if (!$patient) {
    // Redirect to profile completion if patient info not found
    header('Location: user_profile.php?message=Vui lòng cập nhật thông tin cá nhân để xem hồ sơ y tế');
    exit;
}

// Check if record ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: medical_records.php');
    exit;
}

$record_id = intval($_GET['id']);

// Get the medical record and verify it belongs to this patient
$stmt = $conn->prepare("
    SELECT k.*, l.ngay_hen, l.gio_hen, l.ly_do, l.trang_thai, l.phi_kham, l.ma_lichhen,
           b.ho_ten AS doctor_name, b.id AS doctor_id, c.ten_chuyenkhoa AS specialty_name, 
           c.id AS specialty_id, d.ten_dichvu AS service_name, d.id AS service_id,
           bn.ho_ten AS patient_name, bn.nam_sinh, bn.gioi_tinh, bn.cmnd_cccd, bn.dien_thoai, bn.email
    FROM ketqua_kham k 
    JOIN lichhen l ON k.lichhen_id = l.id 
    JOIN bacsi b ON l.bacsi_id = b.id 
    JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id
    JOIN benhnhan bn ON l.benhnhan_id = bn.id
    LEFT JOIN dichvu d ON l.dichvu_id = d.id
    WHERE k.id = ? AND bn.id = ?
");
$stmt->bind_param('ii', $record_id, $patient['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Record not found or doesn't belong to this patient
    header('Location: medical_records.php?error=Không tìm thấy hồ sơ y tế hoặc bạn không có quyền truy cập');
    exit;
}

$record = $result->fetch_assoc();
$stmt->close();

// Get medications for this record
$med_stmt = $conn->prepare("
    SELECT d.*, t.ten_thuoc, t.don_vi, t.gia, t.huong_dan_chung
    FROM don_thuoc d
    JOIN thuoc t ON d.thuoc_id = t.id
    WHERE d.lichhen_id = ?
");
$med_stmt->bind_param('i', $record['lichhen_id']);
$med_stmt->execute();
$med_result = $med_stmt->get_result();
$medications = $med_result->fetch_all(MYSQLI_ASSOC);
$med_stmt->close();

// Calculate medication total cost
$medication_cost = 0;
foreach ($medications as $med) {
    $medication_cost += ($med['gia'] * $med['so_luong']);
}

$page_title = "Chi tiết hồ sơ y tế";
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/view_medical_record.css">
</head>

<body>
<?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-3">
                <?php include 'includes/user_sidebar.php'; ?>
            </div>
            <div class="col-lg-9">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Chi tiết hồ sơ y tế</h4>
                        <div>
                            <a href="medical_records.php" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại
                            </a>
                            <a href="download_medical_record.php?id=<?php echo $record_id; ?>"
                                class="btn btn-light btn-sm ms-2">
                                <i class="fas fa-download me-1"></i> Tải xuống
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="medical-record-header mb-4 p-3 bg-light rounded">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <span
                                                class="badge rounded-pill <?php echo get_status_badge($record['trang_thai']); ?> p-2 fw-bold">
                                                <?php echo get_status_name($record['trang_thai']); ?>
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="mb-1">Mã lịch hẹn:
                                                <?php echo htmlspecialchars($record['ma_lichhen']); ?>
                                            </h5>
                                            <p class="mb-0 text-muted">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($record['ngay_hen'])); ?> |
                                                <i class="far fa-clock me-1"></i>
                                                <?php echo date('H:i', strtotime($record['gio_hen'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-7 text-md-end">
                                    <p class="mb-1">
                                        <strong>Bác sĩ:</strong> <?php echo htmlspecialchars($record['doctor_name']); ?>
                                        <span
                                            class="text-primary">(<?php echo htmlspecialchars($record['specialty_name']); ?>)</span>
                                    </p>
                                    <?php if (!empty($record['service_name'])): ?>
                                        <p class="mb-0">
                                            <strong>Dịch vụ:</strong>
                                            <?php echo htmlspecialchars($record['service_name']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Thông tin bệnh nhân</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="35%">Họ và tên:</th>
                                                <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Năm sinh:</th>
                                                <td><?php echo htmlspecialchars($record['nam_sinh']); ?>
                                                    (<?php echo date('Y') - $record['nam_sinh']; ?> tuổi)</td>
                                            </tr>
                                            <tr>
                                                <th>Giới tính:</th>
                                                <td><?php echo htmlspecialchars($record['gioi_tinh']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>CMND/CCCD:</th>
                                                <td><?php echo htmlspecialchars($record['cmnd_cccd'] ?: 'Chưa cập nhật'); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Số điện thoại:</th>
                                                <td><?php echo htmlspecialchars($record['dien_thoai']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Email:</th>
                                                <td><?php echo htmlspecialchars($record['email'] ?: 'Chưa cập nhật'); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card h-100">
                                    <div class="card-header bg-light">
                                        <h5 class="card-title mb-0">Thông tin khám bệnh</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($record['ly_do'])): ?>
                                            <div class="mb-3">
                                                <h6 class="fw-bold">Lý do khám:</h6>
                                                <p><?php echo nl2br(htmlspecialchars($record['ly_do'])); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <h6 class="fw-bold">Chẩn đoán:</h6>
                                            <p><?php echo nl2br(htmlspecialchars($record['chan_doan'])); ?></p>
                                        </div>

                                        <?php if (!empty($record['mo_ta'])): ?>
                                            <div class="mb-3">
                                                <h6 class="fw-bold">Mô tả chi tiết:</h6>
                                                <p><?php echo nl2br(htmlspecialchars($record['mo_ta'])); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (count($medications) > 0): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Đơn thuốc</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>STT</th>
                                                    <th>Tên thuốc</th>
                                                    <th>Liều dùng</th>
                                                    <th>Số lượng</th>
                                                    <th>Cách dùng</th>
                                                    <th class="text-end">Đơn giá</th>
                                                    <th class="text-end">Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $stt = 1;
                                                foreach ($medications as $med):
                                                    $thanh_tien = $med['gia'] * $med['so_luong'];
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $stt++; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($med['ten_thuoc']); ?></strong>
                                                            <small
                                                                class="d-block text-muted"><?php echo htmlspecialchars($med['don_vi']); ?></small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($med['lieu_dung']); ?></td>
                                                        <td class="text-center">
                                                            <?php echo htmlspecialchars($med['so_luong']); ?>
                                                        </td>
                                                        <td><?php echo nl2br(htmlspecialchars($med['cach_dung'])); ?></td>
                                                        <td class="text-end">
                                                            <?php echo number_format($med['gia'], 0, ',', '.'); ?>
                                                            đ
                                                        </td>
                                                        <td class="text-end">
                                                            <?php echo number_format($thanh_tien, 0, ',', '.'); ?>
                                                            đ
                                                        </td>
                                                    </tr>
                                                    <?php if (!empty($med['huong_dan_chung'])): ?>
                                                        <tr>
                                                            <td colspan="7" class="bg-light">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    <?php echo htmlspecialchars($med['huong_dan_chung']); ?>
                                                                </small>
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <tr class="table-secondary">
                                                    <td colspan="5"></td>
                                                    <td class="text-end fw-bold">Tổng tiền thuốc:</td>
                                                    <td class="text-end fw-bold">
                                                        <?php echo number_format($medication_cost, 0, ',', '.'); ?> đ
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($record['ghi_chu'])): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Ghi chú / Lời dặn</h5>
                                </div>
                                <div class="card-body">
                                    <div class="p-3 bg-light-subtle border rounded">
                                        <?php echo nl2br(htmlspecialchars($record['ghi_chu'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Thông tin phí</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table">
                                            <tr>
                                                <th>Phí khám:</th>
                                                <td class="text-end">
                                                    <?php echo number_format($record['phi_kham'], 0, ',', '.'); ?> đ
                                                </td>
                                            </tr>
                                            <?php if (count($medications) > 0): ?>
                                                <tr>
                                                    <th>Tiền thuốc:</th>
                                                    <td class="text-end">
                                                        <?php echo number_format($medication_cost, 0, ',', '.'); ?> đ
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr class="fw-bold">
                                                <th>Tổng chi phí:</th>
                                                <td class="text-end">
                                                    <?php echo number_format($record['phi_kham'] + $medication_cost, 0, ',', '.'); ?>
                                                    đ
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="text-muted mb-1">
                                            <i class="far fa-calendar-check me-1"></i>
                                            <strong>Ngày khám:</strong>
                                            <?php echo date('d/m/Y', strtotime($record['ngay_hen'])); ?>
                                        </p>
                                        <p class="text-muted">
                                            <i class="far fa-calendar-plus me-1"></i>
                                            <strong>Ngày tạo hồ sơ:</strong>
                                            <?php echo date('d/m/Y H:i', strtotime($record['ngay_tao'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="medical_records.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                            </a>
                            <a href="download_medical_record.php?id=<?php echo $record_id; ?>" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i> Tải xuống hồ sơ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>



<?php
function get_status_badge($status)
{
    switch ($status) {
        case 'completed':
            return 'bg-success';
        case 'confirmed':
            return 'bg-primary';
        case 'pending':
            return 'bg-warning text-dark';
        case 'cancelled':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

function get_status_name($status)
{
    switch ($status) {
        case 'completed':
            return 'Đã hoàn thành';
        case 'confirmed':
            return 'Đã xác nhận';
        case 'pending':
            return 'Chờ xác nhận';
        case 'cancelled':
            return 'Đã hủy';
        default:
            return 'Không xác định';
    }
}

require_once 'includes/footer.php';
?>