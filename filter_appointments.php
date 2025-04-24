<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Bạn cần đăng nhập để sử dụng tính năng này'
    ]);
    exit;
}

// Get patient information
$patient = get_patient_info($_SESSION['user_id']);
$patient_id = $patient['id'];

// Validate and sanitize inputs
$tab_type = isset($_POST['tab_type']) ? $_POST['tab_type'] : 'all';
$doctor_id = isset($_POST['doctor']) && !empty($_POST['doctor']) ? (int)$_POST['doctor'] : null;
$specialty_id = isset($_POST['specialty']) && !empty($_POST['specialty']) ? (int)$_POST['specialty'] : null;
$status = isset($_POST['status']) && !empty($_POST['status']) ? $_POST['status'] : null;
$search = isset($_POST['search']) && !empty($_POST['search']) ? trim($_POST['search']) : null;
$search_type = isset($_POST['search_type']) ? $_POST['search_type'] : 'all';
$sort = isset($_POST['sort']) ? $_POST['sort'] : 'newest';
$date_from = isset($_POST['date_from']) && !empty($_POST['date_from']) ? $_POST['date_from'] : null;
$date_to = isset($_POST['date_to']) && !empty($_POST['date_to']) ? $_POST['date_to'] : null;
$page = isset($_POST['page']) && is_numeric($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = 10; // Items per page
$offset = ($page - 1) * $limit;

// Build query based on filter parameters
$query_conditions = [];
$params = [];
$param_types = '';

// Always filter by patient ID
$query_conditions[] = "l.benhnhan_id = ?";
$params[] = $patient_id;
$param_types .= 'i';

// Add tab-specific filtering
switch ($tab_type) {
    case 'upcoming':
        $today = date('Y-m-d');
        $query_conditions[] = "l.trang_thai IN ('pending', 'confirmed')";
        $query_conditions[] = "l.ngay_hen >= ?";
        $params[] = $today;
        $param_types .= 's';
        break;
    case 'completed':
        $query_conditions[] = "l.trang_thai = 'completed'";
        break;
    case 'cancelled':
        $query_conditions[] = "l.trang_thai = 'cancelled'";
        break;
    // 'all' tab doesn't need additional filtering
}

// Add filter by doctor if selected
if ($doctor_id !== null) {
    $query_conditions[] = "l.bacsi_id = ?";
    $params[] = $doctor_id;
    $param_types .= 'i';
}

// Add filter by specialty if selected
if ($specialty_id !== null) {
    $query_conditions[] = "c.id = ?";
    $params[] = $specialty_id;
    $param_types .= 'i';
}

// Add filter by status if selected and not in a specific tab
if ($status !== null && $tab_type === 'all') {
    $query_conditions[] = "l.trang_thai = ?";
    $params[] = $status;
    $param_types .= 's';
}

// Add date filters
if ($date_from !== null) {
    $query_conditions[] = "l.ngay_hen >= ?";
    $params[] = $date_from;
    $param_types .= 's';
}
if ($date_to !== null) {
    $query_conditions[] = "l.ngay_hen <= ?";
    $params[] = $date_to;
    $param_types .= 's';
}

// Add search filter
if ($search !== null) {
    switch ($search_type) {
        case 'code':
            $query_conditions[] = "l.ma_lichhen LIKE ?";
            $params[] = "%$search%";
            $param_types .= 's';
            break;
        case 'reason':
            $query_conditions[] = "l.ly_do LIKE ?";
            $params[] = "%$search%";
            $param_types .= 's';
            break;
        default: // 'all'
            $query_conditions[] = "(l.ma_lichhen LIKE ? OR l.ly_do LIKE ? OR b.ho_ten LIKE ? OR c.ten_chuyenkhoa LIKE ? OR d.ten_dichvu LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $param_types .= 'sssss';
    }
}

// Combine all conditions
$sql_where = !empty($query_conditions) ? "WHERE " . implode(" AND ", $query_conditions) : "";

// Set sort order
$sql_order = "";
switch ($sort) {
    case 'oldest':
        $sql_order = "ORDER BY l.ngay_hen ASC, l.gio_hen ASC";
        break;
    case 'service':
        $sql_order = "ORDER BY d.ten_dichvu ASC, l.ngay_hen DESC";
        break;
    default: // 'newest'
        $sql_order = "ORDER BY l.ngay_hen DESC, l.gio_hen DESC";
}

// Count total filtered appointments first (for pagination)
$count_sql = "SELECT COUNT(l.id) as total 
              FROM lichhen l 
              JOIN bacsi b ON l.bacsi_id = b.id 
              JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id 
              JOIN dichvu d ON l.dichvu_id = d.id 
              $sql_where";

$stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result()->fetch_assoc();
$total_appointments = $total_result['total'];

// Main query to get filtered appointments
$sql = "SELECT l.*, b.ho_ten AS doctor_name, c.ten_chuyenkhoa AS specialty, d.ten_dichvu AS service  
        FROM lichhen l 
        JOIN bacsi b ON l.bacsi_id = b.id 
        JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id 
        JOIN dichvu d ON l.dichvu_id = d.id 
        $sql_where 
        $sql_order 
        LIMIT ? OFFSET ?";

$param_types .= 'ii';
$params[] = $limit;
$params[] = $offset;

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get filter label information for display in the summary
$filter_labels = [
    'doctor_name' => '',
    'specialty_name' => '',
    'status_text' => '',
    'date_from' => $date_from,
    'date_to' => $date_to,
    'search' => $search
];

// Get doctor name
if ($doctor_id) {
    $stmt = $conn->prepare("SELECT ho_ten FROM bacsi WHERE id = ?");
    $stmt->bind_param('i', $doctor_id);
    $stmt->execute();
    $filter_labels['doctor_name'] = $stmt->get_result()->fetch_assoc()['ho_ten'];
}

// Get specialty name
if ($specialty_id) {
    $stmt = $conn->prepare("SELECT ten_chuyenkhoa FROM chuyenkhoa WHERE id = ?");
    $stmt->bind_param('i', $specialty_id);
    $stmt->execute();
    $filter_labels['specialty_name'] = $stmt->get_result()->fetch_assoc()['ten_chuyenkhoa'];
}

// Get status text
if ($status) {
    $status_texts = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Đã hoàn thành',
        'cancelled' => 'Đã hủy',
        'rescheduled' => 'Đã đổi lịch'
    ];
    $filter_labels['status_text'] = $status_texts[$status] ?? $status;
}

// Generate HTML for the filtered results
ob_start();

if (empty($appointments)) {
    // No appointments found after filtering
    ?>
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-filter-circle-xmark"></i></div>
        <h5>Không tìm thấy lịch hẹn nào</h5>
        <p class="text-muted">Không có lịch hẹn nào phù hợp với bộ lọc của bạn</p>
        <button type="button" class="btn btn-outline-primary" id="resetFilterBtn">
            <i class="fas fa-undo-alt me-1"></i> Đặt lại bộ lọc
        </button>
    </div>
    <?php
} else {
    // Loop through filtered appointments and display them
    foreach ($appointments as $appointment) {
        ?>
        <div class="appointment-card">
            <div class="appointment-header">
                <div class="appointment-doctor">BS. <?php echo htmlspecialchars($appointment['doctor_name']); ?> - <?php echo htmlspecialchars($appointment['specialty']); ?></div>
                <div class="appointment-status status-<?php echo htmlspecialchars($appointment['trang_thai']); ?>">
                    <?php 
                    $status_text = '';
                    switch($appointment['trang_thai']) {
                        case 'confirmed': $status_text = 'Đã xác nhận'; break;
                        case 'pending': $status_text = 'Chờ xác nhận'; break;
                        case 'completed': $status_text = 'Đã hoàn thành'; break;
                        case 'cancelled': $status_text = 'Đã hủy'; break;
                        case 'rescheduled': $status_text = 'Đã đổi lịch'; break;
                        default: $status_text = ucfirst($appointment['trang_thai']);
                    }
                    echo htmlspecialchars($status_text); 
                    ?>
                </div>
            </div>
            <div class="appointment-date">
                <i class="far fa-calendar-alt me-2"></i> <?php echo date('l, d/m/Y', strtotime($appointment['ngay_hen'])); ?>
                <i class="far fa-clock ms-3 me-2"></i> <?php echo htmlspecialchars($appointment['gio_hen']); ?>
            </div>
            <div class="appointment-details">
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-notes-medical"></i></div>
                    <div>Dịch vụ: <span class="fw-medium"><?php echo htmlspecialchars($appointment['service']); ?></span></div>
                </div>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>Địa điểm: <span class="fw-medium"><?php echo htmlspecialchars($appointment['dia_chi'] ?? 'Chưa cập nhật'); ?></span></div>
                </div>
                <?php if (!empty($appointment['phi_kham'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-money-bill-alt"></i></div>
                    <div>Phí khám: <span class="fw-medium"><?php echo number_format($appointment['phi_kham'], 0, ',', '.'); ?> VNĐ</span></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($appointment['ma_lichhen'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-hashtag"></i></div>
                    <div>Mã lịch hẹn: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ma_lichhen']); ?></span></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($appointment['ly_do'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-comment-alt"></i></div>
                    <div>Lý do khám: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ly_do']); ?></span></div>
                </div>
                <?php endif; ?>
                
                <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['chan_doan'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-comment-medical"></i></div>
                    <div>Chẩn đoán: <span class="text-primary fw-medium"><?php echo htmlspecialchars($appointment['chan_doan']); ?></span></div>
                </div>
                <?php endif; ?>
                <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['ket_qua'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-clipboard-check"></i></div>
                    <div>Kết quả: <span class="fw-medium"><?php echo htmlspecialchars($appointment['ket_qua']); ?></span></div>
                </div>
                <?php endif; ?>
                <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['don_thuoc'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-prescription-bottle-alt"></i></div>
                    <div>Đơn thuốc: <span class="fw-medium"><?php echo htmlspecialchars($appointment['don_thuoc']); ?></span></div>
                </div>
                <?php endif; ?>
                <?php if ($appointment['trang_thai'] === 'completed' && !empty($appointment['loi_dan'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-comment-dots"></i></div>
                    <div>Lời dặn: <span class="fst-italic"><?php echo htmlspecialchars($appointment['loi_dan']); ?></span></div>
                </div>
                <?php endif; ?>
                
                <?php if ($appointment['trang_thai'] === 'cancelled' && !empty($appointment['ly_do_huy'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-info-circle text-danger"></i></div>
                    <div>Lý do hủy: <span class="text-danger"><?php echo htmlspecialchars($appointment['ly_do_huy']); ?></span></div>
                </div>
                <?php endif; ?>
                <?php if ($appointment['trang_thai'] === 'cancelled' && !empty($appointment['ngay_huy'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-calendar-times"></i></div>
                    <div>Ngày hủy: <span class="fw-medium"><?php echo date('d/m/Y', strtotime($appointment['ngay_huy'])); ?></span></div>
                </div>
                <?php endif; ?>
                <?php if ($appointment['trang_thai'] === 'rescheduled' && !empty($appointment['ly_do_doi'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-exchange-alt text-warning"></i></div>
                    <div>Lý do đổi lịch: <span class="text-warning"><?php echo htmlspecialchars($appointment['ly_do_doi']); ?></span></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($appointment['thanh_toan'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-credit-card"></i></div>
                    <div>Thanh toán: 
                        <span class="fw-medium">
                        <?php 
                            $payment_status = '';
                            switch($appointment['thanh_toan']) {
                                case 'paid': $payment_status = '<span class="text-success">Đã thanh toán</span>'; break;
                                case 'unpaid': $payment_status = '<span class="text-danger">Chưa thanh toán</span>'; break;
                                case 'partial': $payment_status = '<span class="text-warning">Thanh toán một phần</span>'; break;
                                default: $payment_status = htmlspecialchars($appointment['thanh_toan']);
                            }
                            echo $payment_status;
                        ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($appointment['ghi_chu'])): ?>
                <div class="appointment-detail">
                    <div class="detail-icon"><i class="fas fa-sticky-note"></i></div>
                    <div>Ghi chú: <span class="fst-italic"><?php echo htmlspecialchars($appointment['ghi_chu']); ?></span></div>
                </div>
                <?php endif; ?>
            </div>
            <div class="appointment-actions">
                <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> Chi tiết
                </a>
                
                <?php if ($appointment['trang_thai'] === 'completed'): ?>
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#ratingModal" data-appointment-id="<?php echo $appointment['id']; ?>">
                    <i class="fas fa-star"></i> Đánh giá
                </button>
                <?php if (!empty($appointment['ket_qua'])): ?>
                <a href="xacnhan_datlich.php?id=<?php echo $appointment['id']; ?>&result=view" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-file-medical"></i> Xem kết quả
                </a>
                <?php endif; ?>
                <a href="datlich.php?rebook=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-calendar-plus"></i> Đặt lại
                </a>
                <?php elseif ($appointment['trang_thai'] === 'cancelled'): ?>
                <a href="datlich.php?rebook=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-calendar-plus"></i> Đặt lại lịch hẹn
                </a>
                <?php elseif (in_array($appointment['trang_thai'], ['pending', 'confirmed'])): ?>
                <a href="xuly_doilich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-edit"></i> Thay đổi lịch
                </a>
                <a href="xuly_huylich.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-times"></i> Hủy lịch hẹn
                </a>
                <?php if (!isset($appointment['thanh_toan']) || $appointment['thanh_toan'] !== 'paid'): ?>
                <a href="thanhtoan.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-credit-card"></i> Thanh toán
                </a>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    // Add pagination if needed
    if ($total_appointments > $limit) {
        $total_pages = ceil($total_appointments / $limit);
        ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link page-prev" href="#" data-page="<?php echo $page-1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <?php
                // Calculate page numbers to show
                $start_page = max(1, min($page - 2, $total_pages - 4));
                $end_page = min($total_pages, max($page + 2, 5));
                
                // Show first page if not included in range
                if ($start_page > 1) {
                    echo '<li class="page-item"><a class="page-link page-num" href="#" data-page="1">1</a></li>';
                    if ($start_page > 2) {
                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                    }
                }
                
                // Show page numbers
                for ($i = $start_page; $i <= $end_page; $i++) {
                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '"><a class="page-link page-num" href="#" data-page="' . $i . '">' . $i . '</a></li>';
                }
                
                // Show last page if not included in range
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
                    }
                    echo '<li class="page-item"><a class="page-link page-num" href="#" data-page="' . $total_pages . '">' . $total_pages . '</a></li>';
                }
                ?>
                
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link page-next" href="#" data-page="<?php echo $page+1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <?php
    }
}

$html = ob_get_clean();

// Return the response as JSON
echo json_encode([
    'status' => 'success',
    'html' => $html,
    'count' => count($appointments),
    'total' => $total_appointments,
    'page' => $page,
    'total_pages' => ceil($total_appointments / $limit),
    'filters' => $filter_labels
]);
?>