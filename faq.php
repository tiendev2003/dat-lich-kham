<?php
// Start by including header.php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Câu hỏi thường gặp - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .faq-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/anh-gioithieu.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            text-align: center;
        }
        .faq-container {
            padding: 50px 0;
        }
        .faq-categories {
            margin-bottom: 40px;
        }
        .category-btn {
            padding: 10px 20px;
            margin: 5px;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .category-btn:hover, .category-btn.active {
            background-color: #0d6efd;
            color: white;
        }
        .faq-search {
            margin-bottom: 40px;
        }
        .faq-search input {
            border-radius: 50px;
            padding-left: 20px;
            padding-right: 50px;
            height: 50px;
        }
        .faq-search button {
            position: absolute;
            right: 5px;
            top: 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .accordion-item {
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e1f0ff;
            color: #0d6efd;
            box-shadow: none;
        }
        .accordion-button:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }
        .faq-category-title {
            margin: 40px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #0d6efd;
            display: inline-block;
        }
        .support-section {
            background-color: #f8f9fa;
            padding: 40px 0;
            margin-top: 50px;
            border-radius: 10px;
        }
        .support-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        .support-card:hover {
            transform: translateY(-5px);
        }
        .support-icon {
            width: 70px;
            height: 70px;
            background-color: #e1f0ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .support-icon i {
            font-size: 30px;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <!-- FAQ Header -->
    <section class="faq-header">
        <div class="container">
            <h1>Câu hỏi thường gặp</h1>
            <p class="lead">Tìm câu trả lời nhanh chóng cho các thắc mắc của bạn</p>
        </div>
    </section>

    <div class="container faq-container">
        <!-- Search & Categories -->
        <div class="row">
            <div class="col-lg-6 mx-auto mb-5">
                <div class="faq-search position-relative">
                    <input type="text" class="form-control" id="faqSearch" placeholder="Tìm kiếm câu hỏi...">
                    <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 text-center faq-categories">
                <button class="btn btn-outline-primary category-btn active" data-category="all">Tất cả</button>
                <button class="btn btn-outline-primary category-btn" data-category="booking">Đặt lịch</button>
                <button class="btn btn-outline-primary category-btn" data-category="payment">Thanh toán</button>
                <button class="btn btn-outline-primary category-btn" data-category="medical">Y tế & Điều trị</button>
                <button class="btn btn-outline-primary category-btn" data-category="account">Tài khoản</button>
                <button class="btn btn-outline-primary category-btn" data-category="general">Chung</button>
            </div>
        </div>

        <!-- FAQs -->
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <!-- Đặt lịch -->
                <h3 class="faq-category-title" id="booking">Đặt lịch khám bệnh</h3>
                <div class="accordion" id="bookingAccordion" data-category="booking">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingB1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB1" aria-expanded="true" aria-controls="collapseB1">
                                Làm thế nào để đặt lịch khám bệnh?
                            </button>
                        </h2>
                        <div id="collapseB1" class="accordion-collapse collapse show" aria-labelledby="headingB1" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                <p>Để đặt lịch khám bệnh, bạn có thể thực hiện theo các bước sau:</p>
                                <ol>
                                    <li>Đăng nhập vào tài khoản của bạn (nếu chưa có tài khoản, vui lòng đăng ký)</li>
                                    <li>Nhấp vào nút "Đặt lịch khám" trên menu chính</li>
                                    <li>Chọn chuyên khoa và bác sĩ mà bạn muốn đặt lịch</li>
                                    <li>Chọn ngày và giờ khám phù hợp</li>
                                    <li>Điền thông tin cần thiết và xác nhận đặt lịch</li>
                                    <li>Thanh toán (nếu cần) để hoàn tất quá trình đặt lịch</li>
                                </ol>
                                <p>Bạn sẽ nhận được email xác nhận sau khi đặt lịch thành công.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingB2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB2" aria-expanded="false" aria-controls="collapseB2">
                                Tôi có thể hủy hoặc thay đổi lịch hẹn không?
                            </button>
                        </h2>
                        <div id="collapseB2" class="accordion-collapse collapse" aria-labelledby="headingB2" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                <p>Có, bạn có thể hủy hoặc thay đổi lịch hẹn đã đặt theo các quy định sau:</p>
                                <ul>
                                    <li>Bạn có thể hủy hoặc thay đổi lịch hẹn trước 24 giờ so với thời gian khám mà không bị tính phí.</li>
                                    <li>Hủy lịch trong vòng 24 giờ trước thời gian khám có thể bị tính phí hủy muộn (100.000đ).</li>
                                    <li>Không đến khám theo lịch hẹn mà không thông báo sẽ bị tính toàn bộ phí khám.</li>
                                </ul>
                                <p>Để hủy hoặc thay đổi lịch hẹn, bạn có thể đăng nhập vào tài khoản, vào mục "Lịch hẹn của tôi" và chọn chức năng hủy hoặc thay đổi ở lịch hẹn tương ứng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingB3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB3" aria-expanded="false" aria-controls="collapseB3">
                                Tôi cần chuẩn bị gì khi đến khám bệnh?
                            </button>
                        </h2>
                        <div id="collapseB3" class="accordion-collapse collapse" aria-labelledby="headingB3" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                <p>Khi đến khám bệnh theo lịch hẹn, bạn nên chuẩn bị:</p>
                                <ul>
                                    <li>Giấy tờ tùy thân (CMND/CCCD/Hộ chiếu)</li>
                                    <li>Mã xác nhận lịch hẹn (gửi qua email hoặc SMS)</li>
                                    <li>Thẻ bảo hiểm y tế (nếu có)</li>
                                    <li>Các hồ sơ y tế liên quan đến tình trạng sức khỏe hiện tại (nếu có)</li>
                                    <li>Kết quả xét nghiệm hoặc hồ sơ khám bệnh trước đó (nếu có)</li>
                                    <li>Danh sách thuốc đang sử dụng (nếu có)</li>
                                </ul>
                                <p>Bạn nên đến trước thời gian hẹn 15-30 phút để hoàn thành các thủ tục đăng ký.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingB4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseB4" aria-expanded="false" aria-controls="collapseB4">
                                Tôi có thể đặt lịch khám cho người khác không?
                            </button>
                        </h2>
                        <div id="collapseB4" class="accordion-collapse collapse" aria-labelledby="headingB4" data-bs-parent="#bookingAccordion">
                            <div class="accordion-body">
                                <p>Có, bạn có thể đặt lịch khám cho người khác như thành viên gia đình hoặc bạn bè. Khi đặt lịch, bạn cần:</p>
                                <ul>
                                    <li>Chọn tùy chọn "Đặt lịch cho người khác" trong quá trình đặt lịch</li>
                                    <li>Nhập thông tin cá nhân của người sẽ đến khám</li>
                                    <li>Đảm bảo cung cấp thông tin chính xác để tránh sai sót</li>
                                </ul>
                                <p>Lưu ý: Người đến khám cần mang theo giấy tờ tùy thân để xác minh thông tin.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thanh toán -->
                <h3 class="faq-category-title" id="payment">Thanh toán & Bảo hiểm</h3>
                <div class="accordion" id="paymentAccordion" data-category="payment">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingP1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseP1" aria-expanded="true" aria-controls="collapseP1">
                                Có những phương thức thanh toán nào được chấp nhận?
                            </button>
                        </h2>
                        <div id="collapseP1" class="accordion-collapse collapse show" aria-labelledby="headingP1" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                <p>Chúng tôi chấp nhận nhiều phương thức thanh toán khác nhau để tạo thuận lợi cho bệnh nhân:</p>
                                <ul>
                                    <li>Thẻ tín dụng/ghi nợ (Visa, MasterCard, JCB)</li>
                                    <li>Chuyển khoản ngân hàng</li>
                                    <li>Ví điện tử (MoMo, ZaloPay, VNPay)</li>
                                    <li>Quét mã QR</li>
                                    <li>Tiền mặt (thanh toán tại quầy)</li>
                                </ul>
                                <p>Khi đặt lịch trực tuyến, bạn có thể chọn thanh toán trước hoặc thanh toán tại quầy khi đến khám.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingP2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseP2" aria-expanded="false" aria-controls="collapseP2">
                                Phòng khám có chấp nhận bảo hiểm y tế không?
                            </button>
                        </h2>
                        <div id="collapseP2" class="accordion-collapse collapse" aria-labelledby="headingP2" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                <p>Có, phòng khám chấp nhận bảo hiểm y tế nhà nước và nhiều loại bảo hiểm tư nhân:</p>
                                <ul>
                                    <li>Bảo hiểm y tế nhà nước: Áp dụng theo quy định của Bảo hiểm xã hội Việt Nam</li>
                                    <li>Bảo hiểm tư nhân: Bảo Việt, Prudential, Manulife, AIA, Liberty, v.v.</li>
                                </ul>
                                <p>Khi sử dụng bảo hiểm, vui lòng mang theo thẻ bảo hiểm và giấy tờ tùy thân. Tùy thuộc vào loại bảo hiểm và điều khoản của hợp đồng, mức độ chi trả có thể khác nhau.</p>
                                <p>Lưu ý: Một số dịch vụ đặc biệt có thể không được bảo hiểm chi trả hoặc chi trả một phần, bạn nên kiểm tra trước với công ty bảo hiểm của mình.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingP3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseP3" aria-expanded="false" aria-controls="collapseP3">
                                Chi phí khám bệnh như thế nào?
                            </button>
                        </h2>
                        <div id="collapseP3" class="accordion-collapse collapse" aria-labelledby="headingP3" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                <p>Chi phí khám bệnh phụ thuộc vào nhiều yếu tố như chuyên khoa, bác sĩ, loại dịch vụ và các xét nghiệm cần thiết:</p>
                                <ul>
                                    <li>Khám tổng quát: 200.000đ - 500.000đ</li>
                                    <li>Khám chuyên khoa: 300.000đ - 800.000đ</li>
                                    <li>Khám với bác sĩ chuyên gia/giáo sư: 800.000đ - 1.500.000đ</li>
                                    <li>Các xét nghiệm: Phụ thuộc vào loại xét nghiệm và số lượng</li>
                                </ul>
                                <p>Bạn có thể xem chi tiết phí khám của từng bác sĩ và chuyên khoa trong phần thông tin khi đặt lịch. Phí sẽ được hiển thị trước khi bạn xác nhận đặt lịch.</p>
                                <p>Lưu ý: Phí khám ban đầu không bao gồm các chi phí phát sinh trong quá trình khám như thuốc, xét nghiệm bổ sung, hoặc các thủ thuật được chỉ định thêm.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingP4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseP4" aria-expanded="false" aria-controls="collapseP4">
                                Làm thế nào để yêu cầu hóa đơn tài chính?
                            </button>
                        </h2>
                        <div id="collapseP4" class="accordion-collapse collapse" aria-labelledby="headingP4" data-bs-parent="#paymentAccordion">
                            <div class="accordion-body">
                                <p>Để yêu cầu hóa đơn tài chính (hóa đơn đỏ), bạn có thể thực hiện theo các bước sau:</p>
                                <ol>
                                    <li>Thông báo cho nhân viên tại quầy thanh toán rằng bạn cần hóa đơn tài chính</li>
                                    <li>Cung cấp thông tin chính xác của đơn vị/cá nhân yêu cầu xuất hóa đơn (tên công ty, địa chỉ, mã số thuế)</li>
                                    <li>Hóa đơn sẽ được xuất ngay tại thời điểm thanh toán hoặc gửi đến email của bạn trong vòng 3-5 ngày làm việc</li>
                                </ol>
                                <p>Lưu ý: Yêu cầu hóa đơn tài chính cần được thực hiện tại thời điểm thanh toán. Việc yêu cầu xuất hóa đơn sau khi đã thanh toán có thể gặp khó khăn và mất nhiều thời gian hơn.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Y tế & Điều trị -->
                <h3 class="faq-category-title" id="medical">Y tế & Điều trị</h3>
                <div class="accordion" id="medicalAccordion" data-category="medical">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingM1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseM1" aria-expanded="true" aria-controls="collapseM1">
                                Phòng khám có những chuyên khoa nào?
                            </button>
                        </h2>
                        <div id="collapseM1" class="accordion-collapse collapse show" aria-labelledby="headingM1" data-bs-parent="#medicalAccordion">
                            <div class="accordion-body">
                                <p>Phòng khám chúng tôi cung cấp nhiều chuyên khoa để đáp ứng nhu cầu đa dạng của bệnh nhân:</p>
                                <ul>
                                    <li>Nội khoa tổng quát</li>
                                    <li>Tim mạch</li>
                                    <li>Hô hấp</li>
                                    <li>Tiêu hóa</li>
                                    <li>Nhi khoa</li>
                                    <li>Da liễu</li>
                                    <li>Răng Hàm Mặt</li>
                                    <li>Tai Mũi Họng</li>
                                    <li>Mắt</li>
                                    <li>Sản phụ khoa</li>
                                    <li>Nam khoa</li>
                                    <li>Xương khớp</li>
                                    <li>Thần kinh</li>
                                </ul>
                                <p>Mỗi chuyên khoa đều có đội ngũ bác sĩ giàu kinh nghiệm và được trang bị các thiết bị y tế hiện đại để đảm bảo chất lượng dịch vụ khám chữa bệnh tốt nhất.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingM2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseM2" aria-expanded="false" aria-controls="collapseM2">
                                Tôi có thể nhận kết quả xét nghiệm bằng cách nào?
                            </button>
                        </h2>
                        <div id="collapseM2" class="accordion-collapse collapse" aria-labelledby="headingM2" data-bs-parent="#medicalAccordion">
                            <div class="accordion-body">
                                <p>Bạn có thể nhận kết quả xét nghiệm theo nhiều cách khác nhau tùy theo sở thích:</p>
                                <ul>
                                    <li><strong>Trực tiếp tại phòng khám:</strong> Nhận kết quả tại quầy tiếp nhận sau thời gian hoàn thành xét nghiệm</li>
                                    <li><strong>Email:</strong> Kết quả sẽ được gửi qua email đã đăng ký (dạng file PDF có mật khẩu bảo vệ)</li>
                                    <li><strong>Ứng dụng/Website:</strong> Xem kết quả trực tuyến thông qua tài khoản của bạn trên hệ thống của chúng tôi</li>
                                    <li><strong>Dịch vụ gửi bản cứng:</strong> Gửi kết quả qua đường bưu điện (có thể phát sinh phí gửi)</li>
                                </ul>
                                <p>Thời gian trả kết quả phụ thuộc vào loại xét nghiệm:</p>
                                <ul>
                                    <li>Xét nghiệm cơ bản: 2-24 giờ</li>
                                    <li>Xét nghiệm chuyên sâu: 3-7 ngày</li>
                                </ul>
                                <p>Để bảo mật thông tin y tế, chúng tôi chỉ cung cấp kết quả cho chính bệnh nhân hoặc người được ủy quyền (có giấy ủy quyền và giấy tờ tùy thân).</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingM3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseM3" aria-expanded="false" aria-controls="collapseM3">
                                Tôi nên khám sức khỏe tổng quát bao lâu một lần?
                            </button>
                        </h2>
                        <div id="collapseM3" class="accordion-collapse collapse" aria-labelledby="headingM3" data-bs-parent="#medicalAccordion">
                            <div class="accordion-body">
                                <p>Tần suất khám sức khỏe tổng quát phụ thuộc vào nhiều yếu tố như độ tuổi, tình trạng sức khỏe hiện tại, tiền sử bệnh và yếu tố nguy cơ:</p>
                                <ul>
                                    <li><strong>Người 18-39 tuổi:</strong> Mỗi 1-2 năm nếu khỏe mạnh</li>
                                    <li><strong>Người 40-49 tuổi:</strong> Mỗi năm một lần</li>
                                    <li><strong>Người 50 tuổi trở lên:</strong> Mỗi 6 tháng đến 1 năm</li>
                                    <li><strong>Người có bệnh mãn tính:</strong> Theo hướng dẫn của bác sĩ (thường 3-6 tháng/lần)</li>
                                    <li><strong>Phụ nữ mang thai:</strong> Theo lịch thai sản (thường 4 tuần/lần trong 3 tháng đầu, 2-3 tuần/lần trong 3 tháng giữa, và hàng tuần trong 3 tháng cuối)</li>
                                </ul>
                                <p>Ngoài khám định kỳ, bạn nên đi khám ngay khi có các dấu hiệu bất thường về sức khỏe. Phòng bệnh hơn chữa bệnh là cách tốt nhất để duy trì sức khỏe tốt.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tài khoản -->
                <h3 class="faq-category-title" id="account">Tài khoản & Bảo mật</h3>
                <div class="accordion" id="accountAccordion" data-category="account">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingA1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA1" aria-expanded="true" aria-controls="collapseA1">
                                Làm thế nào để đăng ký tài khoản?
                            </button>
                        </h2>
                        <div id="collapseA1" class="accordion-collapse collapse show" aria-labelledby="headingA1" data-bs-parent="#accountAccordion">
                            <div class="accordion-body">
                                <p>Để đăng ký tài khoản mới, bạn có thể thực hiện theo các bước sau:</p>
                                <ol>
                                    <li>Truy cập trang web của chúng tôi</li>
                                    <li>Nhấp vào nút "Đăng ký" ở góc phải trên cùng của trang</li>
                                    <li>Điền đầy đủ thông tin cá nhân theo yêu cầu (họ tên, email, số điện thoại)</li>
                                    <li>Tạo mật khẩu an toàn (nên bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt)</li>
                                    <li>Đọc và chấp nhận các điều khoản dịch vụ</li>
                                    <li>Nhấp vào nút "Đăng ký" để hoàn tất</li>
                                    <li>Xác nhận tài khoản qua email hoặc SMS</li>
                                </ol>
                                <p>Sau khi đăng ký và xác nhận thành công, bạn có thể đăng nhập và sử dụng đầy đủ các tính năng của hệ thống đặt lịch khám bệnh.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingA2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA2" aria-expanded="false" aria-controls="collapseA2">
                                Tôi quên mật khẩu, làm cách nào để đặt lại?
                            </button>
                        </h2>
                        <div id="collapseA2" class="accordion-collapse collapse" aria-labelledby="headingA2" data-bs-parent="#accountAccordion">
                            <div class="accordion-body">
                                <p>Nếu bạn quên mật khẩu, hãy làm theo các bước sau để đặt lại:</p>
                                <ol>
                                    <li>Truy cập trang đăng nhập</li>
                                    <li>Nhấp vào liên kết "Quên mật khẩu"</li>
                                    <li>Nhập địa chỉ email đã đăng ký tài khoản</li>
                                    <li>Kiểm tra email của bạn để nhận liên kết đặt lại mật khẩu</li>
                                    <li>Nhấp vào liên kết và tạo mật khẩu mới</li>
                                </ol>
                                <p>Lưu ý:</p>
                                <ul>
                                    <li>Liên kết đặt lại mật khẩu chỉ có hiệu lực trong 24 giờ</li>
                                    <li>Nếu không nhận được email, hãy kiểm tra thư mục spam/rác</li>
                                    <li>Nếu vẫn gặp vấn đề, vui lòng liên hệ bộ phận hỗ trợ qua số điện thoại 1900 1234</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingA3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseA3" aria-expanded="false" aria-controls="collapseA3">
                                Thông tin cá nhân và y tế của tôi được bảo vệ như thế nào?
                            </button>
                        </h2>
                        <div id="collapseA3" class="accordion-collapse collapse" aria-labelledby="headingA3" data-bs-parent="#accountAccordion">
                            <div class="accordion-body">
                                <p>Chúng tôi cam kết bảo vệ thông tin cá nhân và y tế của bạn với các biện pháp bảo mật sau:</p>
                                <ul>
                                    <li><strong>Mã hóa dữ liệu:</strong> Tất cả thông tin được mã hóa theo tiêu chuẩn SSL/TLS khi truyền tải</li>
                                    <li><strong>Kiểm soát truy cập:</strong> Chỉ nhân viên được ủy quyền mới có thể truy cập thông tin của bạn</li>
                                    <li><strong>Xác thực hai yếu tố:</strong> Bảo vệ tài khoản với lớp bảo mật bổ sung</li>
                                    <li><strong>Tuân thủ quy định:</strong> Hệ thống của chúng tôi tuân thủ các quy định về bảo vệ dữ liệu y tế</li>
                                    <li><strong>Backup dữ liệu:</strong> Sao lưu dữ liệu thường xuyên để đảm bảo an toàn thông tin</li>
                                    <li><strong>Đào tạo nhân viên:</strong> Nhân viên được đào tạo về quy trình bảo mật và bảo vệ thông tin</li>
                                </ul>
                                <p>Chúng tôi không chia sẻ thông tin của bạn với bên thứ ba khi chưa có sự đồng ý, trừ các trường hợp bắt buộc theo quy định pháp luật.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chung -->
                <h3 class="faq-category-title" id="general">Thông tin chung</h3>
                <div class="accordion" id="generalAccordion" data-category="general">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingG1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseG1" aria-expanded="true" aria-controls="collapseG1">
                                Thời gian làm việc của phòng khám?
                            </button>
                        </h2>
                        <div id="collapseG1" class="accordion-collapse collapse show" aria-labelledby="headingG1" data-bs-parent="#generalAccordion">
                            <div class="accordion-body">
                                <p>Phòng khám làm việc theo lịch sau:</p>
                                <ul>
                                    <li><strong>Thứ 2 - Thứ 6:</strong> 7:30 - 17:00</li>
                                    <li><strong>Thứ 7:</strong> 8:00 - 16:00</li>
                                    <li><strong>Chủ nhật:</strong> 8:00 - 12:00</li>
                                </ul>
                                <p><strong>Lưu ý về các dịp lễ:</strong></p>
                                <ul>
                                    <li>Trong các dịp lễ, tết, phòng khám có thể điều chỉnh giờ làm việc</li>
                                    <li>Thông tin về lịch làm việc trong các dịp lễ sẽ được thông báo trước trên website và tại phòng khám</li>
                                    <li>Dịch vụ cấp cứu luôn sẵn sàng 24/7</li>
                                </ul>
                                <p>Để biết thêm thông tin chi tiết hoặc đặt lịch ngoài giờ làm việc thông thường, vui lòng liên hệ tổng đài 1900 1234.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingG2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseG2" aria-expanded="false" aria-controls="collapseG2">
                                Phòng khám có đỗ xe không?
                            </button>
                        </h2>
                        <div id="collapseG2" class="accordion-collapse collapse" aria-labelledby="headingG2" data-bs-parent="#generalAccordion">
                            <div class="accordion-body">
                                <p>Có, phòng khám có bãi đỗ xe dành cho bệnh nhân và người nhà:</p>
                                <ul>
                                    <li><strong>Vị trí:</strong> Tầng hầm B1 và B2 của tòa nhà</li>
                                    <li><strong>Loại xe:</strong> Cả xe máy và ô tô</li>
                                    <li><strong>Phí gửi xe:</strong>
                                        <ul>
                                            <li>Xe máy: 5.000đ/lượt</li>
                                            <li>Ô tô: 20.000đ/lượt</li>
                                        </ul>
                                    </li>
                                    <li><strong>Miễn phí:</strong> Bệnh nhân có thẻ VIP hoặc đang cấp cứu được miễn phí gửi xe</li>
                                </ul>
                                <p>Ngoài ra, khu vực xung quanh phòng khám cũng có các bãi đỗ xe công cộng với mức phí khác nhau. Chúng tôi khuyến khích sử dụng phương tiện công cộng hoặc dịch vụ xe công nghệ để giảm áp lực giao thông.</p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingG3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseG3" aria-expanded="false" aria-controls="collapseG3">
                                Có dịch vụ khám tại nhà không?
                            </button>
                        </h2>
                        <div id="collapseG3" class="accordion-collapse collapse" aria-labelledby="headingG3" data-bs-parent="#generalAccordion">
                            <div class="accordion-body">
                                <p>Có, phòng khám cung cấp dịch vụ khám tại nhà cho một số trường hợp:</p>
                                <ul>
                                    <li><strong>Đối tượng áp dụng:</strong>
                                        <ul>
                                            <li>Người cao tuổi, khó khăn trong di chuyển</li>
                                            <li>Bệnh nhân sau phẫu thuật cần theo dõi</li>
                                            <li>Bệnh nhân mãn tính cần khám định kỳ</li>
                                            <li>Trẻ em và trường hợp đặc biệt khác</li>
                                        </ul>
                                    </li>
                                    <li><strong>Dịch vụ bao gồm:</strong>
                                        <ul>
                                            <li>Khám tổng quát tại nhà</li>
                                            <li>Lấy mẫu xét nghiệm</li>
                                            <li>Tiêm chủng tại nhà</li>
                                            <li>Thay băng, chăm sóc vết thương</li>
                                            <li>Vật lý trị liệu tại nhà</li>
                                        </ul>
                                    </li>
                                </ul>
                                <p>Để đặt lịch khám tại nhà, vui lòng liên hệ hotline 1900 1234 (Ext. 5) hoặc đặt qua website với tùy chọn "Khám tại nhà". Chi phí dịch vụ sẽ phụ thuộc vào loại dịch vụ, khoảng cách và yêu cầu cụ thể.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Section -->
        <div class="row support-section">
            <div class="col-md-12 text-center mb-4">
                <h2>Vẫn chưa tìm thấy câu trả lời?</h2>
                <p class="lead text-muted">Liên hệ với chúng tôi qua các kênh hỗ trợ sau</p>
            </div>
            <div class="col-md-4">
                <div class="support-card text-center">
                    <div class="support-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <h4 class="mb-3">Gọi điện</h4>
                    <p class="mb-3">Liên hệ tổng đài hỗ trợ 24/7 của chúng tôi</p>
                    <a href="tel:19001234" class="btn btn-primary">1900 1234</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="support-card text-center">
                    <div class="support-icon">
                        <i class="far fa-envelope"></i>
                    </div>
                    <h4 class="mb-3">Email</h4>
                    <p class="mb-3">Gửi email cho chúng tôi, chúng tôi sẽ phản hồi trong 24h</p>
                    <a href="mailto:support@locbinh.com" class="btn btn-primary">support@locbinh.com</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="support-card text-center">
                    <div class="support-icon">
                        <i class="far fa-comment-dots"></i>
                    </div>
                    <h4 class="mb-3">Chat trực tuyến</h4>
                    <p class="mb-3">Chat với nhân viên hỗ trợ của chúng tôi</p>
                    <button class="btn btn-primary" id="startChatBtn">Bắt đầu chat</button>
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
            // Filter FAQ by category
            const categoryBtns = document.querySelectorAll('.category-btn');
            const accordions = document.querySelectorAll('.accordion');
            
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Update active button
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    const category = this.getAttribute('data-category');
                    
                    // Show/hide accordions based on category
                    if (category === 'all') {
                        accordions.forEach(accordion => {
                            accordion.style.display = 'block';
                        });
                    } else {
                        accordions.forEach(accordion => {
                            if (accordion.getAttribute('data-category') === category) {
                                accordion.style.display = 'block';
                            } else {
                                accordion.style.display = 'none';
                            }
                        });
                    }

                    // Update section heading visibility
                    const sectionHeadings = document.querySelectorAll('.faq-category-title');
                    sectionHeadings.forEach(heading => {
                        if (category === 'all' || heading.id === category) {
                            heading.style.display = 'block';
                        } else {
                            heading.style.display = 'none';
                        }
                    });
                });
            });

            // Search functionality
            const searchInput = document.getElementById('faqSearch');
            const accordionItems = document.querySelectorAll('.accordion-item');
            
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                
                if (searchTerm.length > 2) {
                    // Reset category filters
                    categoryBtns.forEach(btn => btn.classList.remove('active'));
                    document.querySelector('[data-category="all"]').classList.add('active');
                    
                    // Show all sections
                    accordions.forEach(accordion => {
                        accordion.style.display = 'block';
                    });
                    
                    // Show all section headings
                    const sectionHeadings = document.querySelectorAll('.faq-category-title');
                    sectionHeadings.forEach(heading => {
                        heading.style.display = 'block';
                    });
                    
                    // Filter accordion items
                    accordionItems.forEach(item => {
                        const question = item.querySelector('.accordion-button').textContent.toLowerCase();
                        const answer = item.querySelector('.accordion-body').textContent.toLowerCase();
                        
                        if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                            item.style.display = 'block';
                            item.classList.add('search-match');
                            // Expand the item
                            const collapse = item.querySelector('.accordion-collapse');
                            collapse.classList.add('show');
                        } else {
                            item.style.display = 'none';
                            item.classList.remove('search-match');
                        }
                    });
                } else if (searchTerm.length === 0) {
                    // Reset to default view
                    accordionItems.forEach(item => {
                        item.style.display = 'block';
                        item.classList.remove('search-match');
                        // Collapse the items except the first one in each category
                        const collapse = item.querySelector('.accordion-collapse');
                        if (!item.querySelector('.accordion-button').classList.contains('first-item')) {
                            collapse.classList.remove('show');
                        }
                    });
                }
            });

            // Chat button functionality 
            document.getElementById('startChatBtn').addEventListener('click', function() {
                alert("Tính năng chat trực tuyến đang được phát triển. Vui lòng liên hệ qua điện thoại hoặc email để được hỗ trợ.");
            });
        });
    </script>
</body>
</html>