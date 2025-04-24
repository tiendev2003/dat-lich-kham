<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Thiết lập phân trang
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$items_per_page = 10; // Số bác sĩ hiển thị trên mỗi trang
if ($current_page < 1)
    $current_page = 1;

// Lấy danh sách bác sĩ
$filter = [];
if (isset($_GET['specialty']) && !empty($_GET['specialty'])) {
    $filter['specialty_id'] = $_GET['specialty'];
}
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filter['search'] = $_GET['search'];
}

// Định nghĩa biến để ngăn bacsi_crud.php include lại file db_connect.php
$db_already_connected = true;

// Include file CRUD
require_once 'crud/bacsi_crud.php';

// Đếm tổng số bác sĩ theo điều kiện lọc
$total_doctors = countDoctors($filter);
$total_pages = ceil($total_doctors / $items_per_page);

// Điều chỉnh trang hiện tại nếu vượt quá tổng số trang
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Lấy danh sách bác sĩ với phân trang
$doctors = getAllDoctors($filter, $current_page, $items_per_page);
$specialties = getAllSpecialties();
?>
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
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/admin.css">
    <style>
        .doctor-info-modal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
        }

        .avatar-container {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
            border-radius: 50%;
            color: #aaa;
            font-size: 2rem;
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
                    <div class="content-header d-flex justify-content-between align-items-center  ">
                        <h2 class="page-title">Quản lý Bác sĩ</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                            <i class="fas fa-user-plus"></i> Thêm bác sĩ mới
                        </button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="search-filter mb-4">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="row">
                                    <div class="col-lg-5 col-md-12 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" name="search" class="form-control"
                                                placeholder="Tìm theo tên, email, số điện thoại..."
                                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-8 mb-2">
                                        <select name="specialty" class="form-select">
                                            <option value="">Tất cả chuyên khoa</option>
                                            <?php foreach ($specialties as $specialty): ?>
                                                <option value="<?php echo $specialty['id']; ?>" <?php echo (isset($_GET['specialty']) && $_GET['specialty'] == $specialty['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $specialty['ten_chuyenkhoa']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-4 mb-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter"></i> Lọc
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Doctors List -->
                    <div class="card">
                        <div class="card-body">
                            <?php if (count($doctors) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Ảnh</th>
                                                <th>Họ tên</th>
                                                <th>Chuyên khoa</th>
                                                <th class="d-none d-md-table-cell">Liên hệ</th>
                                                <th class="d-none d-md-table-cell">Lịch hẹn</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($doctors as $doctor): ?>
                                                <tr>
                                                    <td><?php echo $doctor['id']; ?></td>
                                                    <td>
                                                        <?php if (!empty($doctor['hinh_anh'])): ?>
                                                            <img src="../<?php echo $doctor['hinh_anh']; ?>"
                                                                alt="<?php echo $doctor['ho_ten']; ?>" class="doctor-avatar">
                                                        <?php else: ?>
                                                            <div class="avatar-container">
                                                                <i class="fas fa-user-md"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo $doctor['ho_ten']; ?></strong>
                                                        <div class="small text-muted">
                                                            <?php echo $doctor['gioi_tinh']; ?>,
                                                            <?php echo date('Y') - $doctor['nam_sinh']; ?> tuổi
                                                        </div>
                                                        <!-- Hiện thị thông tin liên hệ trên mobile -->
                                                        <div class="d-md-none small mt-1">
                                                            <div><i class="fas fa-phone-alt me-1"></i>
                                                                <?php echo $doctor['dien_thoai'] ?? 'Chưa cập nhật'; ?></div>
                                                            <div class="mt-1"><span
                                                                    class="badge bg-primary"><?php echo $doctor['so_lichhen']; ?>
                                                                    lịch hẹn</span></div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo $doctor['ten_chuyenkhoa'] ?? 'Chưa phân chuyên khoa'; ?></td>
                                                    <td class="d-none d-md-table-cell">
                                                        <div><i class="fas fa-phone-alt me-1"></i>
                                                            <?php echo $doctor['dien_thoai'] ?? 'Chưa cập nhật'; ?></div>
                                                        <div><i class="fas fa-envelope me-1"></i>
                                                            <?php echo $doctor['email'] ?? 'Chưa cập nhật'; ?></div>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <span class="badge bg-primary"><?php echo $doctor['so_lichhen']; ?> lịch
                                                            hẹn</span>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-info view-doctor" data-bs-toggle="modal"
                                                            data-bs-target="#viewDoctorModal"
                                                            data-id="<?php echo $doctor['id']; ?>" title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-primary edit-doctor"
                                                            data-bs-toggle="modal" data-bs-target="#editDoctorModal"
                                                            data-id="<?php echo $doctor['id']; ?>" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        </button>
                                                        <?php if ($doctor['so_lichhen'] == 0): ?>
                                                            <button class="btn btn-sm btn-danger delete-doctor"
                                                                data-id="<?php echo $doctor['id']; ?>" title="Xóa">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <nav aria-label="Phân trang" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($current_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $current_page - 1; ?><?php echo !empty($_GET['specialty']) ? '&specialty=' . htmlspecialchars($_GET['specialty']) : ''; ?><?php echo !empty($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>"
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
                                                    href="?page=<?php echo $i; ?><?php echo !empty($_GET['specialty']) ? '&specialty=' . htmlspecialchars($_GET['specialty']) : ''; ?><?php echo !empty($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link"
                                                    href="?page=<?php echo $current_page + 1; ?><?php echo !empty($_GET['specialty']) ? '&specialty=' . htmlspecialchars($_GET['specialty']) : ''; ?><?php echo !empty($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?>"
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
                                        Hiển thị <?php echo count($doctors); ?> trong tổng số <?php echo $total_doctors; ?>
                                        bác sĩ
                                        (Trang <?php echo $current_page; ?> / <?php echo $total_pages; ?>)
                                    </small>
                                </div>


                            <?php else: ?>
                                <div class="alert alert-info">Không tìm thấy bác sĩ nào. Hãy thêm bác sĩ mới!</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1" aria-labelledby="addDoctorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDoctorModalLabel">Thêm bác sĩ mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addDoctorForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="hoTen" class="form-label">Họ và tên <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="hoTen" name="hoTen" required>
                            </div>
                            <div class="col-md-6">
                                <label for="chuyenKhoaId" class="form-label">Chuyên khoa <span
                                        class="text-danger">*</span></label>
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
                                <label for="namSinh" class="form-label">Năm sinh</label>
                                <input type="number" class="form-control" id="namSinh" name="namSinh" min="1950"
                                    max="2010">
                            </div>
                            <div class="col-md-6">
                                <label for="gioiTinh" class="form-label">Giới tính</label>
                                <select class="form-select" id="gioiTinh" name="gioiTinh">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="dienThoai" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="dienThoai" name="dienThoai">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="diaChi" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="diaChi" name="diaChi">
                        </div>

                        <div class="mb-3">
                            <label for="hinhAnh" class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" id="hinhAnh" name="hinhAnh" accept="image/*">
                            <div class="form-text">Kích thước đề xuất: 300x300px, tối đa 2MB.</div>
                        </div>

                        <div class="mb-3">
                            <label for="moTa" class="form-label">Mô tả</label>
                            <textarea class="form-control summernote" id="moTa" name="moTa" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="bangCap" class="form-label">Bằng cấp</label>
                            <textarea class="form-control summernote" id="bangCap" name="bangCap" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="kinhNghiem" class="form-label">Kinh nghiệm</label>
                            <textarea class="form-control summernote" id="kinhNghiem" name="kinhNghiem"
                                rows="3"></textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="taoTaiKhoan" name="taoTaiKhoan"
                                value="1">
                            <label class="form-check-label" for="taoTaiKhoan">Tạo tài khoản đăng nhập cho bác sĩ</label>
                            <div class="form-text">Hệ thống sẽ tự động tạo tài khoản với email và mật khẩu ngẫu nhiên.
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitAddDoctor">Thêm bác sĩ</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Doctor Modal -->
    <div class="modal fade" id="editDoctorModal" tabindex="-1" aria-labelledby="editDoctorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDoctorModalLabel">Chỉnh sửa thông tin bác sĩ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editDoctorForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="editDoctorId">

                        <!-- Form fields similar to add form but with id prefix "edit_" -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_hoTen" class="form-label">Họ và tên <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_hoTen" name="hoTen" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_chuyenKhoaId" class="form-label">Chuyên khoa <span
                                        class="text-danger">*</span></label>
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
                                <label for="edit_namSinh" class="form-label">Năm sinh</label>
                                <input type="number" class="form-control" id="edit_namSinh" name="namSinh" min="1950"
                                    max="2010">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_gioiTinh" class="form-label">Giới tính</label>
                                <select class="form-select" id="edit_gioiTinh" name="gioiTinh">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                    <option value="Khác">Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_dienThoai" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="edit_dienThoai" name="dienThoai">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_diaChi" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="edit_diaChi" name="diaChi">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hình ảnh hiện tại</label>
                            <div id="currentImageContainer" class="mb-2">
                                <!-- Image will be displayed here -->
                            </div>
                            <label for="edit_hinhAnh" class="form-label">Cập nhật hình mới (nếu muốn thay đổi)</label>
                            <input type="file" class="form-control" id="edit_hinhAnh" name="hinhAnh" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label for="edit_moTa" class="form-label">Mô tả</label>
                            <textarea class="form-control summernote" id="edit_moTa" name="moTa" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_bangCap" class="form-label">Bằng cấp</label>
                            <textarea class="form-control summernote" id="edit_bangCap" name="bangCap"
                                rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_kinhNghiem" class="form-label">Kinh nghiệm</label>
                            <textarea class="form-control summernote" id="edit_kinhNghiem" name="kinhNghiem"
                                rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitEditDoctor">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Doctor Modal -->
    <div class="modal fade doctor-info-modal" id="viewDoctorModal" tabindex="-1" aria-labelledby="viewDoctorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewDoctorModalLabel">Thông tin chi tiết bác sĩ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4" id="doctorProfileImage">
                        <!-- Profile image will be displayed here -->
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h5 class="fw-bold" id="viewDoctorName"></h5>
                            <div class="text-muted" id="viewDoctorSpecialty"></div>
                        </div>
                        <div class="col-md-6 mb-3 text-md-end">
                            <div id="viewDoctorContact"></div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Thông tin cá nhân</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Giới tính:</strong> <span id="viewDoctorGender"></span>
                                        </li>
                                        <li class="mb-2"><strong>Năm sinh:</strong> <span
                                                id="viewDoctorBirthYear"></span></li>
                                        <li class="mb-2"><strong>Địa chỉ:</strong> <span id="viewDoctorAddress"></span>
                                        </li>
                                        <li class="mb-2"><strong>Ngày tham gia:</strong> <span
                                                id="viewDoctorJoinDate"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Thống kê</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><strong>Tổng lịch hẹn:</strong> <span
                                                id="viewDoctorTotalAppointments"></span></li>
                                        <li class="mb-2"><strong>Lịch hẹn hoàn thành:</strong> <span
                                                id="viewDoctorCompletedAppointments"></span></li>
                                        <li class="mb-2"><strong>Lịch hẹn sắp tới:</strong> <span
                                                id="viewDoctorUpcomingAppointments"></span></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2">Mô tả</h6>
                        <div id="viewDoctorDescription"></div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2">Bằng cấp</h6>
                        <div id="viewDoctorQualifications"></div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold border-bottom pb-2">Kinh nghiệm</h6>
                        <div id="viewDoctorExperience"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary edit-from-view" id="editFromView">Chỉnh sửa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Show Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="passwordModalLabel">Thông tin tài khoản</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <p>Tài khoản đã được tạo thành công với thông tin sau:</p>
                        <div class="mt-3">
                            <p><strong>Email:</strong> <span id="accountEmail"></span></p>
                            <p><strong>Mật khẩu:</strong> <span id="accountPassword"></span></p>
                        </div>
                        <p class="mt-2"><strong>Lưu ý:</strong> Hãy lưu thông tin này và thông báo cho bác sĩ. Bạn sẽ
                            không thể xem lại mật khẩu sau khi đóng thông báo này.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <!-- Admin JS -->
    <script src="asset/admin.js"></script>
    <script>
        $(document).ready(function () {
            // Initialize Summernote editor
            $('.summernote').summernote({
                height: 150,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });

            // Add doctor functionality (new code)
            $('#submitAddDoctor').on('click', function () {
                var formData = new FormData($('#addDoctorForm')[0]);

                $.ajax({
                    type: 'POST',
                    url: 'crud/bacsi_crud.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        try {
                            var result = typeof response === 'string' ? JSON.parse(response) : response;

                            if (result.success) {
                                alert(result.message);

                                // If a password was returned (account created), show it in the password modal
                                if (result.password) {
                                    $('#accountEmail').text($('#email').val());
                                    $('#accountPassword').text(result.password);
                                    $('#passwordModal').modal('show');
                                } else {
                                    $('#addDoctorModal').modal('hide');
                                    // Refresh page to see changes
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1000);
                                }
                            } else {
                                alert(result.message || 'Có lỗi xảy ra khi thêm bác sĩ.');
                            }
                        } catch (e) {
                            console.error('Lỗi khi xử lý phản hồi:', e, response);
                            alert('Có lỗi xảy ra trong quá trình xử lý phản hồi.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        alert('Có lỗi xảy ra khi kết nối tới máy chủ. Vui lòng thử lại sau.');
                    }
                });
            });

            // Debug các buttons để xem sự kiện có được kích hoạt không
            console.log("Số lượng nút view-doctor:", $('.view-doctor').length);
            console.log("Số lượng nút edit-doctor:", $('.edit-doctor').length);

            // Xử lý khi click vào nút Edit để sửa thông tin bác sĩ
            $(document).on('click', '.edit-doctor', function () {
                var doctorId = $(this).data('id');
                console.log("Đã click vào nút sửa bác sĩ ID:", doctorId);

                // Hiện modal trước để người dùng biết đang tải
                $('#editDoctorModal').modal('show');

                // Load thông tin bác sĩ từ server
                $.ajax({
                    type: 'GET',
                    url: 'crud/bacsi_crud.php',
                    data: {
                        action: 'get_doctor',
                        id: doctorId
                    },
                    dataType: 'json',
                    success: function (doctor) {
                        if (!doctor) {
                            alert('Không thể tải thông tin bác sĩ. Vui lòng thử lại sau.');
                            return;
                        }

                        if (doctor.error) {
                            alert(doctor.error);
                            return;
                        }

                        // Điền thông tin vào form
                        $('#editDoctorId').val(doctor.id);
                        $('#edit_hoTen').val(doctor.ho_ten);
                        $('#edit_chuyenKhoaId').val(doctor.chuyenkhoa_id);
                        $('#edit_namSinh').val(doctor.nam_sinh);
                        $('#edit_gioiTinh').val(doctor.gioi_tinh);
                        $('#edit_dienThoai').val(doctor.dien_thoai);
                        $('#edit_email').val(doctor.email);
                        $('#edit_diaChi').val(doctor.dia_chi);

                        // Xử lý và hiển thị hình ảnh hiện tại
                        if (doctor.hinh_anh) {
                            $('#currentImageContainer').html('<img src="../' + doctor.hinh_anh + '" alt="Hình ảnh hiện tại" class="img-thumbnail" style="max-height: 150px;">');
                        } else {
                            $('#currentImageContainer').html('<div class="text-muted">Chưa có hình ảnh</div>');
                        }

                        // Cập nhật nội dung các trình soạn thảo Summernote
                        $('#edit_moTa').summernote('code', doctor.mo_ta || '');
                        $('#edit_bangCap').summernote('code', doctor.bang_cap || '');
                        $('#edit_kinhNghiem').summernote('code', doctor.kinh_nghiem || '');
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        alert('Có lỗi xảy ra khi kết nối tới máy chủ. Vui lòng thử lại sau.');
                    }
                });
            });

            // Xử lý nút "Edit" từ modal xem chi tiết
            $(document).on('click', '.edit-from-view', function () {
                var doctorId = $(this).data('id');
                $('#viewDoctorModal').modal('hide'); // Ẩn modal xem chi tiết

                // Tạm thời đợi modal ẩn hoàn toàn rồi mới mở modal sửa
                setTimeout(function () {
                    $('.edit-doctor[data-id="' + doctorId + '"]').click();
                }, 500);
            });

            // Xử lý khi click nút "Cập nhật"
            $('#submitEditDoctor').on('click', function () {
                var formData = new FormData($('#editDoctorForm')[0]);

                $.ajax({
                    type: 'POST',
                    url: 'crud/bacsi_crud.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        try {
                            var result = typeof response === 'string' ? JSON.parse(response) : response;

                            if (result.success) {
                                alert(result.message);
                                $('#editDoctorModal').modal('hide');
                                // Refresh trang để thấy thay đổi
                                window.location.reload();
                            } else {
                                alert(result.message || 'Có lỗi xảy ra khi cập nhật.');
                            }
                        } catch (e) {
                            console.error('Lỗi khi xử lý phản hồi:', e, response);
                            alert('Có lỗi xảy ra trong quá trình xử lý phản hồi.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        alert('Có lỗi xảy ra khi kết nối tới máy chủ. Vui lòng thử lại sau.');
                    }
                });
            });

            // Fix: Thêm event delegation để đảm bảo các nút hoạt động ngay cả khi DOM được cập nhật
            $(document).on('click', '.view-doctor', function () {
                var doctorId = $(this).data('id');
                console.log("Đã click vào nút xem chi tiết bác sĩ ID:", doctorId);

                // Hiện modal trước để người dùng biết đang tải
                $('#viewDoctorModal').modal('show');

                // Load doctor data using AJAX
                $.ajax({
                    type: 'GET',
                    url: 'crud/bacsi_crud.php',
                    data: {
                        action: 'get_doctor_full',
                        id: doctorId
                    },
                    dataType: 'json', // Explicitly tell jQuery to expect JSON
                    success: function (response) {
                        console.log("Nhận được phản hồi:", response);

                        // Check if response is empty
                        if (!response) {
                            $('#viewDoctorModal .modal-body').html('<div class="alert alert-danger">Phản hồi từ máy chủ trống. Vui lòng kiểm tra file bacsi_crud.php.</div>');
                            return;
                        }

                        // Check if there's an error in the response
                        if (response.error) {
                            $('#viewDoctorModal .modal-body').html('<div class="alert alert-danger">' + response.error + '</div>');
                            return;
                        }

                        // We don't need to parse the JSON since jQuery already did it for us
                        var doctor = response;

                        // Fill the view modal
                        $('#viewDoctorName').text(doctor.ho_ten);
                        $('#viewDoctorSpecialty').text(doctor.ten_chuyenkhoa || 'Chưa phân chuyên khoa');

                        // Contact info
                        var contactHtml = '';
                        if (doctor.dien_thoai) contactHtml += '<div><i class="fas fa-phone-alt me-1"></i> ' + doctor.dien_thoai + '</div>';
                        if (doctor.email) contactHtml += '<div><i class="fas fa-envelope me-1"></i> ' + doctor.email + '</div>';
                        $('#viewDoctorContact').html(contactHtml);

                        // Personal info
                        $('#viewDoctorGender').text(doctor.gioi_tinh || 'Chưa cập nhật');
                        $('#viewDoctorBirthYear').text(doctor.nam_sinh || 'Chưa cập nhật');
                        $('#viewDoctorAddress').text(doctor.dia_chi || 'Chưa cập nhật');
                        $('#viewDoctorJoinDate').text(doctor.ngay_tao ? new Date(doctor.ngay_tao).toLocaleDateString('vi-VN') : 'Chưa cập nhật');

                        // Stats
                        $('#viewDoctorTotalAppointments').text(doctor.total_appointments || '0');
                        $('#viewDoctorCompletedAppointments').text(doctor.completed_appointments || '0');
                        $('#viewDoctorUpcomingAppointments').text(doctor.upcoming_appointments || '0');

                        // Detailed info
                        $('#viewDoctorDescription').html(doctor.mo_ta || '<p class="text-muted">Chưa cập nhật</p>');
                        $('#viewDoctorQualifications').html(doctor.bang_cap || '<p class="text-muted">Chưa cập nhật</p>');
                        $('#viewDoctorExperience').html(doctor.kinh_nghiem || '<p class="text-muted">Chưa cập nhật</p>');

                        // Profile image
                        if (doctor.hinh_anh) {
                            $('#doctorProfileImage').html('<img src="../' + doctor.hinh_anh + '" alt="' + doctor.ho_ten + '" class="img-fluid rounded" style="max-height: 200px;">');
                        } else {
                            $('#doctorProfileImage').html('<div class="avatar-container mx-auto" style="width: 150px; height: 150px; font-size: 4rem;"><i class="fas fa-user-md"></i></div>');
                        }

                        // Set the edit button to load this doctor's data
                        $('.edit-from-view').data('id', doctor.id);
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $('#viewDoctorModal .modal-body').html('<div class="alert alert-danger">Có lỗi xảy ra khi kết nối máy chủ. Vui lòng thử lại sau.</div>');
                    }
                });
            });
        });
    </script>
</body>

</html>