<?php
// Chỉ kết nối database nếu chưa được kết nối trước đó
if (!isset($db_already_connected) || $db_already_connected !== true) {
    // Kiểm tra các vị trí có thể của file db_connect.php
    $possible_paths = [
        '../includes/db_connect.php',           // Khi gọi từ AJAX trong admin
        '../../includes/db_connect.php',        // Nếu có thư mục includes ở thư mục gốc
        dirname(__DIR__) . '/includes/db_connect.php' // Sử dụng đường dẫn tuyệt đối
    ];
    
    $connected = false;
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $connected = true;
            break;
        }
    }
    
    if (!$connected) {
        die("Không thể kết nối đến cơ sở dữ liệu. Vui lòng kiểm tra đường dẫn.");
    }
}

/**
 * Lấy tất cả bác sĩ từ cơ sở dữ liệu
 */
function getAllDoctors($filter = [], $page = 1, $limit = 10) {
    global $conn;
    
    $whereClause = "WHERE 1";
    
    // Áp dụng bộ lọc
    if (!empty($filter['specialty_id'])) {
        $specialtyId = $conn->real_escape_string($filter['specialty_id']);
        $whereClause .= " AND bs.chuyenkhoa_id = '$specialtyId'";
    }
    
    if (!empty($filter['search'])) {
        $search = $conn->real_escape_string($filter['search']);
        $whereClause .= " AND (bs.ho_ten LIKE '%$search%' OR bs.email LIKE '%$search%' OR bs.dien_thoai LIKE '%$search%')";
    }
    
    // Tính offset cho phân trang
    $offset = ($page - 1) * $limit;
    
    $sql = "SELECT bs.*, ck.ten_chuyenkhoa,
           (SELECT COUNT(*) FROM lichhen lh WHERE lh.bacsi_id = bs.id) as so_lichhen
           FROM bacsi bs
           LEFT JOIN chuyenkhoa ck ON bs.chuyenkhoa_id = ck.id
           $whereClause
           ORDER BY bs.id DESC
           LIMIT $limit OFFSET $offset";
    
    $result = $conn->query($sql);
    $doctors = [];
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }
    
    return $doctors;
}

/**
 * Đếm tổng số bác sĩ theo điều kiện lọc
 */
function countDoctors($filter = []) {
    global $conn;
    
    $whereClause = "WHERE 1";
    
    // Áp dụng bộ lọc
    if (!empty($filter['specialty_id'])) {
        $specialtyId = $conn->real_escape_string($filter['specialty_id']);
        $whereClause .= " AND bs.chuyenkhoa_id = '$specialtyId'";
    }
    
    if (!empty($filter['search'])) {
        $search = $conn->real_escape_string($filter['search']);
        $whereClause .= " AND (bs.ho_ten LIKE '%$search%' OR bs.email LIKE '%$search%' OR bs.dien_thoai LIKE '%$search%')";
    }
    
    $sql = "SELECT COUNT(*) as total FROM bacsi bs $whereClause";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc()['total'];
    }
    
    return 0;
}

/**
 * Lấy thông tin một bác sĩ theo ID
 */
function getDoctorById($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    $sql = "SELECT bs.*, ck.ten_chuyenkhoa 
            FROM bacsi bs
            LEFT JOIN chuyenkhoa ck ON bs.chuyenkhoa_id = ck.id
            WHERE bs.id = '$id'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Lấy thông tin đầy đủ của bác sĩ bao gồm thống kê lịch hẹn
 */
function getFullDoctorInfo($id) {
    global $conn;
    
    // Lấy thông tin cơ bản của bác sĩ
    $doctor = getDoctorById($id);
    
    if (!$doctor) {
        return ["error" => "Không tìm thấy bác sĩ"];
    }
    
    // Thống kê lịch hẹn
    $id = $conn->real_escape_string($id);
    
    // Tổng số lịch hẹn
    $sql_total = "SELECT COUNT(*) as total FROM lichhen WHERE bacsi_id = '$id'";
    $result_total = $conn->query($sql_total);
    $total = $result_total ? $result_total->fetch_assoc()['total'] : 0;
    
    // Số lịch hẹn đã hoàn thành
    $sql_completed = "SELECT COUNT(*) as completed FROM lichhen WHERE bacsi_id = '$id' AND trang_thai = 'hoanthanh'";
    $result_completed = $conn->query($sql_completed);
    $completed = $result_completed ? $result_completed->fetch_assoc()['completed'] : 0;
    
    // Số lịch hẹn sắp tới - Sửa tên cột "ngay" thành "ngay_hen"
    $sql_upcoming = "SELECT COUNT(*) as upcoming FROM lichhen WHERE bacsi_id = '$id' AND trang_thai = 'xacnhan' AND ngay_hen >= CURDATE()";
    $result_upcoming = $conn->query($sql_upcoming);
    $upcoming = $result_upcoming ? $result_upcoming->fetch_assoc()['upcoming'] : 0;
    
    // Thêm thông tin thống kê vào kết quả
    $doctor['total_appointments'] = $total;
    $doctor['completed_appointments'] = $completed;
    $doctor['upcoming_appointments'] = $upcoming;
    
    return $doctor;
}

/**
 * Lấy danh sách tất cả chuyên khoa
 */
function getAllSpecialties() {
    global $conn;
    
    $sql = "SELECT * FROM chuyenkhoa ORDER BY ten_chuyenkhoa ASC";
    $result = $conn->query($sql);
    $specialties = [];
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $specialties[] = $row;
        }
    }
    
    return $specialties;
}

/**
 * Thêm bác sĩ mới
 */
function addDoctor($data, $image) {
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $hoTen = $conn->real_escape_string($data['hoTen']);
    $chuyenKhoaId = $conn->real_escape_string($data['chuyenKhoaId']);
    $namSinh = $conn->real_escape_string($data['namSinh']);
    $gioiTinh = $conn->real_escape_string($data['gioiTinh']);
    $dienThoai = $conn->real_escape_string($data['dienThoai']);
    $email = $conn->real_escape_string($data['email']);
    $diaChi = $conn->real_escape_string($data['diaChi']);
    $moTa = $conn->real_escape_string($data['moTa']);
    $bangCap = $conn->real_escape_string($data['bangCap']);
    $kinhNghiem = $conn->real_escape_string($data['kinhNghiem']);
    
    // Kiểm tra email đã tồn tại chưa
    if (!empty($email)) {
        $sql_check = "SELECT id FROM bacsi WHERE email = '$email'";
        $result_check = $conn->query($sql_check);
        if ($result_check && $result_check->num_rows > 0) {
            return ["success" => false, "message" => "Email đã tồn tại trong hệ thống"];
        }
    }
    
    // Xử lý upload hình ảnh nếu có
    $hinhAnh = '';
    if ($image['name'] != '') {
        $target_dir = "../../assets/img/bacsi/";
        $fileName = time() . '_' . basename($image["name"]);
        $target_file = $target_dir . $fileName;
        
        // Kiểm tra thư mục đích
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Kiểm tra loại file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return ["success" => false, "message" => "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF."];
        }
        
        // Upload file
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $hinhAnh = "assets/img/bacsi/" . $fileName;
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }
    
    // Thêm vào cơ sở dữ liệu
    $sql = "INSERT INTO bacsi (ho_ten, chuyenkhoa_id, nam_sinh, gioi_tinh, dien_thoai, 
            email, dia_chi, hinh_anh, mo_ta, bang_cap, kinh_nghiem, ngay_tao) 
            VALUES ('$hoTen', '$chuyenKhoaId', '$namSinh', '$gioiTinh', '$dienThoai', 
            '$email', '$diaChi', '$hinhAnh', '$moTa', '$bangCap', '$kinhNghiem', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        $doctorId = $conn->insert_id;
        
        // Tạo tài khoản người dùng nếu yêu cầu
        if (!empty($data['taoTaiKhoan']) && $data['taoTaiKhoan'] == '1' && !empty($email)) {
            $matKhauMacDinh = generateRandomPassword();
            $matKhauHash = password_hash($matKhauMacDinh, PASSWORD_DEFAULT);
            
            $sql_user = "INSERT INTO nguoidung (email, mat_khau, vai_tro) VALUES ('$email', '$matKhauHash', 'bacsi')";
            
            if ($conn->query($sql_user) === TRUE) {
                $userId = $conn->insert_id;
                
                // Cập nhật nguoidung_id cho bác sĩ
                $sql_update = "UPDATE bacsi SET nguoidung_id = '$userId' WHERE id = '$doctorId'";
                $conn->query($sql_update);
                
                return [
                    "success" => true, 
                    "message" => "Thêm bác sĩ thành công và tạo tài khoản thành công.", 
                    "password" => $matKhauMacDinh
                ];
            } else {
                return [
                    "success" => true, 
                    "message" => "Thêm bác sĩ thành công nhưng không thể tạo tài khoản: " . $conn->error
                ];
            }
        }
        
        return ["success" => true, "message" => "Thêm bác sĩ thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Cập nhật bác sĩ
 */
function updateDoctor($id, $data, $image) {
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $id = $conn->real_escape_string($id);
    $hoTen = $conn->real_escape_string($data['hoTen']);
    $chuyenKhoaId = $conn->real_escape_string($data['chuyenKhoaId']);
    $namSinh = $conn->real_escape_string($data['namSinh']);
    $gioiTinh = $conn->real_escape_string($data['gioiTinh']);
    $dienThoai = $conn->real_escape_string($data['dienThoai']);
    $email = $conn->real_escape_string($data['email']);
    $diaChi = $conn->real_escape_string($data['diaChi']);
    $moTa = $conn->real_escape_string($data['moTa']);
    $bangCap = $conn->real_escape_string($data['bangCap']);
    $kinhNghiem = $conn->real_escape_string($data['kinhNghiem']);
    
    // Kiểm tra email đã tồn tại chưa (trừ email hiện tại của bác sĩ)
    if (!empty($email)) {
        $sql_check = "SELECT id FROM bacsi WHERE email = '$email' AND id != '$id'";
        $result_check = $conn->query($sql_check);
        if ($result_check && $result_check->num_rows > 0) {
            return ["success" => false, "message" => "Email đã tồn tại trong hệ thống"];
        }
    }
    
    // Xử lý upload hình ảnh nếu có
    $imageUpdate = "";
    if ($image['name'] != '') {
        $target_dir = "../../assets/img/bacsi/";
        $fileName = time() . '_' . basename($image["name"]);
        $target_file = $target_dir . $fileName;
        
        // Kiểm tra thư mục đích
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Kiểm tra loại file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return ["success" => false, "message" => "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF."];
        }
        
        // Upload file
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Lấy và xóa ảnh cũ nếu có
            $oldImage = getDoctorById($id)['hinh_anh'];
            if (!empty($oldImage) && file_exists("../../" . $oldImage)) {
                unlink("../../" . $oldImage);
            }
            
            $imageUpdate = ", hinh_anh = 'assets/img/bacsi/$fileName'";
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }
    
    // Cập nhật trong cơ sở dữ liệu
    $sql = "UPDATE bacsi SET 
            ho_ten = '$hoTen', 
            chuyenkhoa_id = '$chuyenKhoaId', 
            nam_sinh = '$namSinh', 
            gioi_tinh = '$gioiTinh', 
            dien_thoai = '$dienThoai', 
            email = '$email', 
            dia_chi = '$diaChi', 
            mo_ta = '$moTa', 
            bang_cap = '$bangCap', 
            kinh_nghiem = '$kinhNghiem'
            $imageUpdate, 
            ngay_capnhat = NOW() 
            WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Cập nhật bác sĩ thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Xóa bác sĩ
 */
function deleteDoctor($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    // Kiểm tra nếu bác sĩ có lịch hẹn
    $sql_check = "SELECT COUNT(*) as count FROM lichhen WHERE bacsi_id = '$id'";
    $result = $conn->query($sql_check);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        return ["success" => false, "message" => "Không thể xóa bác sĩ này vì đã có lịch hẹn liên kết"];
    }
    
    // Lấy thông tin hình ảnh và nguoidung_id để xóa
    $doctor = getDoctorById($id);
    $nguoiDungId = $doctor['nguoidung_id'];
    
    // Xóa trong cơ sở dữ liệu
    $sql = "DELETE FROM bacsi WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        // Xóa file hình ảnh nếu có
        if (!empty($doctor['hinh_anh']) && file_exists("../../" . $doctor['hinh_anh'])) {
            unlink("../../" . $doctor['hinh_anh']);
        }
        
        // Xóa tài khoản người dùng nếu có
        if (!empty($nguoiDungId)) {
            $sql_user = "DELETE FROM nguoidung WHERE id = '$nguoiDungId'";
            $conn->query($sql_user);
        }
        
        return ["success" => true, "message" => "Xóa bác sĩ thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Hàm tạo mật khẩu ngẫu nhiên
 */
function generateRandomPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    return $password;
}

/**
 * Lấy lịch làm việc của bác sĩ
 */
function getDoctorSchedule($doctorId) {
    global $conn;
    
    $doctorId = $conn->real_escape_string($doctorId);
    
    $sql = "SELECT * FROM lichtrinh_bacsi WHERE bacsi_id = '$doctorId' ORDER BY ngay, gio_bat_dau";
    $result = $conn->query($sql);
    $schedule = [];
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $schedule[] = $row;
        }
    }
    
    return $schedule;
}

/**
 * Xử lý yêu cầu AJAX
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            echo json_encode(addDoctor($_POST, $_FILES['hinhAnh'] ?? ['name' => '']));
            break;
        case 'update':
            echo json_encode(updateDoctor($_POST['id'], $_POST, $_FILES['hinhAnh'] ?? ['name' => '']));
            break;
        case 'delete':
            echo json_encode(deleteDoctor($_POST['id']));
            break;
        case 'get_schedule':
            echo json_encode(getDoctorSchedule($_POST['doctorId']));
            break;
        default:
            echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
            break;
    }
}

// Xử lý yêu cầu GET cho việc lấy danh sách
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    $action = $_GET['action'];
    
    // Set JSON content type for all responses
    header('Content-Type: application/json');
    
    // Error handling to prevent HTML errors
    try {
        switch ($action) {
            case 'get_specialties':
                echo json_encode(getAllSpecialties());
                break;
            case 'get_doctor':
                if (!isset($_GET['id'])) {
                    echo json_encode(["error" => "Thiếu ID bác sĩ"]);
                    exit;
                }
                echo json_encode(getDoctorById($_GET['id']));
                break;
            case 'get_doctor_full':
                if (!isset($_GET['id'])) {
                    echo json_encode(["error" => "Thiếu ID bác sĩ"]);
                    exit;
                }
                echo json_encode(getFullDoctorInfo($_GET['id']));
                break;
            default:
                echo json_encode(["error" => "Hành động không hợp lệ"]);
                break;
        }
    } catch (Exception $e) {
        echo json_encode(["error" => "Lỗi hệ thống: " . $e->getMessage()]);
    }
    exit;
}
?>
