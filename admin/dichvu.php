<?php
// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Thiết lập phân trang
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 6; // Số dịch vụ hiển thị trên mỗi trang
if ($current_page < 1) $current_page = 1;

// Lấy các tham số lọc
$filter = [];
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filter['search'] = $_GET['search'];
}
if (isset($_GET['chuyenkhoa']) && !empty($_GET['chuyenkhoa'])) {
    $filter['chuyenkhoa'] = $_GET['chuyenkhoa'];
}
if (isset($_GET['trangthai']) && !empty($_GET['trangthai'])) {
    $filter['trangthai'] = $_GET['trangthai'];
}
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    $filter['sort'] = $_GET['sort'];
}

// Định nghĩa biến để ngăn dichvu_crud.php include lại file db_connect.php
$db_already_connected = true;

// Include file CRUD
require_once 'crud/dichvu_crud.php';

// Đếm tổng số dịch vụ theo điều kiện lọc
$total_services = countServices($filter);
$total_pages = ceil($total_services / $items_per_page);

// Điều chỉnh trang hiện tại nếu vượt quá tổng số trang
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Lấy danh sách dịch vụ với phân trang và tìm kiếm
$services = getAllServices($filter, $current_page, $items_per_page);
$specialties = getAllSpecialties();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Dịch vụ - Phòng khám Lộc Bình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="asset/admin.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/dichvu.css">
    <style>
        .service-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
            background-color: #fff;
            overflow: hidden;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .service-image {
            height: 180px;
            overflow: hidden;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .service-image i {
            font-size: 4rem;
            color: #aaa;
        }
        .service-body {
            padding: 20px;
        }
        .service-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            height: 50px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .service-info p {
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #666;
        }
        .service-description {
            height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin-bottom: 15px;
            font-size: 0.85rem;
            color: #666;
        }
        .service-price {
            font-weight: 600;
            color: #28a745;
            font-size: 1rem;
            margin-bottom: 15px;
        }
        .service-actions {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
        }
        .search-filter {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .no-services {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .service-status {
            position: absolute;
            top: 10px;
            right: 10px;
            border-radius: 50px;
            padding: 2px 10px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active {
            background-color: rgba(40, 167, 69, 0.9);
            color: white;
        }
        .status-inactive {
            background-color: rgba(108, 117, 125, 0.9);
            color: white;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                gap: 10px;
            }
            .search-filter .row {
                gap: 10px;
            }
            .service-card {
                margin-bottom: 15px;
            }
            .service-image {
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-12 mt-5 main-content">
                <div class="content-wrapper">
                    <div class="content-header d-flex justify-content-between align-items-center mb-4">
                        <h2 class="page-title">Quản lý Dịch vụ</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                            <i class="fas fa-plus"></i> Thêm dịch vụ
                        </button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="search-filter mb-4">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="row">
                                    <div class="col-lg-3 col-md-6 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" name="search" class="form-control" 
                                                placeholder="Tìm kiếm dịch vụ..." 
                                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 mb-2">
                                        <select name="chuyenkhoa" class="form-select">
                                            <option value="">Tất cả chuyên khoa</option>
                                            <?php foreach ($specialties as $specialty): ?>
                                                <option value="<?php echo $specialty['id']; ?>" <?php echo (isset($_GET['chuyenkhoa']) && $_GET['chuyenkhoa'] == $specialty['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $specialty['ten_chuyenkhoa']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <select name="trangthai" class="form-select">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="1" <?php echo (isset($_GET['trangthai']) && $_GET['trangthai'] == '1') ? 'selected' : ''; ?>>Hoạt động</option>
                                            <option value="0" <?php echo (isset($_GET['trangthai']) && $_GET['trangthai'] == '0') ? 'selected' : ''; ?>>Không hoạt động</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <select name="sort" class="form-select">
                                            <option value="">Sắp xếp theo</option>
                                            <option value="name" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name') ? 'selected' : ''; ?>>Tên dịch vụ</option>
                                            <option value="price" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price') ? 'selected' : ''; ?>>Giá</option>
                                            <option value="date" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'date') ? 'selected' : ''; ?>>Ngày tạo</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4 mb-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter"></i> Lọc
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="services-grid">
                        <?php if (count($services) > 0): ?>
                            <div class="row">
                                <?php foreach ($services as $service): ?>
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="service-card position-relative">
                                            <div class="service-status <?php echo $service['trangthai'] ? 'status-active' : 'status-inactive'; ?>">
                                                <?php echo $service['trangthai'] ? 'Hoạt động' : 'Không hoạt động'; ?>
                                            </div>
                                            <div class="service-image">
                                                <?php if (!empty($service['hinh_anh'])): ?>
                                                    <img src="../<?php echo htmlspecialchars($service['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($service['ten_dichvu']); ?>">
                                                <?php else: ?>
                                                    <i class="fas fa-heartbeat"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="service-body">
                                                <h4 class="service-title"><?php echo htmlspecialchars($service['ten_dichvu']); ?></h4>
                                                <div class="service-info">
                                                    <p><i class="fas fa-stethoscope me-2"></i><?php echo htmlspecialchars($service['ten_chuyenkhoa'] ?? 'Chưa phân loại'); ?></p>
                                                    <p class="service-price"><i class="fas fa-tags me-2"></i><?php echo number_format($service['gia_coban']); ?>đ</p>
                                                    <p class="service-description"><?php echo htmlspecialchars($service['mota_ngan'] ?? 'Chưa có mô tả'); ?></p>
                                                </div>
                                                <div class="service-actions">
                                                    <button class="btn btn-sm btn-info edit-service" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editServiceModal"
                                                        data-id="<?php echo $service['id']; ?>">
                                                        <i class="fas fa-edit"></i> Sửa
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-service" 
                                                        data-id="<?php echo $service['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($service['ten_dichvu']); ?>">
                                                        <i class="fas fa-trash"></i> Xóa
                                                    </button>
                                                </div>
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
                                                href="?page=<?php echo $current_page - 1; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['chuyenkhoa']) ? '&chuyenkhoa=' . htmlspecialchars($_GET['chuyenkhoa']) : ''; ?><?php echo isset($_GET['trangthai']) ? '&trangthai=' . htmlspecialchars($_GET['trangthai']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>"
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
                                                href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['chuyenkhoa']) ? '&chuyenkhoa=' . htmlspecialchars($_GET['chuyenkhoa']) : ''; ?><?php echo isset($_GET['trangthai']) ? '&trangthai=' . htmlspecialchars($_GET['trangthai']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $current_page + 1; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['chuyenkhoa']) ? '&chuyenkhoa=' . htmlspecialchars($_GET['chuyenkhoa']) : ''; ?><?php echo isset($_GET['trangthai']) ? '&trangthai=' . htmlspecialchars($_GET['trangthai']) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . htmlspecialchars($_GET['sort']) : ''; ?>"
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
                                    Hiển thị <?php echo count($services); ?> trong tổng số <?php echo $total_services; ?> dịch vụ
                                    (Trang <?php echo $current_page; ?> / <?php echo max(1, $total_pages); ?>)
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="no-services">
                                <?php if (!empty($filter)): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Không tìm thấy dịch vụ nào phù hợp với điều kiện lọc.
                                        <a href="dichvu.php" class="alert-link">Xem tất cả dịch vụ</a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Chưa có dịch vụ nào. Hãy thêm dịch vụ mới!
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm dịch vụ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addServiceForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="tenDichVu" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tenDichVu" name="tenDichVu" required>
                            </div>
                            <div class="col-md-4">
                                <label for="chuyenKhoaId" class="form-label">Chuyên khoa <span class="text-danger">*</span></label>
                                <select class="form-select" id="chuyenKhoaId" name="chuyenKhoaId" required>
                                    <option value="">Chọn chuyên khoa</option>
                                    <?php foreach ($specialties as $specialty): ?>
                                        <option value="<?php echo $specialty['id']; ?>">
                                            <?php echo $specialty['ten_chuyenkhoa']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="giaCoBan" class="form-label">Giá cơ bản <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="giaCoBan" name="giaCoBan" min="0" step="1000" required>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="trangThai" class="form-label">Trạng thái</label>
                                <select class="form-select" id="trangThai" name="trangThai">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Không hoạt động</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="hinhAnh" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="hinhAnh" name="hinhAnh" accept="image/*">
                            <small class="form-text text-muted">Hình ảnh minh họa cho dịch vụ (tối đa 2MB, định dạng JPG, PNG, GIF)</small>
                        </div>
                        <div class="mb-3">
                            <label for="moTaNgan" class="form-label">Mô tả ngắn <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="moTaNgan" name="moTaNgan" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="chiTiet" class="form-label">Chi tiết dịch vụ</label>
                            <textarea class="form-control" id="chiTiet" name="chiTiet" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitAddService">Thêm dịch vụ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa dịch vụ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="edit_tenDichVu" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_tenDichVu" name="tenDichVu" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_chuyenKhoaId" class="form-label">Chuyên khoa <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_chuyenKhoaId" name="chuyenKhoaId" required>
                                    <option value="">Chọn chuyên khoa</option>
                                    <?php foreach ($specialties as $specialty): ?>
                                        <option value="<?php echo $specialty['id']; ?>">
                                            <?php echo $specialty['ten_chuyenkhoa']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_giaCoBan" class="form-label">Giá cơ bản <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="edit_giaCoBan" name="giaCoBan" min="0" step="1000" required>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_trangThai" class="form-label">Trạng thái</label>
                                <select class="form-select" id="edit_trangThai" name="trangThai">
                                    <option value="1">Hoạt động</option>
                                    <option value="0">Không hoạt động</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_hinhAnh" class="form-label">Hình ảnh</label>
                            <div id="current_image_container" class="mb-2"></div>
                            <input type="file" class="form-control" id="edit_hinhAnh" name="hinhAnh" accept="image/*">
                            <small class="form-text text-muted">Để trống nếu không muốn thay đổi hình ảnh hiện tại</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_moTaNgan" class="form-label">Mô tả ngắn <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_moTaNgan" name="moTaNgan" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_chiTiet" class="form-label">Chi tiết dịch vụ</label>
                            <textarea class="form-control" id="edit_chiTiet" name="chiTiet" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitEditService">Lưu thay đổi</button>
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
            // Format giá tiền khi nhập
            function formatCurrency(input) {
                let value = input.value.replace(/[^\d]/g, '');
                if (value === '') {
                    input.value = '';
                    return;
                }
                input.value = parseInt(value, 10);
            }

            $('#giaCoBan, #edit_giaCoBan').on('input', function() {
                formatCurrency(this);
            });
            
            // Add new service
            $('#submitAddService').on('click', function() {
                var formData = new FormData($('#addServiceForm')[0]);
                
                $.ajax({
                    type: 'POST',
                    url: 'crud/dichvu_crud.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#addServiceModal').modal('hide');
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
            
            // Load service data for editing
            $('.edit-service').on('click', function() {
                var id = $(this).data('id');
                
                $.ajax({
                    type: 'GET',
                    url: 'crud/dichvu_crud.php',
                    data: {
                        action: 'get_service',
                        id: id
                    },
                    dataType: 'json',
                    success: function(service) {
                        if (service) {
                            $('#edit_id').val(service.id);
                            $('#edit_tenDichVu').val(service.ten_dichvu);
                            $('#edit_chuyenKhoaId').val(service.chuyenkhoa_id);
                            $('#edit_giaCoBan').val(service.gia_coban);
                            $('#edit_moTaNgan').val(service.mota_ngan);
                            $('#edit_chiTiet').val(service.chi_tiet);
                            $('#edit_trangThai').val(service.trangthai);
                            
                            // Hiển thị hình ảnh hiện tại nếu có
                            let currentImageContainer = $('#current_image_container');
                            currentImageContainer.empty();
                            
                            if (service.hinh_anh) {
                                currentImageContainer.html(
                                    '<img src="../' + service.hinh_anh + '" class="img-thumbnail" style="max-height: 100px;" alt="Hình ảnh hiện tại"><br>' +
                                    '<small class="text-muted">Hình ảnh hiện tại</small>'
                                );
                            } else {
                                currentImageContainer.html('<p class="text-muted">Không có hình ảnh</p>');
                            }
                        } else {
                            alert('Không thể tải thông tin dịch vụ.');
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi tải thông tin dịch vụ.');
                    }
                });
            });
            
            // Edit service
            $('#submitEditService').on('click', function() {
                var formData = new FormData($('#editServiceForm')[0]);
                
                $.ajax({
                    type: 'POST',
                    url: 'crud/dichvu_crud.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#editServiceModal').modal('hide');
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
            
            // Delete service
            $('.delete-service').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                
                if (confirm('Bạn có chắc chắn muốn xóa dịch vụ "' + name + '"?')) {
                    $.ajax({
                        type: 'POST',
                        url: 'crud/dichvu_crud.php',
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
            $('#addServiceModal').on('hidden.bs.modal', function() {
                $('#addServiceForm')[0].reset();
            });
            
            $('#editServiceModal').on('hidden.bs.modal', function() {
                $('#editServiceForm')[0].reset();
                $('#current_image_container').empty();
            });
        });
    </script>
</body>
</html>
