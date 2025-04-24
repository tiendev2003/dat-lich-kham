<?php
// Kiểm tra quyền truy cập
require_once '../includes/auth_check.php';

// Lấy thông tin bác sĩ đang đăng nhập
$user = get_logged_in_user();
$doctor_id = null;
$response = [];

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
    
    // Lấy số lượng lịch hẹn của bác sĩ
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_appointments FROM lichhen WHERE bacsi_id = ?");
    $stmt->bind_param('i', $doctor_id);
    $stmt->execute();
    $appointments_count = $stmt->get_result()->fetch_assoc()['total_appointments'];

    // Lấy số lượng bệnh nhân đã khám của bác sĩ
    $stmt = $conn->prepare("SELECT COUNT(DISTINCT benhnhan_id) AS total_patients FROM lichhen WHERE bacsi_id = ? AND trang_thai = 'completed'");
    $stmt->bind_param('i', $doctor_id);
    $stmt->execute();
    $patients_count = $stmt->get_result()->fetch_assoc()['total_patients'];
    
    // Thêm thông tin bổ sung vào đối tượng bác sĩ
    $doctor['appointments_count'] = $appointments_count;
    $doctor['patients_count'] = $patients_count;
    
    // Không cần xử lý đặc biệt cho các trường HTML, trả về nguyên bản
    // vì chúng sẽ được xử lý bởi Summernote
    
    $response = [
        'success' => true,
        'doctor' => $doctor
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Không tìm thấy thông tin bác sĩ'
    ];
}

// Trả về kết quả dạng JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
