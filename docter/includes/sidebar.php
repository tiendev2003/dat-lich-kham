<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            
            <li class="nav-item">
                <a class="nav-link active" href="lichkham.php">
                    <i class="fas fa-calendar-alt"></i>
                    Lịch khám
                </a>
            </li>
            
            
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Cài đặt</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                
            </li>
            <li class="nav-item">
                <a class="nav-link" href="changepassword.php">
                    <i class="fas fa-key"></i>
                    Đổi mật khẩu
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    Đăng xuất
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    position: fixed;
    top: 60px;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
}

.sidebar .nav-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.sidebar .nav-link:hover {
    color: #005bac;
    background-color: #f8f9fa;
}

.sidebar .nav-link.active {
    color: #005bac;
    background-color: #e9ecef;
}

.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: static;
        height: auto;
        padding-top: 0;
    }
}
</style> 