<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Chuyên khoa - Phòng khám Lộc Bình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/chuyenkhoa.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-10 content">
                <div class="content-header">
                    <h2>Quản lý Chuyên khoa</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecialtyModal">
                        <i class="fas fa-plus"></i> Thêm chuyên khoa
                    </button>
                </div>

                <!-- Search -->
                <div class="search-filter">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" placeholder="Tìm kiếm chuyên khoa...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specialties List -->
                <div class="specialties-grid">
                    <div class="row">
                        <!-- Specialty Card -->
                        <div class="col-md-4 mb-4">
                            <div class="specialty-card">
                                <div class="specialty-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="specialty-info">
                                    <h4>Tim mạch</h4>
                                    <p>Số bác sĩ: 5</p>
                                    <p class="description">Chuyên điều trị các bệnh về tim mạch và mạch máu.</p>
                                </div>
                                <div class="specialty-actions">
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editSpecialtyModal">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(1)">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Another Specialty Card -->
                        <div class="col-md-4 mb-4">
                            <div class="specialty-card">
                                <div class="specialty-icon">
                                    <i class="fas fa-brain"></i>
                                </div>
                                <div class="specialty-info">
                                    <h4>Thần kinh</h4>
                                    <p>Số bác sĩ: 3</p>
                                    <p class="description">Chuyên điều trị các bệnh về hệ thần kinh.</p>
                                </div>
                                <div class="specialty-actions">
                                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editSpecialtyModal">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(2)">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </div>
                            </div>
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

    <!-- Add Specialty Modal -->
    <div class="modal fade" id="addSpecialtyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm chuyên khoa mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSpecialtyForm">
                        <div class="mb-3">
                            <label class="form-label">Tên chuyên khoa</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon (Font Awesome Class)</label>
                            <input type="text" class="form-control" placeholder="fa-heart">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Thêm chuyên khoa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Specialty Modal -->
    <div class="modal fade" id="editSpecialtyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa chuyên khoa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editSpecialtyForm">
                        <div class="mb-3">
                            <label class="form-label">Tên chuyên khoa</label>
                            <input type="text" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Icon (Font Awesome Class)</label>
                            <input type="text" class="form-control" placeholder="fa-heart">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" rows="4"></textarea>
                        </div>
                    </form>
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
            if(confirm('Bạn có chắc chắn muốn xóa chuyên khoa này?')) {
                // Xử lý xóa chuyên khoa
            }
        }
    </script>
</body>
</html>
