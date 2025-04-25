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

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = "Hồ sơ y tế";

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Search filters
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$filter_date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build query conditions
$where_conditions = ["l.benhnhan_id = ?"];
$params = [$patient['id']];
$param_types = "i";

if (!empty($search_query)) {
    $where_conditions[] = "(b.ho_ten LIKE ? OR c.ten_chuyenkhoa LIKE ? OR k.chan_doan LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $param_types .= "sss";
}

if (!empty($filter_date_from)) {
    $where_conditions[] = "l.ngay_hen >= ?";
    $params[] = $filter_date_from;
    $param_types .= "s";
}

if (!empty($filter_date_to)) {
    $where_conditions[] = "l.ngay_hen <= ?";
    $params[] = $filter_date_to;
    $param_types .= "s";
}

if (!empty($filter_status)) {
    $where_conditions[] = "l.trang_thai = ?";
    $params[] = $filter_status;
    $param_types .= "s";
}

$where_clause = implode(' AND ', $where_conditions);

// Count total records for pagination
$count_query = "SELECT COUNT(*) as total FROM ketqua_kham k
                JOIN lichhen l ON k.lichhen_id = l.id
                JOIN bacsi b ON l.bacsi_id = b.id
                JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id
                WHERE $where_clause";

$count_stmt = $conn->prepare($count_query);
if ($count_stmt) {
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row['total'];
    $total_pages = ceil($total_records / $records_per_page);
    $count_stmt->close();
} else {
    die("Error preparing count query: " . $conn->error);
}

// Get medical records
$query = "SELECT k.*, l.ngay_hen, l.gio_hen, l.ly_do, l.trang_thai,
          b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty_name,
          d.ten_dichvu AS service_name
          FROM ketqua_kham k
          JOIN lichhen l ON k.lichhen_id = l.id
          JOIN bacsi b ON l.bacsi_id = b.id
          JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id
          LEFT JOIN dichvu d ON l.dichvu_id = d.id
          WHERE $where_clause
          ORDER BY l.ngay_hen DESC, l.gio_hen DESC
          LIMIT ?, ?";

$stmt = $conn->prepare($query);
if ($stmt) {
    $params[] = $start_from;
    $params[] = $records_per_page;
    $param_types .= "ii";

    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $medical_records = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    die("Error preparing query: " . $conn->error);
}

// Get medications for each medical record
function get_medications($conn, $lichhen_id)
{
    $query = "SELECT d.*, t.ten_thuoc, t.don_vi 
              FROM don_thuoc d
              JOIN thuoc t ON d.thuoc_id = t.id
              WHERE d.lichhen_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $lichhen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $medications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $medications;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <link rel="stylesheet" href="assets/css/pages/medical_records.css">
    <style>
        .medical-record-card {
            border: 1px solid #eaeaea;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .medical-record-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 500;
        }
        
        .table-sm {
            font-size: 0.9rem;
        }
        
        .fw-bold {
            font-weight: 600;
        }
        
        .pagination .page-link {
            color: #0d6efd;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        @media (max-width: 767.98px) {
            .col-md-3 {
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container py-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <?php include 'includes/user_sidebar.php'; ?>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-file-medical me-2"></i>Hồ sơ y tế của tôi</h4>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter -->
                        <form method="GET" action="medical_records.php" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..."
                                            value="<?php echo htmlspecialchars($search_query); ?>">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" name="date_from" placeholder="Từ ngày"
                                        value="<?php echo $filter_date_from; ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" class="form-control" name="date_to" placeholder="Đến ngày"
                                        value="<?php echo $filter_date_to; ?>">
                                </div>
                                <div class="col-md-2">
                                    <select class="form-select" name="status">
                                        <option value="">-- Trạng thái --</option>
                                        <option value="completed" <?php echo $filter_status === 'completed' ? 'selected' : ''; ?>>Đã hoàn thành</option>
                                        <option value="confirmed" <?php echo $filter_status === 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                        <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <?php if (count($medical_records) > 0): ?>
                            <div class="medical-records">
                                <?php foreach ($medical_records as $record): ?>
                                    <div class="card mb-3 medical-record-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <span>
                                                <strong>Ngày khám:</strong>
                                                <?php echo date('d/m/Y', strtotime($record['ngay_hen'])); ?> |
                                                <strong>Giờ:</strong> <?php echo date('H:i', strtotime($record['gio_hen'])); ?>
                                            </span>
                                            <span class="badge <?php echo get_status_badge($record['trang_thai']); ?>">
                                                <?php echo get_status_name($record['trang_thai']); ?>
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Bác sĩ:</strong>
                                                        <?php echo htmlspecialchars($record['doctor_name']); ?></p>
                                                    <p><strong>Chuyên khoa:</strong>
                                                        <?php echo htmlspecialchars($record['specialty_name']); ?></p>
                                                    <?php if (!empty($record['service_name'])): ?>
                                                        <p><strong>Dịch vụ:</strong>
                                                            <?php echo htmlspecialchars($record['service_name']); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Chẩn đoán:</strong>
                                                        <?php echo htmlspecialchars($record['chan_doan']); ?></p>
                                                    <?php if (!empty($record['mo_ta'])): ?>
                                                        <p><strong>Mô tả:</strong>
                                                            <?php echo nl2br(htmlspecialchars($record['mo_ta'])); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <?php
                                            // Get medications for this record
                                            $medications = get_medications($conn, $record['lichhen_id']);
                                            if (count($medications) > 0):
                                                ?>
                                                <div class="mt-3">
                                                    <h6 class="fw-bold mb-2">Đơn thuốc:</h6>
                                                    <table class="table table-sm table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Thuốc</th>
                                                                <th>Liều dùng</th>
                                                                <th>Số lượng</th>
                                                                <th>Cách dùng</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($medications as $med): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($med['ten_thuoc']) . ' (' . $med['don_vi'] . ')'; ?>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($med['lieu_dung']); ?></td>
                                                                    <td><?php echo htmlspecialchars($med['so_luong']); ?></td>
                                                                    <td><?php echo htmlspecialchars($med['cach_dung']); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($record['ghi_chu'])): ?>
                                                <div class="mt-3">
                                                    <h6 class="fw-bold">Ghi chú / Lời dặn:</h6>
                                                    <p><?php echo nl2br(htmlspecialchars($record['ghi_chu'])); ?></p>
                                                </div>
                                            <?php endif; ?>

                                        <div class="mt-3 text-end">
                                                <a href="download_medical_record.php?id=<?php echo $record['id']; ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download me-1"></i> Tải xuống
                                                </a>
                                                <a href="view_medical_record.php?id=<?php echo $record['id']; ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye me-1"></i> Xem chi tiết
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center mt-4">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search_query); ?>&date_from=<?php echo $filter_date_from; ?>&date_to=<?php echo $filter_date_to; ?>&status=<?php echo $filter_status; ?>"
                                                    aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                                <a class="page-link"
                                                    href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&date_from=<?php echo $filter_date_from; ?>&date_to=<?php echo $filter_date_to; ?>&status=<?php echo $filter_status; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search_query); ?>&date_from=<?php echo $filter_date_from; ?>&date_to=<?php echo $filter_date_to; ?>&status=<?php echo $filter_status; ?>"
                                                    aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Không tìm thấy hồ sơ y tế nào phù hợp với điều kiện
                                tìm kiếm.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
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
?>