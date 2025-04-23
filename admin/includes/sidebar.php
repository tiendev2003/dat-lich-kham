<div class="sidebar">
    <div class="sidebar-content d-flex flex-column p-3 text-white">
        <!-- Logo/Brand -->
        <a href="tongquan.php"
            class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
            <i class="fas fa-hospital me-2"></i>
            <span class="fs-4">Lộc Bình Clinic</span>
        </a>
        <hr>

        <!-- Menu Items -->
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="tongquan.php"
                    class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'tongquan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-home me-2"></i>
                    Tổng quan
                </a>
            </li>

            <li>
                <a href="bacsi.php"
                    class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'bacsi.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user-md me-2"></i>
                    Quản lý bác sĩ
                </a>
            </li>
            <li>
                <a href="benhnhan.php"
                    class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'benhnhan.php') ? 'active' : ''; ?>">
                    <i class="fas fa-users me-2"></i>
                    Quản lý bệnh nhân
                </a>
            </li>
            <li>
                <a href="chuyenkhoa.php"
                    class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'chuyenkhoa.php') ? 'active' : ''; ?>">
                    <i class="fas fa-stethoscope me-2"></i>
                    Quản lý chuyên khoa
                </a>
            </li>
            <li>
                <a href="dichvu.php"
                    class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'dichvu.php') ? 'active' : ''; ?>">
                    <i class="fas fa-hand-holding-medical me-2"></i>
                    Quản lý dịch vụ
                </a>
            </li>
            <li>
                <a href="tintuc.php"
                    class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'tintuc.php') ? 'active' : ''; ?>">
                    <i class="fas fa-newspaper me-2"></i>
                    Quản lý tin tức
                </a>
            </li>
            <li class="nav-item">
                <a href="caidat.php" class="nav-link text-white <?php echo (basename($_SERVER['PHP_SELF']) == 'caidat.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog me-2"></i>
                    Cài đặt Website
                </a>
            </li>
        </ul>
        <hr>

        <!-- User Profile -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong>Admin</strong>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
                <li><a class="dropdown-item" href="caidat.php"><i class="fas fa-cog me-2"></i>Cài đặt</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a>
                </li>
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
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
            id="mobileUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="" width="32" height="32" class="rounded-circle">
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow dropdown-menu-end"
            aria-labelledby="mobileUserDropdown">
            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
            <li><a class="dropdown-item" href="caidat.php"><i class="fas fa-cog me-2"></i>Cài đặt</a></li>
            <li>
                <hr class="dropdown-divider">
            </li>
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
        padding: 10px;
        align-items: center;
        justify-content: space-between;
        display: flex;
    }

    .mobile-toggle {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
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

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            transition: left 0.3s;
            z-index: 1000;
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
    }
</style>