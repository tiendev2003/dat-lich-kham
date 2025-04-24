<?php
$user = get_logged_in_user();
$doctor_id = null;

// Get the doctor ID from the logged-in user
if ($user && $user['vai_tro'] == 'bacsi') {
    $stmt = $conn->prepare("SELECT id, ho_ten, hinh_anh FROM bacsi WHERE nguoidung_id = ?");
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        $doctor_id = $doctor['id'];
        $doctor_name = $doctor['ho_ten'];
        $doctor_image = $doctor['hinh_anh'] ? '../' . $doctor['hinh_anh'] : '../assets/img/doctor-default.jpg';
    }
}

// Get the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-content d-flex flex-column p-3 text-white">
        <!-- Logo/Brand -->
        <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <i class="fas fa-clinic-medical me-2"></i>
            <span class="fs-4">Lộc Bình Clinic</span>
        </a>
        <hr>

        <!-- Menu Items -->
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="index.php" class="nav-link text-white <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
                    <i class="fas fa-home me-2"></i> Tổng quan
                </a>
            </li>

            <li>
                <a href="lichhen.php" class="nav-link text-white <?php echo ($current_page == 'lichhen.php') ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt me-2"></i> Lịch hẹn
                </a>
            </li>

            <li>
                <a href="profile.php" class="nav-link text-white <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user-md me-2"></i> Thông tin cá nhân
                </a>
            </li>
        </ul>
        <hr>

        <!-- User Profile -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo isset($doctor_image) ? $doctor_image : '../assets/img/doctor-default.jpg'; ?>" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong><?php echo isset($doctor_name) ? "BS. $doctor_name" : 'Bác sĩ'; ?></strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Mobile Header (Visible on small screens) -->
<div class="mobile-header">
    <button class="mobile-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <span class="brand-text">Lộc Bình Clinic</span>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="mobileUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?php echo isset($doctor_image) ? $doctor_image : '../assets/img/doctor-default.jpg'; ?>" alt="" width="32" height="32" class="rounded-circle">
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow dropdown-menu-end" aria-labelledby="mobileUserDropdown">
            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
        </ul>
    </div>
</div>

<!-- Overlay for sidebar on mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<style>
    .sidebar {
        background-color: #343a40;
        min-width: 250px;
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        z-index: 1000;
        transition: all 0.3s;
    }

    .sidebar-content {
        min-height: 100vh;
    }

    .nav-link {
        border-radius: 5px;
        margin: 5px 0;
        transition: all 0.3s;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: flex;
        align-items: center;
        padding: 10px 15px;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        background-color: #0d6efd !important;
    }

    .dropdown-item {
        padding: 8px 20px;
    }

    .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    hr {
        color: rgba(255, 255, 255, 0.3);
    }

    .mobile-header {
        display: none;
        background-color: #343a40;
        color: white;
        padding: 10px 15px;
        align-items: center;
        justify-content: space-between;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 999;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .mobile-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0;
    }

    .brand-text {
        font-weight: 600;
        font-size: 1.25rem;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    /* Main content spacing */
    .main-content {
        margin-left: 250px;
        transition: margin 0.3s;
    }

    @media (max-width: 991.98px) {
        .sidebar {
            left: -250px;
        }
        
        .sidebar.active {
            left: 0;
        }
        
        .mobile-header {
            display: flex;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        .main-content {
            margin-left: 0;
            padding-top: 60px !important;
        }
    }
</style>