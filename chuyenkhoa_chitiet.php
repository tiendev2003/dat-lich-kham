<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết chuyên khoa - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/chuyenkhoa_chitiet.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .specialty-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/img/rang.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0 60px;
            position: relative;
            text-align: center;
        }
        .specialty-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .specialty-content {
            padding: 50px 0;
        }
        .specialty-description {
            margin-bottom: 40px;
        }
        .specialty-feature {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        .specialty-feature:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: #0d6efd;
        }
        .doctor-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
        }
        .doctor-image {
            height: 250px;
            overflow: hidden;
        }
        .doctor-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .doctor-info {
            padding: 20px;
        }
        .doctor-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .doctor-specialty {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 12px;
        }
        .doctor-credentials {
            margin-bottom: 15px;
            font-size: 14px;
        }
        .doctor-rating {
            color: #ffc107;
            margin-bottom: 15px;
        }
        .service-card {
            padding: 30px;
            border-radius: 10px;
            background-color: #f8f9fa;
            margin-bottom: 30px;
            transition: transform 0.3s ease;
        }
        .service-card:hover {
            transform: translateY(-5px);
            background-color: #e9f0ff;
        }
        .service-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #e1f0ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .service-icon i {
            font-size: 30px;
            color: #0d6efd;
        }
        .service-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .service-price {
            font-weight: 600;
            margin-top: 15px;
            font-size: 16px;
        }
        .faq-section {
            padding: 40px 0;
            background-color: #f8f9fa;
        }
        .accordion-button:not(.collapsed) {
            background-color: #e1f0ff;
            color: #0d6efd;
        }
        .accordion-item {
            margin-bottom: 10px;
            border-radius: 8px;
            overflow: hidden;
        }
        .book-consultation {
            background-color: #0d6efd;
            color: white;
            padding: 40px 0;
            text-align: center;
        }
        .patient-review {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        .review-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .reviewer-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
        }
        .reviewer-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .reviewer-info {
            flex-grow: 1;
        }
        .reviewer-name {
            font-weight: 600;
            margin-bottom: 3px;
        }
        .review-date {
            font-size: 12px;
            color: #6c757d;
        }
        .review-rating {
            color: #ffc107;
        }
        .review-content {
            margin-top: 10px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Specialty Header -->
    <section class="specialty-header">
        <div class="container">
            <div class="specialty-icon">
                <i class="fas fa-tooth"></i>
            </div>
            <h1>Chuyên khoa Răng Hàm Mặt</h1>
            <p class="lead">Chăm sóc sức khỏe răng miệng toàn diện với đội ngũ bác sĩ giàu kinh nghiệm</p>
        </div>
    </section>

    <!-- Specialty Content -->
    <section class="specialty-content">
        <div class="container">
            <div class="specialty-description">
                <h2 class="mb-4">Giới thiệu Chuyên khoa Răng Hàm Mặt</h2>
                <div class="row">
                    <div class="col-lg-8">
                        <p>Chuyên khoa Răng Hàm Mặt tại Phòng Khám Lộc Bình là đơn vị khám và điều trị răng miệng hàng đầu, được trang bị hệ thống máy móc và thiết bị hiện đại. Với đội ngũ bác sĩ chuyên khoa có trình độ chuyên môn cao, giàu kinh nghiệm, chúng tôi cam kết mang đến cho bệnh nhân dịch vụ chăm sóc răng miệng chất lượng cao.</p>
                        <p>Mục tiêu của chúng tôi là giúp bệnh nhân có hàm răng khỏe mạnh và nụ cười đẹp tự nhiên. Chúng tôi áp dụng các phương pháp điều trị nha khoa tiên tiến, đảm bảo an toàn và hiệu quả, giúp bệnh nhân thoải mái và tự tin trong suốt quá trình điều trị.</p>
                        <p>Chuyên khoa Răng Hàm Mặt của chúng tôi cung cấp đầy đủ các dịch vụ từ thăm khám, tư vấn chăm sóc răng miệng, điều trị bệnh lý răng miệng cơ bản đến các dịch vụ chuyên sâu như phục hình răng, chỉnh hình răng và phẫu thuật hàm mặt.</p>
                    </div>
                    <div class="col-lg-4">
                        <img src="assets/img/rang.jpg" alt="Chuyên khoa Răng Hàm Mặt" class="img-fluid rounded">
                    </div>
                </div>
            </div>

            <!-- Specialty Features -->
            <h2 class="mb-4">Tại sao chọn Chuyên khoa Răng Hàm Mặt của chúng tôi?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="specialty-feature">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h4>Đội ngũ bác sĩ giàu kinh nghiệm</h4>
                        <p>Các bác sĩ của chúng tôi đều được đào tạo chuyên sâu và có nhiều năm kinh nghiệm trong lĩnh vực răng hàm mặt, đảm bảo chất lượng điều trị tốt nhất.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-feature">
                        <div class="feature-icon">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h4>Trang thiết bị hiện đại</h4>
                        <p>Chúng tôi đầu tư các trang thiết bị nha khoa hiện đại, giúp chẩn đoán chính xác và điều trị hiệu quả, giảm thiểu đau đớn cho bệnh nhân.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="specialty-feature">
                        <div class="feature-icon">
                            <i class="fas fa-procedures"></i>
                        </div>
                        <h4>Phương pháp điều trị tiên tiến</h4>
                        <p>Áp dụng các phương pháp điều trị nha khoa mới nhất, kết hợp với vật liệu chất lượng cao, đảm bảo kết quả điều trị bền vững và thẩm mỹ.</p>
                    </div>
                </div>
            </div>

            <!-- Doctors -->
            <h2 class="mt-5 mb-4">Đội ngũ bác sĩ</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_rang.jpg" alt="Bác sĩ Nguyễn Thế Lâm">
                        </div>
                        <div class="doctor-info">
                            <h3 class="doctor-name">BS. Nguyễn Thế Lâm</h3>
                            <div class="doctor-specialty">Chuyên khoa Răng Hàm Mặt</div>
                            <div class="doctor-credentials">
                                <i class="fas fa-graduation-cap me-2"></i> Đại học Y Hà Nội
                            </div>
                            <div class="doctor-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span class="ms-2">4.7/5 (120 đánh giá)</span>
                            </div>
                            <a href="chitiet_bacsi.php" class="btn btn-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_rang.jpg" alt="Bác sĩ Trần Minh Đức">
                        </div>
                        <div class="doctor-info">
                            <h3 class="doctor-name">BS. Trần Minh Đức</h3>
                            <div class="doctor-specialty">Chuyên khoa Răng Hàm Mặt</div>
                            <div class="doctor-credentials">
                                <i class="fas fa-graduation-cap me-2"></i> Đại học Y Dược TP. HCM
                            </div>
                            <div class="doctor-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span class="ms-2">4.2/5 (95 đánh giá)</span>
                            </div>
                            <a href="chitiet_bacsi.php" class="btn btn-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="doctor-card">
                        <div class="doctor-image">
                            <img src="assets/img/bsi_rang.jpg" alt="Bác sĩ Hoàng Thị Lan">
                        </div>
                        <div class="doctor-info">
                            <h3 class="doctor-name">BS. Hoàng Thị Lan</h3>
                            <div class="doctor-specialty">Chuyên khoa Răng Hàm Mặt</div>
                            <div class="doctor-credentials">
                                <i class="fas fa-graduation-cap me-2"></i> Đại học Y Thái Nguyên
                            </div>
                            <div class="doctor-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="ms-2">4.9/5 (78 đánh giá)</span>
                            </div>
                            <a href="chitiet_bacsi.php" class="btn btn-primary w-100">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <h2 class="mt-5 mb-4">Dịch vụ cung cấp</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-teeth"></i>
                        </div>
                        <h3 class="service-title">Khám và tư vấn sức khỏe răng miệng</h3>
                        <p>Thăm khám tổng quát, chẩn đoán tình trạng răng miệng và tư vấn phương pháp điều trị phù hợp.</p>
                        <div class="service-price">
                            Giá: 200.000đ - 300.000đ
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-tooth"></i>
                        </div>
                        <h3 class="service-title">Điều trị tủy răng</h3>
                        <p>Điều trị tủy răng bằng các phương pháp hiện đại, giảm đau và bảo tồn tối đa cấu trúc răng.</p>
                        <div class="service-price">
                            Giá: 800.000đ - 2.500.000đ
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-teeth-open"></i>
                        </div>
                        <h3 class="service-title">Nhổ răng an toàn</h3>
                        <p>Nhổ răng với kỹ thuật hiện đại, giảm thiểu đau đớn và biến chứng sau nhổ răng.</p>
                        <div class="service-price">
                            Giá: 300.000đ - 1.000.000đ
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-smile"></i>
                        </div>
                        <h3 class="service-title">Phục hình răng sứ</h3>
                        <p>Phục hình răng bằng sứ cao cấp, đảm bảo độ thẩm mỹ và chức năng ăn nhai tối ưu.</p>
                        <div class="service-price">
                            Giá: 2.000.000đ - 6.000.000đ/răng
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-broom"></i>
                        </div>
                        <h3 class="service-title">Lấy cao răng, đánh bóng</h3>
                        <p>Làm sạch cao răng, mảng bám và đánh bóng bề mặt răng, giúp răng trắng sáng tự nhiên.</p>
                        <div class="service-price">
                            Giá: 300.000đ - 500.000đ
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fas fa-teeth"></i>
                        </div>
                        <h3 class="service-title">Niềng răng chỉnh nha</h3>
                        <p>Điều trị chỉnh nha bằng các phương pháp hiện đại, giúp răng đều và đẹp hơn.</p>
                        <div class="service-price">
                            Giá: 30.000.000đ - 60.000.000đ
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Reviews -->
            <h2 class="mt-5 mb-4">Đánh giá từ khách hàng</h2>
            <div class="row">
                <div class="col-lg-6">
                    <div class="patient-review">
                        <div class="review-header">
                            <div class="reviewer-avatar">
                                <img src="assets/img/user-avatar.png" alt="Khách hàng">
                            </div>
                            <div class="reviewer-info">
                                <div class="reviewer-name">Nguyễn Thanh Tùng</div>
                                <div class="review-date">15/03/2025</div>
                            </div>
                            <div class="review-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <div class="review-content">
                            <p>"Tôi rất hài lòng với dịch vụ tại đây. Bác sĩ Lâm đã tư vấn cho tôi rất tận tình và thực hiện điều trị tủy răng gần như không đau. Nhân viên phòng khám rất thân thiện và chu đáo. Tôi sẽ tiếp tục sử dụng dịch vụ tại đây."</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="patient-review">
                        <div class="review-header">
                            <div class="reviewer-avatar">
                                <img src="assets/img/user-avatar.png" alt="Khách hàng">
                            </div>
                            <div class="reviewer-info">
                                <div class="reviewer-name">Trần Thu Hà</div>
                                <div class="review-date">20/02/2025</div>
                            </div>
                            <div class="review-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        <div class="review-content">
                            <p>"Phòng khám sạch sẽ, trang thiết bị hiện đại. Bác sĩ và nhân viên rất chuyên nghiệp. Tôi đã thực hiện bọc răng sứ tại đây và rất hài lòng với kết quả. Giá cả hợp lý so với chất lượng dịch vụ."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h2 class="mb-4 text-center">Câu hỏi thường gặp</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ Item 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Thời điểm nào nên đưa trẻ em đến khám răng lần đầu?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Theo khuyến cáo của các bác sĩ nha khoa, trẻ em nên được đưa đến khám răng lần đầu khi mọc chiếc răng sữa đầu tiên, hoặc không muộn hơn 1 tuổi. Việc khám răng sớm giúp phát hiện vấn đề về răng miệng từ sớm, tư vấn cho phụ huynh cách chăm sóc răng miệng cho trẻ đúng cách và giúp trẻ làm quen với việc đi khám răng định kỳ.
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Nên lấy cao răng bao lâu một lần?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Thông thường, bạn nên lấy cao răng 6 tháng một lần để duy trì sức khỏe răng miệng tốt. Tuy nhiên, tùy thuộc vào tình trạng răng miệng cá nhân, một số người có thể cần lấy cao răng thường xuyên hơn, đặc biệt là người hút thuốc, uống nhiều trà hoặc cà phê, hoặc có xu hướng tích tụ cao răng nhanh. Bác sĩ nha khoa sẽ tư vấn lịch lấy cao răng phù hợp cho từng người.
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Điều trị tủy răng có đau không?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Nhiều người lo ngại rằng điều trị tủy răng sẽ gây đau đớn, nhưng thực tế với kỹ thuật và thuốc tê hiện đại, quá trình điều trị tủy răng hầu như không gây đau đớn. Bác sĩ sẽ gây tê kỹ trước khi thực hiện thủ thuật, đảm bảo bệnh nhân không cảm thấy đau trong suốt quá trình điều trị. Sau khi thuốc tê hết tác dụng, có thể có cảm giác hơi nhức nhẹ, nhưng sẽ giảm dần và hết sau vài ngày.
                                </div>
                            </div>
                        </div>
                        <!-- FAQ Item 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Niềng răng mất bao lâu thì hoàn thành?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Thời gian niềng răng phụ thuộc vào mức độ phức tạp của từng trường hợp, thường dao động từ 12 đến 36 tháng. Trường hợp đơn giản như răng hơi xô lệch có thể hoàn thành trong 12-18 tháng, trong khi những trường hợp phức tạp như khớp cắn lệch, răng mọc lộn xộn nhiều có thể kéo dài 24-36 tháng. Việc tuân thủ hướng dẫn của bác sĩ, đeo duy trì và tái khám đúng lịch sẽ giúp quá trình điều trị hiệu quả và rút ngắn thời gian.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Book Consultation -->
    <section class="book-consultation">
        <div class="container">
            <h2 class="mb-4">Đặt lịch khám ngay hôm nay</h2>
            <p class="lead mb-4">Hãy đặt lịch để được các bác sĩ chuyên khoa Răng Hàm Mặt tư vấn và điều trị.</p>
            <a href="datlich.php" class="btn btn-light btn-lg">Đặt lịch ngay</a>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>