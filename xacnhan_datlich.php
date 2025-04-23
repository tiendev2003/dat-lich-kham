<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đặt lịch - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .confirm-container {
            padding: 40px 0;
        }
        .confirm-card {
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            background-color: #fff;
        }
        .confirm-header {
            background-color: #0d6efd;
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
        }
        .confirm-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .confirm-header p {
            margin-bottom: 0;
            font-size: 16px;
        }
        .confirm-success-icon {
            width: 80px;
            height: 80px;
            background-color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: #0d6efd;
            margin: 0 auto 15px;
            border: 3px solid #0d6efd;
        }
        .confirm-content {
            padding: 30px;
        }
        .appointment-summary {
            margin-bottom: 30px;
        }
        .summary-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .summary-item {
            display: flex;
            margin-bottom: 12px;
        }
        .summary-label {
            min-width: 180px;
            color: #6c757d;
            font-weight: 500;
        }
        .summary-value {
            font-weight: 500;
            flex-grow: 1;
        }
        .alert-info {
            border-left: 4px solid #0dcaf0;
        }
        .doctor-avatar {
            width: 70px;
            height: 70px;
            border-radius: 10px;
            overflow: hidden;
            margin-right: 15px;
        }
        .doctor-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .doctor-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .doctor-name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        .doctor-specialty {
            color: #6c757d;
            font-size: 14px;
        }
        .payment-methods {
            margin-top: 20px;
        }
        .payment-method {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method:hover {
            border-color: #0d6efd;
        }
        .payment-method.active {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }
        .payment-method-header {
            display: flex;
            align-items: center;
        }
        .payment-method-radio {
            margin-right: 10px;
        }
        .payment-method-logo {
            margin-right: 10px;
            font-size: 24px;
            width: 40px;
            text-align: center;
        }
        .payment-details {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #dee2e6;
            display: none;
        }
        .payment-method.active .payment-details {
            display: block;
        }
        .price-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .price-row.total {
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            margin-top: 10px;
            font-weight: 600;
            font-size: 18px;
        }
        .btn-group {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }
        @media (max-width: 768px) {
            .summary-item {
                flex-direction: column;
                margin-bottom: 20px;
            }
            .summary-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="container confirm-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="confirm-card">
                    <div class="confirm-header">
                        <div class="confirm-success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h1>Đặt lịch thành công!</h1>
                        <p>Cảm ơn bạn đã đặt lịch khám tại Phòng khám Lộc Bình</p>
                    </div>
                    <div class="confirm-content">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Lịch hẹn của bạn đã được ghi nhận và đang chờ xác nhận từ bác sĩ. Chúng tôi sẽ thông báo qua email và tin nhắn khi lịch hẹn được xác nhận.
                        </div>

                        <div class="appointment-summary">
                            <h2 class="summary-title">Chi tiết lịch hẹn</h2>

                            <div class="doctor-info">
                                <div class="doctor-avatar">
                                    <img src="assets/img/bsi_rang.jpg" alt="Bác sĩ">
                                </div>
                                <div>
                                    <div class="doctor-name">BS. Nguyễn Thế Lâm</div>
                                    <div class="doctor-specialty">Chuyên khoa Răng Hàm Mặt</div>
                                </div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label"><i class="far fa-calendar-alt me-2"></i>Ngày khám:</div>
                                <div class="summary-value">Thứ 2, 25/04/2025</div>
                            </div>
                            
                            <div class="summary-item">
                                <div class="summary-label"><i class="far fa-clock me-2"></i>Giờ khám:</div>
                                <div class="summary-value">10:30 - 11:00</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label"><i class="fas fa-hospital me-2"></i>Cơ sở y tế:</div>
                                <div class="summary-value">Phòng khám Lộc Bình</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ:</div>
                                <div class="summary-value">67 Minh Khai, Lộc Bình, Lạng Sơn</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label"><i class="fas fa-stethoscope me-2"></i>Dịch vụ khám:</div>
                                <div class="summary-value">Khám răng định kỳ</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label"><i class="fas fa-user me-2"></i>Bệnh nhân:</div>
                                <div class="summary-value">Nguyễn Văn A</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label"><i class="fas fa-phone me-2"></i>Số điện thoại:</div>
                                <div class="summary-value">0123456789</div>
                            </div>

                            <div class="summary-item">
                                <div class="summary-label"><i class="fas fa-notes-medical me-2"></i>Triệu chứng:</div>
                                <div class="summary-value">Đau răng hàm bên phải, ê buốt khi ăn đồ lạnh</div>
                            </div>
                        </div>

                        <div class="payment-summary">
                            <h2 class="summary-title">Thông tin thanh toán</h2>
                            
                            <div class="summary-item">
                                <div class="summary-label"><i class="fas fa-money-bill-wave me-2"></i>Hình thức thanh toán:</div>
                                <div class="summary-value">Thanh toán sau tại cơ sở y tế</div>
                            </div>

                            <div class="price-details">
                                <div class="price-row">
                                    <span>Giá khám</span>
                                    <span>500.000đ</span>
                                </div>
                                <div class="price-row">
                                    <span>Phí đặt lịch</span>
                                    <span class="text-success">Miễn phí</span>
                                </div>
                                <div class="price-row total">
                                    <span>Tổng cộng</span>
                                    <span>500.000đ</span>
                                </div>
                            </div>
                        </div>

                        <div class="btn-group">
                            <a href="lichsu_datlich.php" class="btn btn-outline-primary">
                                <i class="fas fa-history me-2"></i>Xem lịch sử đặt khám
                            </a>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>Về trang chủ
                            </a>
                        </div>

                        <div class="mt-4">
                            <p class="text-center text-muted">Mã lịch hẹn: <span class="fw-bold">APT25042025103</span></p>
                        </div>
                    </div>
                </div>

                <!-- Lưu ý quan trọng -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Lưu ý quan trọng</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Vui lòng đến trước giờ hẹn 15 phút để làm thủ tục</li>
                            <li>Mang theo CMND/CCCD và thẻ BHYT (nếu có)</li>
                            <li>Trường hợp không thể đến khám theo lịch hẹn, vui lòng hủy trước ít nhất 24 giờ</li>
                            <li>Để được hỗ trợ, vui lòng gọi Hotline: <strong>1900 1234</strong></li>
                        </ul>
                    </div>
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
            // Xử lý khi người dùng chọn phương thức thanh toán
            const paymentMethods = document.querySelectorAll('.payment-method');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove active class from all methods
                    paymentMethods.forEach(m => {
                        m.classList.remove('active');
                    });
                    
                    // Add active class to selected method
                    this.classList.add('active');
                    
                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                });
            });
        });
    </script>
</body>
</html>