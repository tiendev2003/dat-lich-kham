<?php
/**
 * File xử lý ghi log lỗi
 */

// Kiểm tra nếu là yêu cầu AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'log_error') {
    // Lấy thông tin lỗi
    $errorType = isset($_POST['error_type']) ? $_POST['error_type'] : 'unknown_error';
    $errorMessage = isset($_POST['error_message']) ? $_POST['error_message'] : 'Không có thông tin lỗi';
    
    // Xóa thông tin nhạy cảm từ dữ liệu form (như mật khẩu)
    $formData = isset($_POST['form_data']) ? $_POST['form_data'] : '';
    // Loại bỏ mật khẩu nếu có
    $formData = preg_replace('/password=[^&]*/', 'password=REDACTED', $formData);
    
    // Thông tin bổ sung
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not_logged_in';
    $userIP = $_SERVER['REMOTE_ADDR'];
    $timestamp = date('Y-m-d H:i:s');
    $requestData = json_encode($_POST, JSON_UNESCAPED_UNICODE);
    
    // Tạo nội dung log
    $logEntry = "[{$timestamp}] [{$errorType}] [{$userIP}] [{$userId}] {$errorMessage}\n";
    if (!empty($formData)) {
        $logEntry .= "Form Data: {$formData}\n";
    }
    if (isset($_POST['doctor_id'])) {
        $logEntry .= "Doctor ID: {$_POST['doctor_id']}\n";
    }
    if (isset($_POST['status_code'])) {
        $logEntry .= "Status Code: {$_POST['status_code']}\n";
    }
    if (isset($_POST['response'])) {
        $logEntry .= "Response: " . substr($_POST['response'], 0, 500) . (strlen($_POST['response']) > 500 ? '...' : '') . "\n";
    }
    $logEntry .= "-----------------------------------\n";
    
    // Tạo thư mục logs nếu chưa tồn tại
    $logDir = __DIR__ . '/../../logs';
    if (!file_exists($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    // Ghi vào file log
    $logFile = $logDir . '/bacsi_errors_' . date('Y-m') . '.log';
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Trả về phản hồi
    echo json_encode(['success' => true, 'message' => 'Đã ghi nhật ký lỗi']);
}
?>
