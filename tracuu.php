<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
include_once 'includes/page_banner.php';

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = "Tra cứu thông tin khám bệnh";

$error = '';
$medical_record = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ma_lichhen = trim($_POST['ma_lichhen'] ?? '');
    $sdt = trim($_POST['sdt'] ?? '');

    // Validate inputs
    if (empty($ma_lichhen) || empty($sdt)) {
        $error = "Vui lòng nhập đầy đủ thông tin tra cứu.";
    } else {
        // Get appointment info
        $stmt = $conn->prepare("
            SELECT l.*, bn.ho_ten AS patient_name, bn.dien_thoai, b.ho_ten AS doctor_name, 
                   c.ten_chuyenkhoa AS specialty_name, d.ten_dichvu AS service_name,
                   k.id AS record_id
            FROM lichhen l
            JOIN benhnhan bn ON l.benhnhan_id = bn.id
            JOIN bacsi b ON l.bacsi_id = b.id
            JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id
            LEFT JOIN dichvu d ON l.dichvu_id = d.id
            LEFT JOIN ketqua_kham k ON k.lichhen_id = l.id
            WHERE l.ma_lichhen = ? AND bn.dien_thoai = ?
        ");

        $stmt->bind_param("ss", $ma_lichhen, $sdt);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error = "Không tìm thấy thông tin khám bệnh phù hợp với dữ liệu bạn cung cấp.";
        } else {
            $medical_record = $result->fetch_assoc();
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        .search-section {
            position: relative;
            z-index: 1;
            margin-top: -50px;
        }

        .form-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .form-card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-header-custom {
            background: linear-gradient(to right, #005bac, #0077cc);
            color: white;
            border: none;
            padding: 20px;
        }

        .search-form-section {
            background: #fff;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }

        .form-control,
        .input-group-text {
            border-radius: 5px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(0, 91, 172, 0.2);
            border-color: #005bac;
        }

        .search-btn {
            background: #005bac;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .search-btn:hover {
            background: #004a8c;
            transform: translateY(-2px);
        }

        .help-icon {
            background-color: #f8f9fa;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: #005bac;
            font-size: 18px;
        }

        .guide-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .result-section {
            margin-top: 30px;
        }

        .result-card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .result-header {
            background: linear-gradient(to right, #28a745, #20c997);
            color: white;
            border: none;
            padding: 15px 20px;
        }

        .result-body {
            padding: 30px;
        }

        .detail-table th {
            width: 35%;
            background-color: #f8f9fa;
        }

        .action-btn {
            padding: 10px 24px;
            font-weight: 600;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .separator {
            height: 100%;
            width: 1px;
            background-color: rgba(0, 0, 0, 0.1);
            margin: 0 30px;
        }

        @media (max-width: 767px) {
            .separator {
                width: 100%;
                height: 1px;
                margin: 20px 0;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Banner -->
    <?php display_page_banner('Tra Cứu Thông Tin Khám Bệnh', 'Nhập mã lịch hẹn và số điện thoại để xem thông tin khám bệnh của bạn'); ?>

    <div class="container search-section">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="form-card shadow-sm mb-5">
                    <div class="card-header-custom">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-1 fw-bold"><i class="fas fa-search-plus me-2"></i>Tra cứu hồ sơ y tế</h4>
                                <p class="mb-0 text-white-50">Kiểm tra thông tin lịch hẹn và kết quả khám bệnh</p>
                            </div>
                            <div class="d-none d-md-block">
                                <i class="fas fa-file-medical fa-2x text-white-50"></i>
                            </div>
                        </div>
                    </div>
                    <div class="search-form-section">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="fas fa-exclamation-circle fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Lỗi tra cứu</h6>
                                            <p class="mb-0"><?php echo $error; ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="" class="mt-2">
                                    <div class="mb-4">
                                        <label for="ma_lichhen" class="form-label fw-bold">Mã lịch hẹn <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i
                                                    class="fas fa-ticket-alt text-primary"></i></span>
                                            <input type="text" class="form-control border-start-0 ps-0" id="ma_lichhen"
                                                name="ma_lichhen" placeholder="Nhập mã lịch hẹn (ví dụ: APT12345)"
                                                required>
                                        </div>
                                        <div class="form-text"><i class="fas fa-info-circle me-1 text-muted"></i>Mã lịch
                                            hẹn được cung cấp khi bạn đặt khám.</div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="sdt" class="form-label fw-bold">Số điện thoại <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i
                                                    class="fas fa-phone-alt text-primary"></i></span>
                                            <input type="text" class="form-control border-start-0 ps-0" id="sdt"
                                                name="sdt" placeholder="Số điện thoại đã đăng ký" required>
                                        </div>
                                        <div class="form-text"><i class="fas fa-info-circle me-1 text-muted"></i>Số điện
                                            thoại bạn đã dùng khi đăng ký khám bệnh.</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary search-btn w-100">
                                        <i class="fas fa-search me-2"></i> Tra cứu thông tin
                                    </button>
                                </form>
                            </div>
                            <div class="col-lg-6 position-relative">
                                <div class="d-none d-lg-block position-absolute start-0 top-0 h-100">
                                    <div class="separator"></div>
                                </div>
                                <div class="d-block d-lg-none my-4">
                                    <div class="separator"></div>
                                </div>
                                <div class="ps-lg-4">
                                    <h5 class="fw-bold mb-4 pb-2 border-bottom">Hướng dẫn tra cứu</h5>

                                    <div class="guide-item">
                                        <div class="help-icon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold">Mã lịch hẹn</p>
                                            <p class="small text-muted mb-0">Mã được cung cấp khi đặt lịch (định dạng:
                                                APT12345)</p>
                                        </div>
                                    </div>

                                    <div class="guide-item">
                                        <div class="help-icon">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold">Số điện thoại</p>
                                            <p class="small text-muted mb-0">Số điện thoại đã dùng khi đăng ký lịch hẹn
                                            </p>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-4">
                                        <div class="d-flex">
                                            <div class="me-3">
                                                <i class="fas fa-shield-alt fa-2x"></i>
                                            </div>
                                            <div>
                                                <h6 class="alert-heading fw-bold">Bảo mật thông tin</h6>
                                                <p class="mb-0">Thông tin y tế của bạn được bảo mật. Chỉ người có mã
                                                    lịch hẹn và số điện thoại chính xác mới có thể tra cứu.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="fw-bold text-primary"><i class="fas fa-headset me-2"></i>Bạn cần trợ
                                            giúp?</h6>
                                        <div class="d-flex mt-3">
                                            <div class="me-4">
                                                <p class="mb-1"><i class="fas fa-phone-alt me-2 text-primary"></i> 0987
                                                    654 321</p>
                                                <p class="mb-0 small text-muted">Hotline (7:00 - 20:00)</p>
                                            </div>
                                            <div>
                                                <p class="mb-1"><i class="fas fa-envelope me-2 text-primary"></i>
                                                    info@phongkhamlocbinh.vn</p>
                                                <p class="mb-0 small text-muted">Hỗ trợ 24/7</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($medical_record): ?>
                    <div class="result-section">
                        <div class="result-card mb-5">
                            <div class="result-header">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clipboard-check fa-2x me-3"></i>
                                    <div>
                                        <h5 class="mb-1 fw-bold">Kết quả tra cứu</h5>
                                        <p class="mb-0 text-white-50">Thông tin lịch hẹn và kết quả khám bệnh</p>
                                    </div>
                                </div>
                            </div>
                            <div class="result-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="border-bottom pb-2 mb-3 fw-bold"><i
                                                class="fas fa-calendar-check me-2 text-primary"></i>Thông tin lịch hẹn</h5>
                                        <table class="table table-hover detail-table">
                                            <tr>
                                                <th class="rounded-start">Mã lịch hẹn:</th>
                                                <td class="rounded-end"><span
                                                        class="fw-bold text-primary"><?php echo htmlspecialchars($medical_record['ma_lichhen']); ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="rounded-start">Tên bệnh nhân:</th>
                                                <td class="rounded-end">
                                                    <?php echo htmlspecialchars($medical_record['patient_name']); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="rounded-start">Ngày khám:</th>
                                                <td class="rounded-end">
                                                    <?php echo date('d/m/Y', strtotime($medical_record['ngay_hen'])); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="rounded-start">Giờ khám:</th>
                                                <td class="rounded-end">
                                                    <?php echo date('H:i', strtotime($medical_record['gio_hen'])); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="rounded-start">Bác sĩ:</th>
                                                <td class="rounded-end">BS.
                                                    <?php echo htmlspecialchars($medical_record['doctor_name']); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="rounded-start">Chuyên khoa:</th>
                                                <td class="rounded-end">
                                                    <?php echo htmlspecialchars($medical_record['specialty_name']); ?>
                                                </td>
                                            </tr>
                                            <?php if (!empty($medical_record['service_name'])): ?>
                                                <tr>
                                                    <th class="rounded-start">Dịch vụ:</th>
                                                    <td class="rounded-end">
                                                        <?php echo htmlspecialchars($medical_record['service_name']); ?>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <th class="rounded-start">Trạng thái:</th>
                                                <td class="rounded-end">
                                                    <span
                                                        class="badge rounded-pill px-3 py-2 <?php echo get_status_badge($medical_record['trang_thai']); ?>">
                                                        <i
                                                            class="<?php echo get_status_icon($medical_record['trang_thai']); ?> me-1"></i>
                                                        <?php echo get_status_name($medical_record['trang_thai']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-lg-6">
                                        <h5 class="border-bottom pb-2 mb-3 fw-bold"><i
                                                class="fas fa-file-medical me-2 text-primary"></i>Thông tin khám bệnh</h5>
                                        <?php if ($medical_record['record_id']): ?>
                                            <div class="alert alert-success d-flex">
                                                <div class="me-3">
                                                    <i class="fas fa-check-circle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-1">Đã có kết quả khám bệnh</h6>
                                                    <p class="mb-0">Kết quả khám bệnh của bạn đã được cập nhật vào hệ thống.</p>
                                                </div>
                                            </div>

                                            <div class="card bg-light mb-3">
                                                <div class="card-body">
                                                    <p class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Bạn có
                                                        thể xem chi tiết kết quả khám bệnh và đơn thuốc bằng cách nhấn vào nút
                                                        bên dưới.</p>
                                                </div>
                                            </div>

                                            <div class="mt-4 text-center">
                                                <a href="view_public_record.php?id=<?php echo $medical_record['record_id']; ?>&code=<?php echo md5($medical_record['ma_lichhen'] . $medical_record['dien_thoai']); ?>"
                                                    class="btn btn-primary action-btn">
                                                    <i class="fas fa-file-medical me-2"></i> Xem chi tiết kết quả khám bệnh
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <?php if ($medical_record['trang_thai'] === 'completed'): ?>
                                                <div class="alert alert-warning d-flex">
                                                    <div class="me-3">
                                                        <i class="fas fa-clock fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-1">Kết quả đang được cập nhật</h6>
                                                        <p class="mb-0">Kết quả khám bệnh của bạn đang được bác sĩ cập nhật. Vui
                                                            lòng kiểm tra lại sau.</p>
                                                    </div>
                                                </div>
                                            <?php elseif ($medical_record['trang_thai'] === 'confirmed' || $medical_record['trang_thai'] === 'pending'): ?>
                                                <div class="alert alert-info d-flex">
                                                    <div class="me-3">
                                                        <i class="fas fa-info-circle fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-1">Lịch hẹn chưa được thực hiện</h6>
                                                        <p class="mb-0">Buổi khám của bạn chưa diễn ra hoặc chưa hoàn thành. Kết quả
                                                            khám sẽ được cập nhật sau khi buổi khám kết thúc.</p>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-danger d-flex">
                                                    <div class="me-3">
                                                        <i class="fas fa-times-circle fa-2x"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-1">Lịch hẹn đã bị hủy</h6>
                                                        <p class="mb-0">Lịch hẹn này đã bị hủy nên không có kết quả khám bệnh.</p>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="text-center mt-4">
                                                <a href="datlich.php" class="btn btn-outline-primary action-btn">
                                                    <i class="fas fa-calendar-plus me-2"></i> Đặt lịch khám mới
                                                </a>
                                            </div>
                                        <?php endif; ?>

                                        <div class="card border-0 bg-light mt-4">
                                            <div class="card-body">
                                                <h6 class="fw-bold"><i class="fas fa-phone-alt me-2 text-primary"></i>Hỗ trợ
                                                    y tế</h6>
                                                <p class="small mb-0">Nếu bạn có thắc mắc về kết quả khám bệnh, vui lòng
                                                    liên hệ với phòng khám qua số điện thoại <strong>0987 654 321</strong>
                                                    để được tư vấn trực tiếp.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

<?php
// Di chuyển các hàm này vào includes/functions.php nếu chưa có
if (!function_exists('get_status_badge')) {
    function get_status_badge($status)
    {
        switch ($status) {
            case 'completed':
                return 'bg-success';
            case 'confirmed':
                return 'bg-primary';
            case 'pending':
                return 'bg-warning text-dark';
            case 'cancelled':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
}

if (!function_exists('get_status_name')) {
    function get_status_name($status)
    {
        switch ($status) {
            case 'completed':
                return 'Đã hoàn thành';
            case 'confirmed':
                return 'Đã xác nhận';
            case 'pending':
                return 'Chờ xác nhận';
            case 'cancelled':
                return 'Đã hủy';
            default:
                return 'Không xác định';
        }
    }
}

if (!function_exists('get_status_icon')) {
    function get_status_icon($status)
    {
        switch ($status) {
            case 'completed':
                return 'fas fa-check-circle';
            case 'confirmed':
                return 'fas fa-calendar-check';
            case 'pending':
                return 'fas fa-clock';
            case 'cancelled':
                return 'fas fa-times-circle';
            default:
                return 'fas fa-question-circle';
        }
    }
}
?>