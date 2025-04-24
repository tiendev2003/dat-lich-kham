<?php
// Kết nối database
$db_already_connected = false;
require_once 'admin/includes/db_connect.php';

// Lấy danh sách chuyên khoa
$sql = "SELECT * FROM chuyenkhoa ORDER BY ten_chuyenkhoa";
$result = $conn->query($sql);
$specialties = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $specialties[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuyên khoa - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/chuyenkhoa.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Specialties Section -->
    <section class="specialties">
        <div class="container">
            <h1 class="page-title">Chuyên khoa</h1>
            
            <?php if (count($specialties) > 0): ?>
            <div class="row">
                <?php foreach ($specialties as $specialty): ?>
                <div class="col-md-4 mb-4">
                    <div class="specialty-card text-center">
                        <div class="specialty-icon mb-3">
                            <?php if (!empty($specialty['hinh_anh'])): ?>
                                <img src="<?= $specialty['hinh_anh'] ?>" alt="<?= $specialty['ten_chuyenkhoa'] ?>">
                            <?php else: ?>
                                <i class="fas <?= !empty($specialty['icon']) ? $specialty['icon'] : 'fa-stethoscope' ?>"></i>
                            <?php endif; ?>
                        </div>
                        <h3><?= $specialty['ten_chuyenkhoa'] ?></h3>
                        <p><?= !empty($specialty['mota']) ? substr(strip_tags($specialty['mota']), 0, 100) . '...' : 'Chăm sóc và điều trị các bệnh lý liên quan.' ?></p>
                        <a href="chuyenkhoa_chitiet.php?id=<?= $specialty['id'] ?>" class="btn btn-outline-primary">Xem chi tiết</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center">
                Hiện chưa có thông tin về chuyên khoa. Vui lòng quay lại sau.
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>