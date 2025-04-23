<?php
// Start by including header.php
include 'includes/header.php';

// Xử lý gửi form liên hệ
$message = '';
$form_submitted = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Trong ứng dụng thực tế, bạn sẽ xử lý và gửi email ở đây
    // Ở đây chỉ giả lập thành công
    $form_submitted = true;
    $message = '<div class="alert alert-success">
                  <i class="fas fa-check-circle me-2"></i> Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong thời gian sớm nhất.
                </div>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .contact-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/anh-gioithieu.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 50px;
        }
        .contact-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
            height: 100%;
        }
        .contact-icon {
            width: 70px;
            height: 70px;
            background-color: #e1f0ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .contact-icon i {
            font-size: 30px;
            color: #0d6efd;
        }
        .contact-form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        .map-container {
            height: 450px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 50px;
        }
        .department-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .department-card:hover {
            transform: translateY(-5px);
        }
        .hours-table {
            width: 100%;
        }
        .hours-table td {
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .faq-link {
            background: linear-gradient(135deg, #0d6efd, #198754);
            padding: 40px 0;
            text-align: center;
            border-radius: 10px;
            color: white;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Contact Header -->
    <section class="contact-header">
        <div class="container">
            <h1>Liên hệ</h1>
            <p class="lead">Chúng tôi luôn sẵn sàng hỗ trợ bạn - Đừng ngần ngại liên hệ với chúng tôi</p>
        </div>
    </section>

    <div class="container mb-5">
        <div class="row">
            <!-- Thông tin liên hệ -->
            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4 class="mb-3">Địa chỉ</h4>
                    <p>123 Đường Lê Lợi, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h4 class="mb-3">Điện thoại</h4>
                    <p class="mb-2">Tổng đài: <a href="tel:19001234">1900 1234</a></p>
                    <p class="mb-2">Khẩn cấp: <a href="tel:0987654321">098 765 4321</a></p>
                    <p>Tư vấn: <a href="tel:0123456789">012 345 6789</a></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-card text-center">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h4 class="mb-3">Email</h4>
                    <p class="mb-2">Thông tin: <a href="mailto:info@locbinh.com">info@locbinh.com</a></p>
                    <p class="mb-2">Hỗ trợ: <a href="mailto:support@locbinh.com">support@locbinh.com</a></p>
                    <p>Tư vấn: <a href="mailto:tuvan@locbinh.com">tuvan@locbinh.com</a></p>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12 text-center mb-5">
                <h2>Gửi tin nhắn cho chúng tôi</h2>
                <p class="text-muted">Chúng tôi sẽ phản hồi trong thời gian sớm nhất</p>
            </div>
        </div>

        <div class="row">
            <!-- Form liên hệ -->
            <div class="col-md-8">
                <div class="contact-form">
                    <?php if (!empty($message) && $form_submitted): ?>
                        <?php echo $message; ?>
                    <?php endif; ?>

                    <form method="post" action="contact.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Họ và tên" required>
                                    <label for="name">Họ và tên</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Số điện thoại">
                                    <label for="phone">Số điện thoại</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="subject" name="subject">
                                        <option value="" selected disabled>Chọn chủ đề</option>
                                        <option value="Đặt lịch khám">Đặt lịch khám</option>
                                        <option value="Thông tin dịch vụ">Thông tin dịch vụ</option>
                                        <option value="Phản hồi dịch vụ">Phản hồi dịch vụ</option>
                                        <option value="Hỗ trợ kỹ thuật">Hỗ trợ kỹ thuật</option>
                                        <option value="Hợp tác">Hợp tác</option>
                                        <option value="Khác">Khác</option>
                                    </select>
                                    <label for="subject">Chủ đề</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="message" name="message" placeholder="Nội dung tin nhắn" style="height: 150px" required></textarea>
                                    <label for="message">Nội dung tin nhắn</label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                    <label class="form-check-label" for="privacy">
                                        Tôi đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">chính sách bảo mật</a> của phòng khám
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i> Gửi tin nhắn
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Thông tin giờ làm việc -->
            <div class="col-md-4">
                <div class="contact-card">
                    <h4 class="text-center mb-4"><i class="far fa-clock me-2"></i> Giờ làm việc</h4>
                    <table class="hours-table">
                        <tr>
                            <td><strong>Thứ 2 - Thứ 6:</strong></td>
                            <td class="text-end">7:30 - 17:00</td>
                        </tr>
                        <tr>
                            <td><strong>Thứ 7:</strong></td>
                            <td class="text-end">8:00 - 16:00</td>
                        </tr>
                        <tr>
                            <td><strong>Chủ nhật:</strong></td>
                            <td class="text-end">8:00 - 12:00</td>
                        </tr>
                    </table>

                    <h5 class="mt-4 mb-3 text-center">Các phòng ban</h5>
                    <div class="department-card">
                        <h6 class="mb-1">Tiếp nhận & Đặt lịch</h6>
                        <p class="mb-0 small"><i class="fas fa-phone-alt me-2 text-primary"></i> 1900 1234 (Ext. 1)</p>
                    </div>
                    <div class="department-card">
                        <h6 class="mb-1">Khoa Nội</h6>
                        <p class="mb-0 small"><i class="fas fa-phone-alt me-2 text-primary"></i> 1900 1234 (Ext. 2)</p>
                    </div>
                    <div class="department-card">
                        <h6 class="mb-1">Khoa Ngoại</h6>
                        <p class="mb-0 small"><i class="fas fa-phone-alt me-2 text-primary"></i> 1900 1234 (Ext. 3)</p>
                    </div>
                    <div class="department-card">
                        <h6 class="mb-1">Phòng thanh toán</h6>
                        <p class="mb-0 small"><i class="fas fa-phone-alt me-2 text-primary"></i> 1900 1234 (Ext. 4)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bản đồ -->
        <div class="row mt-5">
            <div class="col-md-12">
                <h2 class="text-center mb-4">Vị trí của chúng tôi</h2>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.4241674197106!2d106.69904361471821!3d10.777214492319669!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f48f54742dd%3A0x74efcf82d5e9849!2zMTIzIEzDqiBM4bujaSwgQuG6v24gTmdow6ksIFF14bqtbiAxLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1626765797172!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <!-- FAQ Link -->
        <div class="row">
            <div class="col-md-12">
                <div class="faq-link">
                    <h3 class="mb-3">Bạn có câu hỏi?</h3>
                    <p class="lead mb-4">Tìm câu trả lời nhanh chóng trong mục Câu hỏi thường gặp của chúng tôi</p>
                    <a href="faq.php" class="btn btn-light btn-lg">
                        <i class="far fa-question-circle me-2"></i> Xem câu hỏi thường gặp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Chính sách bảo mật</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5>1. Thu thập thông tin</h5>
                    <p>Chúng tôi thu thập thông tin cá nhân khi bạn điền vào biểu mẫu liên hệ, đặt lịch khám hoặc đăng ký tài khoản. Những thông tin này bao gồm họ tên, địa chỉ email, số điện thoại và các thông tin liên quan đến sức khỏe mà bạn cung cấp.</p>

                    <h5>2. Sử dụng thông tin</h5>
                    <p>Thông tin của bạn sẽ được sử dụng để:</p>
                    <ul>
                        <li>Phản hồi các yêu cầu và thắc mắc của bạn</li>
                        <li>Quản lý lịch hẹn khám bệnh</li>
                        <li>Cung cấp thông tin về dịch vụ y tế phù hợp</li>
                        <li>Gửi thông báo về các chương trình và dịch vụ mới</li>
                    </ul>

                    <h5>3. Bảo mật thông tin</h5>
                    <p>Chúng tôi cam kết bảo vệ thông tin cá nhân của bạn. Chúng tôi thực hiện các biện pháp an ninh thích hợp để bảo vệ thông tin khỏi việc truy cập, thay đổi, tiết lộ hoặc phá hủy trái phép.</p>

                    <h5>4. Chia sẻ thông tin</h5>
                    <p>Chúng tôi không bán, trao đổi hoặc chuyển giao thông tin cá nhân của bạn cho bên thứ ba. Điều này không bao gồm các bên thứ ba đáng tin cậy hỗ trợ chúng tôi vận hành trang web, tiến hành hoạt động kinh doanh hoặc phục vụ bạn, miễn là các bên đồng ý giữ bí mật thông tin này.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>