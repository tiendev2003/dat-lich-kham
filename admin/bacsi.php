<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bác sĩ - Phòng khám Lộc Bình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/bacsi.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <div class="content-header">
                    <h2>Quản lý Bác sĩ</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                        <i class="fas fa-plus"></i> Thêm Bác sĩ
                    </button>
                </div>

                <!-- Search and Filter -->
                <div class="search-filter">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Tìm kiếm bác sĩ...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="specialtyFilter">
                                <option value="">Tất cả chuyên khoa</option>
                                <option value="1">Tim mạch</option>
                                <option value="2">Nhi khoa</option>
                                <!-- Thêm các chuyên khoa khác -->
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Doctors List -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
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
                                <td>1</td>
                                <td><img src="path/to/doctor1.jpg" class="doctor-avatar" alt="Doctor"></td>
                                <td>BS. Nguyễn Văn A</td>
                                <td>Tim mạch</td>
                                <td>doctor@example.com</td>
                                <td>0123456789</td>
                                <td><span class="badge bg-success">Đang làm việc</span></td>
                                <td>
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editDoctorModal">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(1)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <!-- Thêm các bác sĩ khác -->
                        </tbody>
                    </table>
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

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Bác sĩ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addDoctorForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chuyên khoa</label>
                                <select class="form-select" required>
                                    <option value="">Chọn chuyên khoa</option>
                                    <option value="1">Tim mạch</option>
                                    <option value="2">Nhi khoa</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Ảnh đại diện</label>
                                <input type="file" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Giới thiệu</label>
                                <textarea class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Thêm bác sĩ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Doctor Modal -->
    <div class="modal fade" id="editDoctorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa thông tin bác sĩ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Form tương tự như Add Doctor -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            if(confirm('Bạn có chắc chắn muốn xóa bác sĩ này?')) {
                // Xử lý xóa bác sĩ
            }
        }
    </script>
</body>
</html>
