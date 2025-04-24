<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: dangnhap.php?redirect=huy_lichhen.php');
    exit;
}

// Get user and patient info
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);

// Get appointment data
$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$reason = $_POST['cancel_reason'] ?? '';
if ($reason === 'Khác' && !empty($_POST['other_reason'])) {
    $reason = trim($_POST['other_reason']);
}

// Validate appointment exists and belongs to user
$stmt = $conn->prepare("SELECT * FROM lichhen WHERE id = ? AND benhnhan_id = ?");
$stmt->bind_param('ii', $appointment_id, $patient['id']);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();

if (!$appointment) {
    // Appointment not found or doesn't belong to current user
    $_SESSION['error_message'] = "Lịch hẹn không tồn tại hoặc bạn không có quyền truy cập";
    header('Location: user_profile.php');
    exit;
}

// Check if appointment can be cancelled (only pending, confirmed or rescheduled)
if (!in_array($appointment['trang_thai'], ['pending', 'confirmed', 'rescheduled'])) {
    $_SESSION['error_message'] = "Lịch hẹn không thể hủy do đã trong trạng thái " . $appointment['trang_thai'];
    header('Location: huy_lichhen.php?id=' . $appointment_id);
    exit;
}

// Start transaction
$conn->begin_transaction();

try {
    // Cố gắng lưu lịch sử huỷ, nhưng bỏ qua nếu gặp lỗi
    try {
        $stmt = $conn->prepare(
            "INSERT INTO lichsu_lichhen (lichhen_id, hanh_dong, nguoi_thuc_hien, thoi_diem, ghi_chu)
             VALUES (?, 'cancel', ?, NOW(), ?)"
        );
        $stmt->bind_param('iis', $appointment_id, $user['id'], $reason);
        $stmt->execute();
    } catch (Exception $ex) {
        // Ghi log lỗi nhưng không dừng quy trình
        error_log("Warning: Không thể lưu lịch sử huỷ lịch: " . $ex->getMessage(), 3, "logs/user_errors_" . date('Y-m') . ".log");
    }
    
    // Cập nhật trạng thái lịch hẹn - phần quan trọng nhất
    $stmt = $conn->prepare(
        "UPDATE lichhen 
         SET trang_thai = 'cancelled',
             thoi_diem_huy = NOW(),
             ghi_chu = ?
         WHERE id = ?"
    );
    $stmt->bind_param('si', $reason, $appointment_id);
    $stmt->execute();
    
    // Commit changes
    $conn->commit();
    
    // Set success message
    $_SESSION['success_message'] = "Lịch hẹn đã được hủy thành công";
    
    // Redirect back to profile
    header('Location: user_profile.php#appointments');
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    
    // Log error
    error_log("Error cancelling appointment ID $appointment_id: " . $e->getMessage(), 3, "logs/user_errors_" . date('Y-m') . ".log");
    
    // Set error message
    $_SESSION['error_message'] = "Có lỗi xảy ra khi hủy lịch hẹn. Vui lòng thử lại hoặc liên hệ hỗ trợ.";
    
    // Redirect back to cancel page
    header('Location: huy_lichhen.php?id=' . $appointment_id . '&action=cancel');
    exit;
}