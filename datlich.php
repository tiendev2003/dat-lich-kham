<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
$isLogged = is_logged_in();
if ($isLogged) {
    $user = get_logged_in_user();
    $patient = get_patient_info($user['id']);
}

// Fetch active services
$services = $conn->query("SELECT id, ten_dichvu FROM dichvu WHERE trangthai=1");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/datlich1.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>


    <div class="booking-container">
        <div class="container">
            <div class="booking-header">
                <h1>ĐẶT LỊCH KHÁM</h1>
                <!-- <div class="doctor-info">
                    <div class="doctor-avatar">
                        <img src="assets/img/bsi_rang.jpg" alt="Bác sĩ">
                    </div>
                    <div class="doctor-details">
                        <h2>PGS. TS. Nguyễn Thế Lâm</h2>
                        <div class="appointment-time">
                            <i class="far fa-calendar-alt"></i>
                            10:30 - 11:00 - Thứ 2 - 14/04/2025
                        </div>
                        <div class="clinic-address">
                            <i class="fas fa-hospital"></i>
                            Phòng khám Lộc Bình
                        </div>
                        <div class="full-address">
                            <i class="fas fa-map-marker-alt"></i>
                            67 Minh Khai, Lộc Bình, Lạng Sơn
                        </div>
                    </div>
                </div> -->
            </div>

            <div class="booking-form">
                <form action="process_booking.php" method="POST">
                    <?php if (!$isLogged): ?>
                        <div class="form-group">
                            <label for="fullname">
                                <i class="fas fa-user"></i>
                                Họ tên bệnh nhân (bắt buộc)
                            </label>
                            <input type="text" id="fullname" name="fullname" class="form-control" required>
                            <small class="form-text text-muted">Hãy ghi rõ Họ Và Tên, viết hoa những chữ cái đầu tiên, ví dụ: Trần Văn Phú</small>
                        </div>

                        <div class="form-group gender-group">
                            <div class="form-check form-check-inline">
                                <input type="radio" id="male" name="gender" value="Nam" class="form-check-input" required>
                                <label for="male" class="form-check-label">Nam</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" id="female" name="gender" value="Nữ" class="form-check-input">
                                <label for="female" class="form-check-label">Nữ</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">
                                <i class="fas fa-phone"></i>
                                Số điện thoại liên hệ (bắt buộc)
                            </label>
                            <input type="tel" id="phone" name="phone" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                Địa chỉ email
                            </label>
                            <input type="email" id="email" name="email" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="birthyear">
                                <i class="fas fa-calendar"></i>
                                Năm sinh (bắt buộc)
                            </label>
                            <input type="number" id="birthyear" name="birthyear" class="form-control" required>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="benhnhan_id" value="<?php echo $patient['id']; ?>">
                        <div class="alert alert-success">
                            Quý bệnh nhân: <strong><?php echo htmlspecialchars($patient['ho_ten']); ?></strong> đang đặt lịch.
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="dichvu">
                            <i class="fas fa-stethoscope"></i>
                            Chọn dịch vụ
                        </label>
                        <select id="dichvu" name="dichvu" class="form-control" required>
                            <option value="">-- Chọn dịch vụ --</option>
                            <?php while ($sv = $services->fetch_assoc()): ?>
                                <option value="<?php echo $sv['id']; ?>"><?php echo htmlspecialchars($sv['ten_dichvu']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="doctor">
                            <i class="fas fa-user-md"></i>
                            Chọn bác sĩ
                        </label>
                        <select id="doctor" name="doctor" class="form-control" required>
                            <option value="">-- Chọn bác sĩ --</option>
                            <option value="1">BS. Nguyễn Thế Lâm - Răng Hàm Mặt</option>
                            <option value="2">BS. Trần Thị Mai - Tim Mạch</option>
                            <option value="3">BS. Lê Văn Hùng - Hô Hấp</option>
                            <option value="4">BS. Phạm Thị Hoa - Da Liễu</option>
                            <option value="5">BS. Hoàng Văn Minh - Mắt</option>
                            <option value="6">BS. Vũ Thị Lan - Xét Nghiệm</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="appointment-date">
                            <i class="far fa-calendar-alt"></i>
                            Chọn ngày khám
                        </label>
                        <input type="date" id="appointment-date" name="appointment-date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="appointment-time">
                            <i class="far fa-clock"></i>
                            Chọn giờ khám
                        </label>
                        <select id="appointment-time" name="appointment-time" class="form-control" required>
                            <option value="">-- Chọn giờ khám --</option>
                            <option value="08:00">08:00 - 08:30</option>
                            <option value="08:30">08:30 - 09:00</option>
                            <option value="09:00">09:00 - 09:30</option>
                            <option value="09:30">09:30 - 10:00</option>
                            <option value="10:00">10:00 - 10:30</option>
                            <option value="10:30">10:30 - 11:00</option>
                            <option value="14:00">14:00 - 14:30</option>
                            <option value="14:30">14:30 - 15:00</option>
                            <option value="15:00">15:00 - 15:30</option>
                            <option value="15:30">15:30 - 16:00</option>
                            <option value="16:00">16:00 - 16:30</option>
                            <option value="16:30">16:30 - 17:00</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="province">
                            <i class="fas fa-map-marked-alt"></i>
                            Tỉnh/Thành
                        </label>
                        <select id="province" name="province" class="form-control" required>
                            <option value="">-- Chọn Tỉnh/Thành --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="district">
                            <i class="fas fa-map-marker-alt"></i>
                            Quận/Huyện
                        </label>
                        <select id="district" name="district" class="form-control" required disabled>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ward">
                            <i class="fas fa-home"></i>
                            Phường/Xã
                        </label>
                        <select id="ward" name="ward" class="form-control" required disabled>
                            <option value="">-- Chọn Phường/Xã --</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="address">
                            <i class="fas fa-home"></i>
                            Địa chỉ
                        </label>
                        <input type="text" id="address" name="address" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="reason">
                            <i class="fas fa-notes-medical"></i>
                            Triệu chứng
                        </label>
                        <textarea id="reason" name="reason" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="payment-info">
                        <h3>Hình thức thanh toán</h3>
                        <div class="form-check">
                            <input type="radio" id="payment_clinic" name="payment_method" value="clinic" class="form-check-input" checked>
                            <label for="payment_clinic" class="form-check-label">Thanh toán sau tại cơ sở y tế</label>
                        </div>

                        <div class="price-details">
                            <div class="price-row">
                                <span>Giá khám</span>
                                <span class="price">500.000đ</span>
                            </div>
                            <div class="price-row">
                                <span>Phí đặt lịch</span>
                                <span class="free">Miễn phí</span>
                            </div>
                            <div class="price-row total">
                                <span>Tổng cộng</span>
                                <span class="total-price">500.000đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="booking-note">
                        <div class="alert alert-info">
                            <h4>LƯU Ý</h4>
                            <p>Thông tin anh/chị cung cấp sẽ được sử dụng làm hồ sơ khám bệnh, khi điền thông tin anh/chị vui lòng:</p>
                            <ul>
                                <li>Ghi rõ họ và tên, viết hoa những chữ cái đầu tiên, ví dụ: Trần Văn Phú</li>
                                <li>Điền đầy đủ, đúng và vui lòng kiểm tra lại thông tin trước khi ấn "Xác nhận"</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Xác nhận đặt khám</button>
                    </div>

                    <div class="terms-agreement text-center">
                        <small>Bằng việc xác nhận đặt khám, bạn đã hoàn toàn đồng ý với <a href="#">Điều khoản sử dụng</a> dịch vụ của chúng tôi.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>

    <!-- Dynamic location scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinceSelect = document.getElementById('province');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        // Load provinces
        fetch('https://vapi.vnappmob.com/api/v2/province/')
            .then(res => res.json())
            .then(data => data.results.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.province_id;
                opt.textContent = item.province_name;
                provinceSelect.appendChild(opt);
            }));
        // On province change
        provinceSelect.addEventListener('change', function() {
            const pid = this.value;
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            wardSelect.disabled = true;
            if (!pid) { districtSelect.disabled = true; return; }
            districtSelect.disabled = false;
            fetch(`https://vapi.vnappmob.com/api/v2/province/district/${pid}`)
                .then(res => res.json())
                .then(data => data.results.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.district_id;
                    opt.textContent = item.district_name;
                    districtSelect.appendChild(opt);
                }));
        });
        // On district change
        districtSelect.addEventListener('change', function() {
            const did = this.value;
            wardSelect.innerHTML = '<option value="">-- Chọn Phường/Xã --</option>';
            if (!did) { wardSelect.disabled = true; return; }
            wardSelect.disabled = false;
            fetch(`https://vapi.vnappmob.com/api/v2/province/ward/${did}`)
                .then(res => res.json())
                .then(data => data.results.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.ward_id;
                    opt.textContent = item.ward_name;
                    wardSelect.appendChild(opt);
                }));
        });
    });
    </script>
</body>
</html>