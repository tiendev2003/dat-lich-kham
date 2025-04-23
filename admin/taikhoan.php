<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tài khoản - Phòng khám Lộc Bình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/taikhoan.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <div class="content-header">
                    <h2>Quản lý Tài khoản</h2>
                </div>

                <!-- Account Type Tabs -->
                <ul class="nav nav-tabs account-tabs mb-4" id="accountTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="patient-tab" data-bs-toggle="tab" data-bs-target="#patient" type="button" role="tab">
                            <i class="fas fa-user-injured me-2"></i>Tài khoản Bệnh nhân
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="doctor-tab" data-bs-toggle="tab" data-bs-target="#doctor" type="button" role="tab">
                            <i class="fas fa-user-md me-2"></i>Tài khoản Bác sĩ
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="accountTabContent">
                    <!-- Patient Accounts Tab -->
                    <div class="tab-pane fade show active" id="patient" role="tabpanel">
                        <!-- Search and Filter -->
                        <div class="search-filter">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Tìm kiếm bệnh nhân...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Trạng thái tài khoản</option>
                                        <option value="active">Đang hoạt động</option>
                                        <option value="inactive">Đã khóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Accounts Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ và tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Ngày đăng ký</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>P001</td>
                                        <td>Nguyễn Văn A</td>
                                        <td>nguyenvana@email.com</td>
                                        <td>0123456789</td>
                                        <td>01/03/2024</td>
                                        <td><span class="badge bg-success">Đang hoạt động</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" title="Khóa tài khoản">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Doctor Accounts Tab -->
                    <div class="tab-pane fade" id="doctor" role="tabpanel">
                        <!-- Search and Filter -->
                        <div class="search-filter">
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Tìm kiếm bác sĩ...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Chuyên khoa</option>
                                        <option value="1">Tim mạch</option>
                                        <option value="2">Nhi khoa</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select">
                                        <option value="">Trạng thái tài khoản</option>
                                        <option value="active">Đang hoạt động</option>
                                        <option value="inactive">Đã khóa</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Doctor Accounts Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ và tên</th>
                                        <th>Chuyên khoa</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>D001</td>
                                        <td>BS. Trần Thị B</td>
                                        <td>Tim mạch</td>
                                        <td>bstranthib@email.com</td>
                                        <td>0987654321</td>
                                        <td><span class="badge bg-success">Đang hoạt động</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" title="Khóa tài khoản">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Trước</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
