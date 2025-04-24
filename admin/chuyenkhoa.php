<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Thiết lập phân trang
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 6; // Số chuyên khoa hiển thị trên mỗi trang
if ($current_page < 1) $current_page = 1;

// Lấy từ khóa tìm kiếm nếu có
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Định nghĩa biến để ngăn chuyenkhoa_crud.php include lại file db_connect.php
$db_already_connected = true;

// Include file CRUD
require_once 'crud/chuyenkhoa_crud.php';

// Đếm tổng số chuyên khoa theo điều kiện tìm kiếm
$total_specialties = countSpecialties($search);
$total_pages = ceil($total_specialties / $items_per_page);

// Điều chỉnh trang hiện tại nếu vượt quá tổng số trang
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Lấy danh sách chuyên khoa với phân trang và tìm kiếm
$specialties = getAllSpecialties($search, $current_page, $items_per_page);
?>
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
    <!-- Admin CSS -->
    <link rel="stylesheet" href="asset/admin.css">
    <!-- Custom CSS -->
    <style>
        .specialty-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            background-color: #fff;
        }
        .specialty-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .specialty-icon {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 15px;
            text-align: center;
        }
        .specialty-info h4 {
            margin-bottom: 10px;
            color: #333;
        }
        .specialty-info p.description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .specialty-actions {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
            gap: 5px;
        }
        .search-filter {
            background-color: #f8f9fa;
             border-radius: 8px;
            margin-bottom: 20px;
        }
      
        .no-specialties {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-12 main-content  mt-5 ">
                <div class="content-wrapper">
                    <div class="content-header d-flex justify-content-between align-items-center">
                        <h2 class="page-title">Quản lý Chuyên khoa</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSpecialtyModal">
                            <i class="fas fa-plus"></i> Thêm chuyên khoa
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="search-filter mb-4">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="row">
                                    <div class="col-md-8 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" name="search" class="form-control" 
                                                placeholder="Tìm kiếm theo tên hoặc mô tả..." 
                                                value="<?php echo htmlspecialchars($search); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter"></i> Lọc
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Specialties List -->
                    <div class="specialties-grid">
                        <?php if (count($specialties) > 0): ?>
                            <div class="row">
                                <?php foreach ($specialties as $specialty): ?>
                                    <div class="col-md-4 col-sm-6 mb-4">
                                        <div class="specialty-card">
                                            <div class="specialty-icon">
                                                <i class="fas <?php echo !empty($specialty['icon']) ? $specialty['icon'] : 'fa-stethoscope'; ?>"></i>
                                            </div>
                                            <div class="specialty-info">
                                                <h4><?php echo htmlspecialchars($specialty['ten_chuyenkhoa']); ?></h4>
                                                <p>Số bác sĩ: <?php echo $specialty['so_bacsi']; ?></p>
                                                <p class="description"><?php echo htmlspecialchars($specialty['mota'] ?? 'Chưa có mô tả'); ?></p>
                                            </div>
                                            <div class="specialty-actions">
                                                <button class="btn btn-sm btn-info edit-specialty" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editSpecialtyModal"
                                                    data-id="<?php echo $specialty['id']; ?>">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </button>
                                                <?php if ($specialty['so_bacsi'] == 0): ?>
                                                    <button class="btn btn-sm btn-danger delete-specialty" 
                                                        data-id="<?php echo $specialty['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($specialty['ten_chuyenkhoa']); ?>">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Pagination -->
                            <nav aria-label="Phân trang" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($current_page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>"
                                                aria-label="Previous">
                                                <span aria-hidden="true">Trước</span>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Trước</a>
                                        </li>
                                    <?php endif; ?>

                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                            <a class="page-link"
                                                href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>"
                                                aria-label="Next">
                                                <span aria-hidden="true">Sau</span>
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Sau</a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Hiển thị <?php echo count($specialties); ?> trong tổng số <?php echo $total_specialties; ?> chuyên khoa
                                    (Trang <?php echo $current_page; ?> / <?php echo max(1, $total_pages); ?>)
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="no-specialties">
                                <?php if (!empty($search)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Không tìm thấy chuyên khoa nào phù hợp với từ khóa "<?php echo htmlspecialchars($search); ?>".
                                        <a href="chuyenkhoa.php" class="alert-link">Xem tất cả chuyên khoa</a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Chưa có chuyên khoa nào. Hãy thêm chuyên khoa mới!
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
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
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label for="tenChuyenKhoa" class="form-label">Tên chuyên khoa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tenChuyenKhoa" name="tenChuyenKhoa" required>
                        </div>
                        <div class="mb-3">
                            <label for="icon" class="form-label">Icon (Font Awesome Class)</label>
                            <div class="input-group">
                                <span class="input-group-text">fa-</span>
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="stethoscope">
                            </div>
                            <small class="form-text text-muted">
                                Nhập tên icon từ Font Awesome, ví dụ: heart, brain, stethoscope...
                                <a href="https://fontawesome.com/icons?d=gallery&s=solid&m=free" target="_blank">Xem danh sách</a>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="moTa" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="moTa" name="moTa" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitAddSpecialty">Thêm chuyên khoa</button>
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
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label for="edit_tenChuyenKhoa" class="form-label">Tên chuyên khoa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_tenChuyenKhoa" name="tenChuyenKhoa" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_icon" class="form-label">Icon (Font Awesome Class)</label>
                            <div class="input-group">
                                <span class="input-group-text">fa-</span>
                                <input type="text" class="form-control" id="edit_icon" name="icon" placeholder="stethoscope">
                            </div>
                            <small class="form-text text-muted">
                                Nhập tên icon từ Font Awesome, ví dụ: heart, brain, stethoscope...
                                <a href="https://fontawesome.com/icons?d=gallery&s=solid&m=free" target="_blank">Xem danh sách</a>
                            </small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_moTa" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="edit_moTa" name="moTa" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitEditSpecialty">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Admin JS -->
    <script src="asset/admin.js"></script>
    
    <script>
        $(document).ready(function() {
            // Add new specialty
            $('#submitAddSpecialty').on('click', function() {
                var formData = $('#addSpecialtyForm').serialize();
                
                $.ajax({
                    type: 'POST',
                    url: 'crud/chuyenkhoa_crud.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#addSpecialtyModal').modal('hide');
                            location.reload(); // Reload page to see changes
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi xử lý yêu cầu.');
                    }
                });
            });
            
            // Load specialty data for editing
            $('.edit-specialty').on('click', function() {
                var id = $(this).data('id');
                
                $.ajax({
                    type: 'GET',
                    url: 'crud/chuyenkhoa_crud.php',
                    data: {
                        action: 'get_specialty',
                        id: id
                    },
                    dataType: 'json',
                    success: function(specialty) {
                        if (specialty) {
                            $('#edit_id').val(specialty.id);
                            $('#edit_tenChuyenKhoa').val(specialty.ten_chuyenkhoa);
                            $('#edit_icon').val(specialty.icon ? specialty.icon.replace('fa-', '') : '');
                            $('#edit_moTa').val(specialty.mota);
                        } else {
                            alert('Không thể tải thông tin chuyên khoa.');
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi tải thông tin chuyên khoa.');
                    }
                });
            });
            
            // Edit specialty
            $('#submitEditSpecialty').on('click', function() {
                var formData = $('#editSpecialtyForm').serialize();
                
                $.ajax({
                    type: 'POST',
                    url: 'crud/chuyenkhoa_crud.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#editSpecialtyModal').modal('hide');
                            location.reload(); // Reload page to see changes
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi xử lý yêu cầu.');
                    }
                });
            });
            
            // Delete specialty
            $('.delete-specialty').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                
                if (confirm('Bạn có chắc chắn muốn xóa chuyên khoa "' + name + '"?')) {
                    $.ajax({
                        type: 'POST',
                        url: 'crud/chuyenkhoa_crud.php',
                        data: {
                            action: 'delete',
                            id: id
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert(response.message);
                                location.reload(); // Reload page to see changes
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function() {
                            alert('Có lỗi xảy ra khi xử lý yêu cầu.');
                        }
                    });
                }
            });
            
            // Reset form when modal is closed
            $('#addSpecialtyModal').on('hidden.bs.modal', function() {
                $('#addSpecialtyForm')[0].reset();
            });
            
            $('#editSpecialtyModal').on('hidden.bs.modal', function() {
                $('#editSpecialtyForm')[0].reset();
            });
        });
    </script>
</body>
</html>
