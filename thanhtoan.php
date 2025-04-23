<?php
// Start session
include 'includes/header.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php?redirect=thanhtoan.php');
    exit();
}

// Lấy thông tin đặt lịch từ URL hoặc session
// Trong một ứng dụng thực tế, bạn sẽ lấy thông tin từ database
// Ở đây tôi sẽ giả lập dữ liệu mẫu
$appointment_id = isset($_GET['id']) ? intval($_GET['id']) : 101;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/anh-gioithieu.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
            text-align: center;
            margin-bottom: 40px;
        }
        .payment-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 40px;
        }
        .appointment-summary {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .payment-method-card {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method-card:hover {
            border-color: #0d6efd;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .payment-method-card.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .payment-method-icon {
            width: 50px;
            height: 50px;
            background-color: #e1f0ff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        .payment-method-icon i {
            font-size: 24px;
            color: #0d6efd;
        }
        .payment-detail {
            margin-top: 30px;
            display: none;
        }
        .payment-detail.active {
            display: block;
        }
        .card-info {
            padding: 20px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
        }
        .bank-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .bank-item {
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .bank-item:hover {
            background-color: #f8f9fa;
        }
        .bank-item.selected {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .bank-logo {
            width: 60px;
            height: 40px;
            object-fit: contain;
        }
        .invoice-total {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .line-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: 600;
            color: #0d6efd;
        }
        .payment-confirmation {
            text-align: center;
            padding: 50px 0;
        }
        .confirmation-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .qr-code {
            max-width: 200px;
            margin: 0 auto 20px;
        }
        .timer {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Payment Header -->
    <section class="payment-header">
        <div class="container">
            <h1>Thanh toán</h1>
            <p class="lead">Hoàn tất đặt lịch khám bệnh của bạn</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row">
            <div class="col-md-12 mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="datlich.php">Đặt lịch</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="payment-container">
                    <h3 class="mb-4">Chi tiết đặt lịch khám</h3>
                    <div class="appointment-summary">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Chuyên khoa:</strong> Răng Hàm Mặt</p>
                                <p><strong>Bác sĩ:</strong> BS. Nguyễn Thế Lâm</p>
                                <p><strong>Ngày khám:</strong> 28/04/2025</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Thời gian:</strong> 09:30 - 10:00</p>
                                <p><strong>Địa điểm:</strong> Phòng khám số 3, Tầng 2</p>
                                <p><strong>Mã đặt lịch:</strong> #APT<?php echo $appointment_id; ?></p>
                            </div>
                        </div>
                    </div>

                    <h3 class="mb-4">Chọn phương thức thanh toán</h3>
                    <div id="paymentMethods">
                        <!-- Credit Card Payment -->
                        <div class="payment-method-card d-flex align-items-center" data-method="credit-card">
                            <div class="payment-method-icon">
                                <i class="far fa-credit-card"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Thẻ tín dụng/ghi nợ</h5>
                                <p class="mb-0 text-muted">Visa, MasterCard, JCB</p>
                            </div>
                            <div class="ms-auto">
                                <img src="https://via.placeholder.com/40x25" alt="Visa" class="me-2">
                                <img src="https://via.placeholder.com/40x25" alt="MasterCard" class="me-2">
                                <img src="https://via.placeholder.com/40x25" alt="JCB">
                            </div>
                        </div>

                        <!-- Bank Transfer Payment -->
                        <div class="payment-method-card d-flex align-items-center" data-method="bank-transfer">
                            <div class="payment-method-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Chuyển khoản ngân hàng</h5>
                                <p class="mb-0 text-muted">Chuyển khoản qua hệ thống ngân hàng</p>
                            </div>
                        </div>

                        <!-- E-wallet Payment -->
                        <div class="payment-method-card d-flex align-items-center" data-method="e-wallet">
                            <div class="payment-method-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Ví điện tử</h5>
                                <p class="mb-0 text-muted">MoMo, ZaloPay, VNPay</p>
                            </div>
                            <div class="ms-auto">
                                <img src="https://via.placeholder.com/40x25" alt="MoMo" class="me-2">
                                <img src="https://via.placeholder.com/40x25" alt="ZaloPay" class="me-2">
                                <img src="https://via.placeholder.com/40x25" alt="VNPay">
                            </div>
                        </div>

                        <!-- QR Code Payment -->
                        <div class="payment-method-card d-flex align-items-center" data-method="qr-code">
                            <div class="payment-method-icon">
                                <i class="fas fa-qrcode"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Quét mã QR</h5>
                                <p class="mb-0 text-muted">Quét QR bằng ứng dụng ngân hàng hoặc ví điện tử</p>
                            </div>
                        </div>

                        <!-- Cash Payment -->
                        <div class="payment-method-card d-flex align-items-center" data-method="cash">
                            <div class="payment-method-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Thanh toán tại quầy</h5>
                                <p class="mb-0 text-muted">Thanh toán bằng tiền mặt khi đến khám</p>
                            </div>
                        </div>
                    </div>

                    <!-- Credit Card Form -->
                    <div id="creditCardPayment" class="payment-detail">
                        <h4 class="mb-3">Thông tin thẻ</h4>
                        <div class="card-info">
                            <form id="creditCardForm">
                                <div class="mb-3">
                                    <label for="cardName" class="form-label">Tên chủ thẻ</label>
                                    <input type="text" class="form-control" id="cardName" placeholder="VD: NGUYEN VAN A">
                                </div>
                                <div class="mb-3">
                                    <label for="cardNumber" class="form-label">Số thẻ</label>
                                    <input type="text" class="form-control" id="cardNumber" placeholder="XXXX XXXX XXXX XXXX">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expiryDate" class="form-label">Ngày hết hạn</label>
                                        <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">CVV/CVC</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="XXX">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Bank Transfer Form -->
                    <div id="bankTransferPayment" class="payment-detail">
                        <h4 class="mb-3">Chọn ngân hàng</h4>
                        <div class="bank-list">
                            <div class="bank-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/60x40" alt="VietcomBank" class="bank-logo me-3">
                                <div>
                                    <h5 class="mb-1">Vietcombank</h5>
                                    <p class="mb-0 text-muted">Ngân hàng TMCP Ngoại thương Việt Nam</p>
                                </div>
                            </div>
                            <div class="bank-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/60x40" alt="VietinBank" class="bank-logo me-3">
                                <div>
                                    <h5 class="mb-1">VietinBank</h5>
                                    <p class="mb-0 text-muted">Ngân hàng TMCP Công thương Việt Nam</p>
                                </div>
                            </div>
                            <div class="bank-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/60x40" alt="BIDV" class="bank-logo me-3">
                                <div>
                                    <h5 class="mb-1">BIDV</h5>
                                    <p class="mb-0 text-muted">Ngân hàng TMCP Đầu tư và Phát triển Việt Nam</p>
                                </div>
                            </div>
                            <div class="bank-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/60x40" alt="Agribank" class="bank-logo me-3">
                                <div>
                                    <h5 class="mb-1">Agribank</h5>
                                    <p class="mb-0 text-muted">Ngân hàng Nông nghiệp và Phát triển Nông thôn Việt Nam</p>
                                </div>
                            </div>
                            <div class="bank-item d-flex align-items-center">
                                <img src="https://via.placeholder.com/60x40" alt="Techcombank" class="bank-logo me-3">
                                <div>
                                    <h5 class="mb-1">Techcombank</h5>
                                    <p class="mb-0 text-muted">Ngân hàng TMCP Kỹ thương Việt Nam</p>
                                </div>
                            </div>
                        </div>

                        <div class="bank-transfer-info mt-4 p-3 bg-light border rounded">
                            <h5>Thông tin chuyển khoản:</h5>
                            <p><strong>Số tài khoản:</strong> 0123 4567 8910</p>
                            <p><strong>Chủ tài khoản:</strong> CÔNG TY TNHH Y TẾ LỘC BÌNH</p>
                            <p><strong>Ngân hàng:</strong> Vietcombank - Chi nhánh Lạng Sơn</p>
                            <p><strong>Nội dung chuyển khoản:</strong> APT101 + Họ tên + SĐT</p>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-info-circle me-2"></i> Lưu ý: Vui lòng chuyển khoản với nội dung chính xác để chúng tôi có thể xác nhận thanh toán của bạn.
                            </div>
                        </div>
                    </div>

                    <!-- E-wallet Form -->
                    <div id="eWalletPayment" class="payment-detail">
                        <h4 class="mb-3">Chọn ví điện tử</h4>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card text-center p-3">
                                    <img src="https://via.placeholder.com/80x50" alt="MoMo" class="mx-auto mb-3">
                                    <h5>MoMo</h5>
                                    <button class="btn btn-primary mt-2">Chọn</button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card text-center p-3">
                                    <img src="https://via.placeholder.com/80x50" alt="ZaloPay" class="mx-auto mb-3">
                                    <h5>ZaloPay</h5>
                                    <button class="btn btn-primary mt-2">Chọn</button>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card text-center p-3">
                                    <img src="https://via.placeholder.com/80x50" alt="VNPay" class="mx-auto mb-3">
                                    <h5>VNPay</h5>
                                    <button class="btn btn-primary mt-2">Chọn</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Payment Form -->
                    <div id="qrCodePayment" class="payment-detail">
                        <div class="row">
                            <div class="col-md-6 offset-md-3">
                                <div class="card text-center p-4">
                                    <h4 class="mb-3">Quét mã QR để thanh toán</h4>
                                    <div class="qr-code">
                                        <img src="https://via.placeholder.com/200x200" alt="QR Code" class="img-fluid">
                                    </div>
                                    <div class="timer" id="qrCodeTimer">
                                        Mã QR sẽ hết hạn sau: <span id="countdownTime">15:00</span>
                                    </div>
                                    <p class="mb-0">Sử dụng ứng dụng ngân hàng hoặc ví điện tử để quét mã.</p>
                                    <p class="mb-0">Số tiền: <strong>300.000 VNĐ</strong></p>
                                    <button class="btn btn-secondary mt-3">Tôi đã thanh toán</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Payment Form -->
                    <div id="cashPayment" class="payment-detail">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Bạn đã chọn thanh toán bằng tiền mặt tại quầy. Vui lòng đến trước thời gian hẹn 15 phút để hoàn tất thủ tục thanh toán.
                        </div>
                        <div class="card p-3">
                            <h5>Thông tin lịch hẹn:</h5>
                            <p>Chuyên khoa: Răng Hàm Mặt</p>
                            <p>Bác sĩ: BS. Nguyễn Thế Lâm</p>
                            <p>Thời gian: 28/04/2025, 09:30 - 10:00</p>
                            <p>Địa điểm: Phòng khám số 3, Tầng 2</p>
                            <p>Phí khám: 300.000 VNĐ</p>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i> Lưu ý: Nếu bạn không đến đúng hẹn mà không thông báo trước, lịch hẹn sẽ bị hủy sau 15 phút và có thể phải đặt lịch lại.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="payment-container">
                    <h3 class="mb-4">Tóm tắt thanh toán</h3>
                    <div class="mb-4">
                        <div class="line-item">
                            <span>Phí khám bệnh</span>
                            <span>300.000 VNĐ</span>
                        </div>
                        <div class="line-item">
                            <span>Phí đặt lịch</span>
                            <span>0 VNĐ</span>
                        </div>
                        <div class="line-item">
                            <span>Giảm giá</span>
                            <span>0 VNĐ</span>
                        </div>
                        <hr>
                        <div class="line-item">
                            <span class="fw-bold">Tổng thanh toán</span>
                            <span class="total-amount">300.000 VNĐ</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="termsCheckbox" required>
                            <label class="form-check-label" for="termsCheckbox">
                                Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản dịch vụ</a>
                            </label>
                        </div>
                    </div>

                    <button id="confirmPaymentBtn" class="btn btn-primary btn-lg w-100 mb-3" disabled>
                        Xác nhận thanh toán
                    </button>

                    <button id="backButton" class="btn btn-outline-secondary w-100">
                        Quay lại
                    </button>
                </div>
            </div>
        </div>

        <!-- Payment Success Section (Initially Hidden) -->
        <div class="row payment-confirmation" id="paymentSuccess" style="display: none;">
            <div class="col-md-8 mx-auto">
                <div class="payment-container text-center">
                    <div class="confirmation-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="mb-4">Thanh toán thành công!</h2>
                    <p class="lead mb-4">Cảm ơn bạn đã đặt lịch và thanh toán. Lịch hẹn của bạn đã được xác nhận.</p>
                    
                    <div class="card mb-4 p-4">
                        <h5>Chi tiết lịch hẹn:</h5>
                        <p><strong>Mã đặt lịch:</strong> #APT<?php echo $appointment_id; ?></p>
                        <p><strong>Chuyên khoa:</strong> Răng Hàm Mặt</p>
                        <p><strong>Bác sĩ:</strong> BS. Nguyễn Thế Lâm</p>
                        <p><strong>Ngày khám:</strong> 28/04/2025</p>
                        <p><strong>Thời gian:</strong> 09:30 - 10:00</p>
                        <p><strong>Địa điểm:</strong> Phòng khám số 3, Tầng 2</p>
                        <p class="mb-0"><strong>Tổng thanh toán:</strong> 300.000 VNĐ</p>
                    </div>

                    <p>Chúng tôi đã gửi xác nhận chi tiết đến email của bạn.</p>
                    <p class="mb-4">Vui lòng đến trước giờ hẹn 15 phút và mang theo giấy tờ tùy thân.</p>
                    
                    <div class="d-flex justify-content-center">
                        <a href="lichsu_datlich.php" class="btn btn-outline-primary me-3">
                            <i class="fas fa-history me-2"></i> Xem lịch sử đặt lịch
                        </a>
                        <a href="index.php" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i> Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Điều khoản dịch vụ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>1. Quy định đặt lịch và thanh toán</h5>
                    <p>1.1. Khi đặt lịch khám, bạn cam kết cung cấp thông tin cá nhân chính xác và đầy đủ.</p>
                    <p>1.2. Phí khám sẽ được thanh toán trước hoặc tại quầy tùy theo lựa chọn của bạn.</p>
                    <p>1.3. Đối với thanh toán trực tuyến, hệ thống sẽ ghi nhận thanh toán thành công khi đã nhận được tiền từ ngân hàng hoặc đơn vị thanh toán.</p>

                    <h5>2. Chính sách hủy và hoàn tiền</h5>
                    <p>2.1. Bạn có thể hủy hoặc thay đổi lịch hẹn trước 24 giờ so với thời gian khám mà không bị tính phí.</p>
                    <p>2.2. Hủy lịch trong vòng 24 giờ trước thời gian khám có thể bị tính phí hủy muộn (100.000đ).</p>
                    <p>2.3. Không đến khám theo lịch hẹn mà không thông báo sẽ bị tính toàn bộ phí khám.</p>
                    <p>2.4. Hoàn tiền sẽ được thực hiện trong vòng 7-14 ngày làm việc tùy theo phương thức thanh toán ban đầu.</p>

                    <h5>3. Bảo mật thông tin</h5>
                    <p>3.1. Chúng tôi cam kết bảo mật thông tin cá nhân và thông tin thanh toán của bạn theo quy định của pháp luật.</p>
                    <p>3.2. Thông tin bệnh án và kết quả khám sẽ được bảo mật tuyệt đối và chỉ được chia sẻ khi có sự đồng ý của bạn hoặc theo yêu cầu của cơ quan chức năng.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="agreeTerms" data-bs-dismiss="modal">Tôi đồng ý</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle payment method selection
            const paymentMethods = document.querySelectorAll('.payment-method-card');
            const paymentDetails = document.querySelectorAll('.payment-detail');
            const confirmPaymentBtn = document.getElementById('confirmPaymentBtn');
            const termsCheckbox = document.getElementById('termsCheckbox');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove selected class from all methods
                    paymentMethods.forEach(m => m.classList.remove('selected'));
                    
                    // Add selected class to current method
                    this.classList.add('selected');
                    
                    // Hide all payment details
                    paymentDetails.forEach(detail => detail.classList.remove('active'));
                    
                    // Show selected payment detail
                    const selectedMethod = this.getAttribute('data-method');
                    document.getElementById(selectedMethod + 'Payment').classList.add('active');
                    
                    // Enable confirm button if terms are checked
                    if (termsCheckbox.checked) {
                        confirmPaymentBtn.disabled = false;
                    }
                });
            });
            
            // Handle terms checkbox
            termsCheckbox.addEventListener('change', function() {
                const anyMethodSelected = document.querySelector('.payment-method-card.selected');
                confirmPaymentBtn.disabled = !(this.checked && anyMethodSelected);
            });
            
            // Handle "I agree" button in terms modal
            document.getElementById('agreeTerms').addEventListener('click', function() {
                termsCheckbox.checked = true;
                const anyMethodSelected = document.querySelector('.payment-method-card.selected');
                confirmPaymentBtn.disabled = !anyMethodSelected;
            });
            
            // Handle bank selection
            const bankItems = document.querySelectorAll('.bank-item');
            bankItems.forEach(bank => {
                bank.addEventListener('click', function() {
                    bankItems.forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
            
            // Handle QR code countdown
            let timeLeft = 15 * 60; // 15 minutes in seconds
            const countdownElement = document.getElementById('countdownTime');
            
            function updateCountdown() {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                countdownElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    countdownElement.textContent = "00:00";
                    alert("Mã QR đã hết hạn! Vui lòng làm mới trang để tạo mã QR mới.");
                } else {
                    timeLeft--;
                }
            }
            
            const countdownInterval = setInterval(updateCountdown, 1000);
            
            // Handle confirm payment button
            confirmPaymentBtn.addEventListener('click', function() {
                // In a real application, you would process the payment here
                // For demo purposes, we'll just show the success message
                document.querySelector('.row:not(#paymentSuccess)').style.display = 'none';
                document.getElementById('paymentSuccess').style.display = 'block';
                
                // Scroll to top to show the success message
                window.scrollTo(0, 0);
            });
            
            // Handle back button
            document.getElementById('backButton').addEventListener('click', function() {
                window.location.href = 'datlich.php';
            });
        });
    </script>
</body>
</html>