<?php
// Get current filename to determine which menu item should be active
$current_page = basename($_SERVER['SCRIPT_NAME']);

// Get patient info for the sidebar
if (!isset($patient) && isset($_SESSION['user_id'])) {
    // If patient info isn't already available, get it
    require_once 'functions.php';
    $patient = get_patient_info($_SESSION['user_id']);
}
?>

<div class="profile-card mb-4">
    <div class="profile-header">
        <div class="text-center mb-3">
            <div class="profile-avatar d-inline-block position-relative"
                style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 3px solid #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <img src="assets/img/user-avatar.png" alt="Ảnh đại diện"
                    style="width: 100%; height: 100%; object-fit: cover;">
            </div>
        </div>
        <h5 class="text-center mb-1"><?php echo htmlspecialchars($patient['ho_ten'] ?? 'Người dùng'); ?></h5>
        <p class="text-center text-muted small mb-0">
            <i class="fas fa-phone-alt me-1"></i> <?php echo htmlspecialchars($patient['dien_thoai'] ?? ''); ?>
        </p>
    </div>
    <div class="list-group list-group-flush">
        <a href="user_profile.php"
            class="list-group-item list-group-item-action <?php echo ($current_page === 'user_profile.php') ? 'active' : ''; ?>">
            <i class="fas fa-user me-2"></i> Thông tin cá nhân
        </a>
        <a href="lichsu_datlich.php"
            class="list-group-item list-group-item-action <?php echo ($current_page === 'lichsu_datlich.php') ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check me-2"></i> Lịch sử đặt khám
        </a>
        <a href="medical_records.php"
            class="list-group-item list-group-item-action <?php echo ($current_page === 'medical_records.php' || $current_page === 'view_medical_record.php') ? 'active' : ''; ?>">
            <i class="fas fa-file-medical-alt me-2"></i> Hồ sơ y tế
        </a>
        <a href="doimatkhau.php"
            class="list-group-item list-group-item-action <?php echo ($current_page === 'doimatkhau.php') ? 'active' : ''; ?>">
            <i class="fas fa-lock me-2"></i> Đổi mật khẩu
        </a>
        <a href="logout.php" class="list-group-item list-group-item-action text-danger">
            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
        </a>
    </div>
</div>