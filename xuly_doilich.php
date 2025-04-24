<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Kiểm tra đăng nhập
if (!is_logged_in()) {
    header('Location: dangnhap.php?redirect=huy_lichhen.php');
    exit;
}

// Lấy thông tin người dùng
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);

// Lấy dữ liệu từ form
$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
$new_date = isset($_POST['new_date']) ? $_POST['new_date'] : '';
$new_time = isset($_POST['new_time']) ? $_POST['new_time'] : '';
$reason = isset($_POST['reschedule_reason']) ? $_POST['reschedule_reason'] : '';

// Nếu lý do là "Khác", lấy lý do chi tiết
if ($reason === 'Khác' && !empty($_POST['other_reschedule_reason'])) {
    $reason = trim($_POST['other_reschedule_reason']);
}

// Debug: Ghi thông tin vào log
error_log("Debug: Thông tin đổi lịch - ID: $appointment_id, Ngày mới: $new_date, Giờ mới: $new_time, Lý do: $reason", 0);

// Kiểm tra dữ liệu đầu vào
if (!$appointment_id || !$new_date || !$new_time) {
    $_SESSION['error_message'] = "Lỗi: Thiếu thông tin cần thiết để đổi lịch hẹn";
    header('Location: huy_lichhen.php?id=' . $appointment_id . '&action=reschedule');
    exit;
}

// Kiểm tra lịch hẹn tồn tại và thuộc về người dùng hiện tại
$stmt = $conn->prepare("SELECT * FROM lichhen WHERE id = ? AND benhnhan_id = ?");
$stmt->bind_param('ii', $appointment_id, $patient['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error_message'] = "Lịch hẹn không tồn tại hoặc bạn không có quyền truy cập";
    header('Location: user_profile.php');
    exit;
}

$appointment = $result->fetch_assoc();

// Kiểm tra trạng thái lịch hẹn có thể đổi được không
if (!in_array($appointment['trang_thai'], ['pending', 'confirmed', 'rescheduled'])) {
    $_SESSION['error_message'] = "Lịch hẹn không thể thay đổi do đã trong trạng thái " . $appointment['trang_thai'];
    header('Location: huy_lichhen.php?id=' . $appointment_id);
    exit;
}

// Tạo ghi chú về việc thay đổi lịch
$prev_datetime = date('d/m/Y', strtotime($appointment['ngay_hen'])) . ' ' . $appointment['gio_hen'];
$new_datetime = date('d/m/Y', strtotime($new_date)) . ' ' . $new_time;
$change_log = "Thay đổi lịch từ $prev_datetime sang $new_datetime. Lý do: $reason";

// Thực hiện cập nhật
try {
    // Cập nhật thông tin lịch hẹn trong bảng lichhen
    $stmt = $conn->prepare("UPDATE lichhen SET ngay_hen = ?, gio_hen = ?, trang_thai = 'rescheduled', ghi_chu = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị câu lệnh: " . $conn->error);
    }
    
    $stmt->bind_param('sssi', $new_date, $new_time, $change_log, $appointment_id);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception("Lỗi thực thi câu lệnh: " . $stmt->error);
    }
    
    // Ghi log thay đổi lịch
    error_log("Đã thay đổi lịch hẹn: ID $appointment_id, ngày/giờ cũ: {$appointment['ngay_hen']} {$appointment['gio_hen']}, ngày/giờ mới: $new_date $new_time", 0);
    
    // Đặt thông báo thành công
    $_SESSION['success_message'] = "Lịch hẹn đã được thay đổi thành công sang ngày " . 
                               date('d/m/Y', strtotime($new_date)) . " lúc $new_time";
    
    // Chuyển hướng về trang chi tiết
    header('Location: huy_lichhen.php?id=' . $appointment_id);
    exit;
    
} catch (Exception $e) {
    // Ghi log lỗi
    error_log("Lỗi đổi lịch hẹn ID $appointment_id: " . $e->getMessage(), 0);
    
    // Đặt thông báo lỗi
    $_SESSION['error_message'] = "Có lỗi xảy ra khi thay đổi lịch hẹn: " . $e->getMessage();
    
    // Chuyển hướng về trang thay đổi lịch
    header('Location: huy_lichhen.php?id=' . $appointment_id . '&action=reschedule');
    exit;
}