<header class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Phòng khám</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                
                <li class="nav-item">
                    <a class="nav-link active" href="lichkham.php">Lịch khám</a>
                </li>
                
            </ul>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown">
                        <i class="fas fa-user-md"></i> Bác sĩ A
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> Thông tin cá nhân</a></li> -->
                        <li><a class="dropdown-item" href="changepassword.php"><i class="fas fa-key"></i> Đổi mật khẩu</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
body {
    padding-top: 60px;
}

.navbar-brand {
    font-weight: bold;
}

.nav-link {
    position: relative;
}

.nav-link.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #fff;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 10px;
}
</style> 