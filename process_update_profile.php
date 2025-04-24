<?php
// process_update_profile.php - Handles the user profile update submission
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: dangnhap.php');
    exit;
}

// Get current user and patient data
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $ho_ten = isset($_POST['ho_ten']) ? trim($_POST['ho_ten']) : '';
    $nam_sinh = isset($_POST['nam_sinh']) ? (int)$_POST['nam_sinh'] : 0;
    $gioi_tinh = isset($_POST['gioi_tinh']) ? trim($_POST['gioi_tinh']) : '';
    $dien_thoai = isset($_POST['dien_thoai']) ? trim($_POST['dien_thoai']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $dia_chi = isset($_POST['dia_chi']) ? trim($_POST['dia_chi']) : '';
    $cmnd_cccd = isset($_POST['cmnd_cccd']) ? trim($_POST['cmnd_cccd']) : '';
    $nhom_mau = isset($_POST['nhom_mau']) ? trim($_POST['nhom_mau']) : '';
    $di_ung = isset($_POST['di_ung']) ? trim($_POST['di_ung']) : '';

    // Validation
    $errors = [];

    if (empty($ho_ten)) {
        $errors[] = 'Họ tên không được để trống';
    }

    if ($nam_sinh < 1900 || $nam_sinh > date('Y')) {
        $errors[] = 'Năm sinh không hợp lệ';
    }

    if (!in_array($gioi_tinh, ['Nam', 'Nữ', 'Khác'])) {
        $errors[] = 'Giới tính không hợp lệ';
    }

    if (empty($dien_thoai)) {
        $errors[] = 'Số điện thoại không được để trống';
    } elseif (!preg_match('/^[0-9]{10,11}$/', $dien_thoai)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }

    // If no errors, update the information
    if (empty($errors)) {
        try {
            // Start transaction
            $conn->begin_transaction();

            // Update patient information
            $query = "UPDATE benhnhan SET 
                ho_ten = ?, 
                nam_sinh = ?, 
                gioi_tinh = ?, 
                dien_thoai = ?, 
                email = ?, 
                dia_chi = ?, 
                cmnd_cccd = ?, 
                nhom_mau = ?, 
                di_ung = ?,
                ngay_capnhat = NOW()
                WHERE id = ?";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "sisssssssi", 
                $ho_ten,
                $nam_sinh, 
                $gioi_tinh, 
                $dien_thoai, 
                $email, 
                $dia_chi, 
                $cmnd_cccd, 
                $nhom_mau, 
                $di_ung,
                $patient['id']
            );
            $stmt->execute();

            // Also update the email in user table if it has changed
            if (!empty($email) && $email !== $user['email']) {
                $stmt = $conn->prepare("UPDATE nguoidung SET email = ?, ngay_capnhat = NOW() WHERE id = ?");
                $stmt->bind_param("si", $email, $user['id']);
                $stmt->execute();
                
                // Update session email
                $_SESSION['email'] = $email;
            }

            // Commit transaction
            $conn->commit();

            // Set success message and redirect
            $_SESSION['profile_message'] = [
                'type' => 'success',
                'text' => 'Thông tin cá nhân đã được cập nhật thành công'
            ];

            header('Location: user_profile.php');
            exit;

        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            
            $_SESSION['profile_message'] = [
                'type' => 'danger',
                'text' => 'Lỗi khi cập nhật thông tin: ' . $e->getMessage()
            ];
            
            header('Location: user_profile.php');
            exit;
        }
    } else {
        // Store errors in session and redirect back
        $_SESSION['profile_message'] = [
            'type' => 'danger',
            'text' => 'Lỗi: ' . implode(', ', $errors)
        ];
        
        header('Location: user_profile.php');
        exit;
    }
} else {
    // Not a POST request
    header('Location: user_profile.php');
    exit;
}
?>