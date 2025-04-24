<?php
// Kết nối đến database
require_once 'includes/db_connect.php';

// Khởi tạo các biến tìm kiếm
$doctor_name = isset($_GET['doctor_name']) ? trim($_GET['doctor_name']) : '';
$specialty_id = isset($_GET['specialty_id']) ? (int)$_GET['specialty_id'] : 0;

// Xây dựng câu truy vấn
$sql = "SELECT b.*, c.ten_chuyenkhoa 
        FROM bacsi b 
        JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id 
        WHERE 1=1 ";

$params = [];
if (!empty($doctor_name)) {
    $sql .= "AND (b.ho_ten LIKE ? OR b.mo_ta LIKE ?) ";
    $search_term = "%$doctor_name%";
    $params[] = $search_term;
    $params[] = $search_term;
}

if ($specialty_id > 0) {
    $sql .= "AND b.chuyenkhoa_id = ? ";
    $params[] = $specialty_id;
}

$sql .= "ORDER BY b.ho_ten ASC";

// Chuẩn bị và thực thi câu lệnh
$stmt = $conn->prepare($sql);

if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

// Lấy danh sách chuyên khoa cho dropdown lọc
$specialties_query = "SELECT * FROM chuyenkhoa ORDER BY ten_chuyenkhoa ASC";
$specialties_result = $conn->query($specialties_query);
$specialties = [];
if ($specialties_result && $specialties_result->num_rows > 0) {
    while ($row = $specialties_result->fetch_assoc()) {
        $specialties[] = $row;
    }
}

// Log kết quả tìm kiếm
$log_file = "logs/search_logs_" . date('Y-m') . ".log";
$log_message = date('Y-m-d H:i:s') . " | Tìm kiếm: '$doctor_name', Chuyên khoa ID: $specialty_id, Kết quả: " . count($doctors) . " bác sĩ\n";
file_put_contents($log_file, $log_message, FILE_APPEND);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm bác sĩ - Hệ thống đặt lịch khám bệnh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pages/doctors.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .search-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .doctor-card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            height: 100%;
        }
        .doctor-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        .doctor-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 15px;
        }
        .doctor-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .doctor-info {
            text-align: center;
        }
        .doctor-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        .doctor-specialty {
            color: #666;
            margin-bottom: 10px;
        }
        .doctor-buttons {
            margin-top: 15px;
            text-align: center;
        }
        .no-results {
            padding: 40px 0;
            text-align: center;
        }
        .no-results i {
            font-size: 50px;
            color: #ccc;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Search Results Section -->
    <section class="search-results py-5">
        <div class="container">
            <h1 class="text-center mb-4">Kết quả tìm kiếm bác sĩ</h1>
            
            <!-- Search Filters -->
            <div class="search-container">
                <form action="search.php" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label for="doctor_name" class="form-label">Tên bác sĩ</label>
                        <input type="text" class="form-control" id="doctor_name" name="doctor_name" 
                               value="<?php echo htmlspecialchars($doctor_name); ?>" placeholder="Nhập tên bác sĩ...">
                    </div>
                    <div class="col-md-5">
                        <label for="specialty_id" class="form-label">Chuyên khoa</label>
                        <select class="form-select" id="specialty_id" name="specialty_id">
                            <option value="">Tất cả chuyên khoa</option>
                            <?php foreach ($specialties as $specialty): ?>
                                <option value="<?php echo $specialty['id']; ?>" 
                                    <?php echo ($specialty_id == $specialty['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($specialty['ten_chuyenkhoa']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                    </div>
                </form>
            </div>

            <!-- Search Results -->
            <div class="search-results-container">
                <?php if (count($doctors) > 0): ?>
                    <h2 class="mb-3">Tìm thấy <?php echo count($doctors); ?> bác sĩ</h2>
                    <div class="row">
                        <?php foreach ($doctors as $doctor): ?>
                            <div class="col-md-3 mb-4">
                                <div class="doctor-card">
                                    <div class="doctor-image">
                                        <?php if (!empty($doctor['hinh_anh'])): ?>
                                            <img src="<?php echo htmlspecialchars($doctor['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($doctor['ho_ten']); ?>">
                                        <?php else: ?>
                                            <img src="assets/img/bacsi/default-doctor.png" alt="Doctor Image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="doctor-info">
                                        <h3 class="doctor-name"><?php echo htmlspecialchars($doctor['ho_ten']); ?></h3>
                                        <p class="doctor-specialty"><?php echo htmlspecialchars($doctor['ten_chuyenkhoa']); ?></p>
                                        <?php if (!empty($doctor['kinh_nghiem'])): ?>
                                            <p><strong>Kinh nghiệm:</strong> <?php echo htmlspecialchars($doctor['kinh_nghiem']); ?> năm</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="doctor-buttons">
                                        <a href="chitiet_bacsi.php?id=<?php echo $doctor['id']; ?>" class="btn btn-outline-primary">Xem chi tiết</a>
                                        <a href="datlich.php?doctor_id=<?php echo $doctor['id']; ?>" class="btn btn-primary mt-2">Đặt lịch</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search"></i>
                        <h3>Không tìm thấy kết quả</h3>
                        <p>Vui lòng thử tìm kiếm với các từ khóa khác hoặc xem danh sách <a href="bacsi.php">tất cả bác sĩ</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>