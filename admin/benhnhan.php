<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Thiết lập phân trang
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10; // Số bệnh nhân hiển thị trên mỗi trang
if ($current_page < 1) $current_page = 1;

// Lấy bộ lọc tìm kiếm nếu có
$search_name = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$search_phone = isset($_GET['search_phone']) ? $_GET['search_phone'] : '';
$db_already_connected = true;

// Include file CRUD
require_once 'crud/benhnhan_crud.php';

// Lấy danh sách bệnh nhân với bộ lọc
$filter = [
    'search_name' => $search_name,
    'search_phone' => $search_phone
];
$patients = getAllPatients($filter);

// Tổng số bệnh nhân
$total_patients = count($patients);
$total_pages = ceil($total_patients / $items_per_page);

// Điều chỉnh trang hiện tại nếu vượt quá tổng số trang
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Lấy danh sách bệnh nhân cho trang hiện tại
$start = ($current_page - 1) * $items_per_page;
$patients_page = array_slice($patients, $start, $items_per_page);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Bệnh nhân - Phòng khám Lộc Bình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="asset/admin.css">
    <!-- Custom CSS -->
    <style>
        .search-filter {
            background-color: #f8f9fa;
             border-radius: 8px;
            margin-bottom: 20px;
        }
      
        .table-responsive {
            background-color: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .patient-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .patient-actions {
            display: flex;
            gap: 5px;
        }
        .patient-icon {
            font-size: 2rem;
            color: #0d6efd;
            margin-bottom: 10px;
        }
        .modal-header {
            background-color: #f8f9fa;
        }
        .medical-history-item {
            border-left: 3px solid #0d6efd;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .prescription-item {
            background-color: #f8f9fa;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
        .gender-male {
            color: #0d6efd;
        }
        .gender-female {
            color: #d63384;
        }
        @media (max-width: 767px) {
            .patient-card {
                padding: 10px;
            }
            .patient-actions {
                justify-content: center;
                margin-top: 10px;
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
            <div class="col-md-12 main-content  mt-5 ">
                <div class="content-wrapper">
                    <div class="content-header d-flex justify-content-between align-items-center">
                        <h2 class="page-title">Quản lý Bệnh nhân</h2>
                    </div>

                    <!-- Search and Filter -->
                    <div class="search-filter">
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="GET" class="row">
                                    <div class="col-md-5  ">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" name="search_name" class="form-control" 
                                                placeholder="Tìm theo tên bệnh nhân..." 
                                                value="<?php echo htmlspecialchars($search_name); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-5  ">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" name="search_phone" class="form-control" 
                                                placeholder="Tìm theo số điện thoại..." 
                                                value="<?php echo htmlspecialchars($search_phone); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2  ">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i> Tìm kiếm
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Patients List -->
                    <div class="table-responsive">
                        <?php if (count($patients_page) > 0): ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="20%">Họ và tên</th>
                                        <th width="10%">Năm sinh</th>
                                        <th width="10%">Giới tính</th>
                                        <th width="15%">Số điện thoại</th>
                                        <th width="20%">Địa chỉ</th>
                                        <th width="10%">Lần khám gần nhất</th>
                                        <th width="10%">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($patients_page as $patient): ?>
                                        <tr>
                                            <td><?php echo $patient['id']; ?></td>
                                            <td><?php echo htmlspecialchars($patient['ho_ten']); ?></td>
                                            <td><?php echo $patient['nam_sinh']; ?></td>
                                            <td>
                                                <?php if ($patient['gioi_tinh'] == 'Nam'): ?>
                                                    <i class="fas fa-male gender-male"></i> Nam
                                                <?php else: ?>
                                                    <i class="fas fa-female gender-female"></i> Nữ
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($patient['dien_thoai']); ?></td>
                                            <td><?php echo htmlspecialchars($patient['dia_chi']); ?></td>
                                            <td>
                                                <?php 
                                                    echo !empty($patient['lan_kham_gannhat']) ? date('d/m/Y', strtotime($patient['lan_kham_gannhat'])) : 'Chưa khám';
                                                ?>
                                            </td>
                                            <td>
                                                <div class="patient-actions">
                                                    <button class="btn btn-sm btn-info view-patient" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewPatientModal" 
                                                        data-id="<?php echo $patient['id']; ?>" 
                                                        title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary edit-patient" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editPatientModal" 
                                                        data-id="<?php echo $patient['id']; ?>" 
                                                        title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-success view-history" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#historyModal" 
                                                        data-id="<?php echo $patient['id']; ?>" 
                                                        data-name="<?php echo htmlspecialchars($patient['ho_ten']); ?>" 
                                                        title="Lịch sử khám">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Mobile View - Cards -->
                            <div class="d-md-none">
                                <?php foreach ($patients_page as $patient): ?>
                                    <div class="patient-card">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="patient-icon">
                                                    <?php if ($patient['gioi_tinh'] == 'Nam'): ?>
                                                        <i class="fas fa-male gender-male"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-female gender-female"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-9">
                                                <h5><?php echo htmlspecialchars($patient['ho_ten']); ?></h5>
                                                <p><strong>SĐT:</strong> <?php echo htmlspecialchars($patient['dien_thoai']); ?></p>
                                                <p><strong>Năm sinh:</strong> <?php echo $patient['nam_sinh']; ?></p>
                                                <p><strong>Lần khám gần nhất:</strong> 
                                                    <?php echo !empty($patient['lan_kham_gannhat']) ? date('d/m/Y', strtotime($patient['lan_kham_gannhat'])) : 'Chưa khám'; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="patient-actions mt-2">
                                            <button class="btn btn-sm btn-info view-patient" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewPatientModal" 
                                                data-id="<?php echo $patient['id']; ?>">
                                                <i class="fas fa-eye"></i> Chi tiết
                                            </button>
                                            <button class="btn btn-sm btn-primary edit-patient" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editPatientModal" 
                                                data-id="<?php echo $patient['id']; ?>">
                                                <i class="fas fa-edit"></i> Sửa
                                            </button>
                                            <button class="btn btn-sm btn-success view-history" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#historyModal" 
                                                data-id="<?php echo $patient['id']; ?>" 
                                                data-name="<?php echo htmlspecialchars($patient['ho_ten']); ?>">
                                                <i class="fas fa-history"></i> Lịch sử
                                            </button>
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
                                                href="?page=<?php echo $current_page - 1; ?><?php echo !empty($search_name) ? '&search_name=' . htmlspecialchars($search_name) : ''; ?><?php echo !empty($search_phone) ? '&search_phone=' . htmlspecialchars($search_phone) : ''; ?>"
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
                                                href="?page=<?php echo $i; ?><?php echo !empty($search_name) ? '&search_name=' . htmlspecialchars($search_name) : ''; ?><?php echo !empty($search_phone) ? '&search_phone=' . htmlspecialchars($search_phone) : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <?php if ($current_page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link"
                                                href="?page=<?php echo $current_page + 1; ?><?php echo !empty($search_name) ? '&search_name=' . htmlspecialchars($search_name) : ''; ?><?php echo !empty($search_phone) ? '&search_phone=' . htmlspecialchars($search_phone) : ''; ?>"
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
                                    Hiển thị <?php echo count($patients_page); ?> trong tổng số <?php echo $total_patients; ?> bệnh nhân
                                    (Trang <?php echo $current_page; ?> / <?php echo max(1, $total_pages); ?>)
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <?php if (!empty($search_name) || !empty($search_phone)): ?>
                                    <i class="fas fa-info-circle me-2"></i>
                                    Không tìm thấy bệnh nhân nào phù hợp với tiêu chí tìm kiếm.
                                    <a href="benhnhan.php" class="alert-link">Xem tất cả bệnh nhân</a>
                                <?php else: ?>
                                    <i class="fas fa-info-circle me-2"></i>
                                    Chưa có dữ liệu bệnh nhân nào trong hệ thống.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Patient Modal -->
    <div class="modal fade" id="viewPatientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin chi tiết bệnh nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="patient-info">
                        <div class="text-center mb-4">
                            <div class="patient-avatar">
                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                            </div>
                            <h4 id="view_hoTen" class="mt-2">Đang tải...</h4>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p><strong>Năm sinh:</strong> <span id="view_namSinh"></span></p>
                                <p><strong>Giới tính:</strong> <span id="view_gioiTinh"></span></p>
                                <p><strong>Số điện thoại:</strong> <span id="view_dienThoai"></span></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p><strong>Email:</strong> <span id="view_email"></span></p>
                                <p><strong>Địa chỉ:</strong> <span id="view_diaChi"></span></p>
                                <p><strong>Nhóm máu:</strong> <span id="view_nhomMau"></span></p>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <p><strong>Dị ứng:</strong> <span id="view_diUng"></span></p>
                            <p><strong>Ngày đăng ký:</strong> <span id="view_ngayTao"></span></p>
                            <p><strong>Cập nhật lần cuối:</strong> <span id="view_ngayCapNhat"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div class="modal fade" id="editPatientModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chỉnh sửa thông tin bệnh nhân</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editPatientForm">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="hoTen" id="edit_hoTen" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Năm sinh <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="namSinh" id="edit_namSinh" min="1900" max="2025" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Giới tính <span class="text-danger">*</span></label>
                                <select class="form-select" name="gioiTinh" id="edit_gioiTinh" required>
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="dienThoai" id="edit_dienThoai" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="diaChi" id="edit_diaChi" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nhóm máu</label>
                                <select class="form-select" name="nhomMau" id="edit_nhomMau">
                                    <option value="">Chọn nhóm máu</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="O">O</option>
                                    <option value="AB">AB</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dị ứng</label>
                                <input type="text" class="form-control" name="diUng" id="edit_diUng" placeholder="Ghi rõ dị ứng nếu có">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitEditPatient">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical History Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lịch sử khám bệnh - <span id="history_patientName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="historyContent" class="mt-3">
                        <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Prescription Modal -->
    <div class="modal fade" id="prescriptionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chi tiết đơn thuốc</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="prescriptionContent">
                        <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
            // Load patient details
            $('.view-patient').on('click', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'GET',
                    url: 'crud/benhnhan_crud.php',
                    data: {
                        action: 'get_patient',
                        id: id
                    },
                    dataType: 'json',
                    success: function(patient) {
                        if (patient) {
                            $('#view_hoTen').text(patient.ho_ten);
                            $('#view_namSinh').text(patient.nam_sinh);
                            $('#view_gioiTinh').text(patient.gioi_tinh);
                            $('#view_dienThoai').text(patient.dien_thoai);
                            $('#view_email').text(patient.email || 'Không có');
                            $('#view_diaChi').text(patient.dia_chi);
                            $('#view_nhomMau').text(patient.nhom_mau || 'Không có thông tin');
                            $('#view_diUng').text(patient.di_ung || 'Không có thông tin');
                            $('#view_ngayTao').text(formatDateFromSQL(patient.ngay_tao));
                            $('#view_ngayCapNhat').text(formatDateFromSQL(patient.ngay_capnhat));
                        } else {
                            alert('Không thể tải thông tin bệnh nhân.');
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi tải thông tin bệnh nhân.');
                    }
                });
            });
            
            // Load patient data for editing
            $('.edit-patient').on('click', function() {
                var id = $(this).data('id');
                
                $.ajax({
                    type: 'GET',
                    url: 'crud/benhnhan_crud.php',
                    data: {
                        action: 'get_patient',
                        id: id
                    },
                    dataType: 'json',
                    success: function(patient) {
                        if (patient) {
                            $('#edit_id').val(patient.id);
                            $('#edit_hoTen').val(patient.ho_ten);
                            $('#edit_namSinh').val(patient.nam_sinh);
                            $('#edit_gioiTinh').val(patient.gioi_tinh);
                            $('#edit_dienThoai').val(patient.dien_thoai);
                            $('#edit_email').val(patient.email || '');
                            $('#edit_diaChi').val(patient.dia_chi);
                            $('#edit_nhomMau').val(patient.nhom_mau || '');
                            $('#edit_diUng').val(patient.di_ung || '');
                        } else {
                            alert('Không thể tải thông tin bệnh nhân.');
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi tải thông tin bệnh nhân.');
                    }
                });
            });
            
            // Submit patient edit form
            $('#submitEditPatient').on('click', function() {
                var formData = $('#editPatientForm').serialize();
                
                $.ajax({
                    type: 'POST',
                    url: 'crud/benhnhan_crud.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            $('#editPatientModal').modal('hide');
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
            
            // Load patient medical history
            $('.view-history').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                
                $('#history_patientName').text(name);
                $('#historyContent').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</p>');
                
                $.ajax({
                    type: 'POST',
                    url: 'crud/benhnhan_crud.php',
                    data: {
                        action: 'get_history',
                        patient_id: id
                    },
                    dataType: 'json',
                    success: function(history) {
                        var content = '';
                        
                        if (history.length > 0) {
                            content += '<div class="accordion" id="historyAccordion">';
                            
                            $.each(history, function(index, visit) {
                                content += '<div class="accordion-item mb-3">';
                                content += '<h2 class="accordion-header">';
                                content += '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' + index + '">';
                                content += formatDateFromSQL(visit.ngay_hen) + ' - ' + visit.gio_hen + ' - ';
                                
                                if (visit.trang_thai === 'completed') {
                                    content += '<span class="badge bg-success ms-2">Đã khám</span>';
                                } else if (visit.trang_thai === 'cancelled') {
                                    content += '<span class="badge bg-danger ms-2">Đã hủy</span>';
                                } else if (visit.trang_thai === 'confirmed') {
                                    content += '<span class="badge bg-primary ms-2">Đã xác nhận</span>';
                                } else {
                                    content += '<span class="badge bg-warning text-dark ms-2">Chờ xác nhận</span>';
                                }
                                
                                content += '</button></h2>';
                                content += '<div id="collapse' + index + '" class="accordion-collapse collapse" data-bs-parent="#historyAccordion">';
                                content += '<div class="accordion-body">';
                                
                                content += '<div class="row mb-3">';
                                content += '<div class="col-md-6">';
                                content += '<p><strong>Bác sĩ:</strong> ' + (visit.ten_bacsi || 'Không có thông tin') + '</p>';
                                content += '<p><strong>Dịch vụ:</strong> ' + (visit.ten_dichvu || 'Không có thông tin') + '</p>';
                                content += '</div>';
                                content += '<div class="col-md-6">';
                                content += '<p><strong>Triệu chứng:</strong> ' + (visit.trieu_chung || 'Không có thông tin') + '</p>';
                                content += '<p><strong>Ghi chú:</strong> ' + (visit.ghi_chu || 'Không có') + '</p>';
                                content += '</div>';
                                content += '</div>';
                                
                                if (visit.trang_thai === 'completed' && visit.chan_doan) {
                                    content += '<div class="medical-history-item">';
                                    content += '<h5>Kết quả khám</h5>';
                                    content += '<p><strong>Chẩn đoán:</strong> ' + visit.chan_doan + '</p>';
                                    
                                    if (visit.don_thuoc) {
                                        content += '<p><strong>Đơn thuốc:</strong> ';
                                        content += '<button class="btn btn-sm btn-info view-prescription" data-id="' + visit.id + '" data-bs-toggle="modal" data-bs-target="#prescriptionModal">';
                                        content += '<i class="fas fa-pills"></i> Xem chi tiết</button></p>';
                                    }
                                    
                                    content += '<p><strong>Ghi chú:</strong> ' + (visit.ghi_chu || 'Không có') + '</p>';
                                    content += '</div>';
                                }
                                
                                content += '</div></div></div>';
                            });
                            
                            content += '</div>';
                        } else {
                            content = '<div class="alert alert-info">';
                            content += '<i class="fas fa-info-circle me-2"></i>';
                            content += 'Bệnh nhân chưa có lịch sử khám bệnh nào.';
                            content += '</div>';
                        }
                        
                        $('#historyContent').html(content);
                        
                        // Attach prescription view event
                        $('.view-prescription').on('click', function() {
                            var appointmentId = $(this).data('id');
                            loadPrescription(appointmentId);
                        });
                    },
                    error: function() {
                        $('#historyContent').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải lịch sử khám bệnh.</div>');
                    }
                });
            });
            
            // Load prescription details
            function loadPrescription(appointmentId) {
                $('#prescriptionContent').html('<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</p>');
                
                $.ajax({
                    type: 'POST',
                    url: 'crud/benhnhan_crud.php',
                    data: {
                        action: 'get_prescription',
                        appointment_id: appointmentId
                    },
                    dataType: 'json',
                    success: function(prescription) {
                        var content = '';
                        
                        if (prescription.length > 0) {
                            content += '<table class="table table-striped">';
                            content += '<thead><tr>';
                            content += '<th>Thuốc</th>';
                            content += '<th>Liều dùng</th>';
                            content += '<th>Cách dùng</th>';
                            content += '<th>Đơn vị</th>';
                            content += '</tr></thead>';
                            content += '<tbody>';
                            
                            $.each(prescription, function(index, med) {
                                content += '<tr>';
                                content += '<td>' + med.ten_thuoc + '</td>';
                                content += '<td>' + med.lieu_dung + '</td>';
                                content += '<td>' + med.cach_dung + '</td>';
                                content += '<td>' + med.don_vi + '</td>';
                                content += '</tr>';
                            });
                            
                            content += '</tbody></table>';
                            
                            // Hiển thị hướng dẫn chung nếu có
                            if (prescription[0].huong_dan_chung) {
                                content += '<div class="prescription-item mt-3">';
                                content += '<h6>Hướng dẫn chung:</h6>';
                                content += '<p>' + prescription[0].huong_dan_chung + '</p>';
                                content += '</div>';
                            }
                        } else {
                            content = '<div class="alert alert-info">';
                            content += '<i class="fas fa-info-circle me-2"></i>';
                            content += 'Không có thông tin đơn thuốc.';
                            content += '</div>';
                        }
                        
                        $('#prescriptionContent').html(content);
                    },
                    error: function() {
                        $('#prescriptionContent').html('<div class="alert alert-danger">Có lỗi xảy ra khi tải thông tin đơn thuốc.</div>');
                    }
                });
            }
            
            // Helper function to format date from SQL
            function formatDateFromSQL(sqlDate) {
                if (!sqlDate) return 'Không có thông tin';
                
                var date = new Date(sqlDate);
                return date.getDate().toString().padStart(2, '0') + '/' +
                       (date.getMonth() + 1).toString().padStart(2, '0') + '/' +
                       date.getFullYear();
            }
            
            // Reset form when modal is closed
            $('#editPatientModal').on('hidden.bs.modal', function() {
                $('#editPatientForm')[0].reset();
            });
        });
    </script>
</body>
</html>
