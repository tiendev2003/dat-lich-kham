<?php
if (!isset($db_already_connected)) {
    require_once '../includes/db_connect.php';
}

/**
 * Đếm tổng số dịch vụ, có thể lọc theo từ khóa tìm kiếm và chuyên khoa
 */
function countServices($filter = []) {
    global $conn;
    
    $whereClause = [];
    
    if (!empty($filter['search'])) {
        $search = $conn->real_escape_string($filter['search']);
        $whereClause[] = "(ten_dichvu LIKE '%$search%' OR mota_ngan LIKE '%$search%' OR chi_tiet LIKE '%$search%')";
    }
    
    if (!empty($filter['chuyenkhoa'])) {
        $chuyenkhoa = $conn->real_escape_string($filter['chuyenkhoa']);
        $whereClause[] = "chuyenkhoa_id = '$chuyenkhoa'";
    }
    
    if (!empty($filter['trangthai']) && $filter['trangthai'] !== 'all') {
        $trangthai = $conn->real_escape_string($filter['trangthai']);
        $whereClause[] = "trangthai = '$trangthai'";
    }
    
    $whereClauseStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";
    
    $sql = "SELECT COUNT(*) as total FROM dichvu $whereClauseStr";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Lấy tất cả dịch vụ từ cơ sở dữ liệu với phân trang và tìm kiếm
 */
function getAllServices($filter = [], $page = 1, $items_per_page = 10) {
    global $conn;
    
    $whereClause = [];
    
    if (!empty($filter['search'])) {
        $search = $conn->real_escape_string($filter['search']);
        $whereClause[] = "(d.ten_dichvu LIKE '%$search%' OR d.mota_ngan LIKE '%$search%' OR d.chi_tiet LIKE '%$search%')";
    }
    
    if (!empty($filter['chuyenkhoa'])) {
        $chuyenkhoa = $conn->real_escape_string($filter['chuyenkhoa']);
        $whereClause[] = "d.chuyenkhoa_id = '$chuyenkhoa'";
    }
    
    if (!empty($filter['trangthai']) && $filter['trangthai'] !== 'all') {
        $trangthai = $conn->real_escape_string($filter['trangthai']);
        $whereClause[] = "d.trangthai = '$trangthai'";
    }
    
    $whereClauseStr = !empty($whereClause) ? "WHERE " . implode(" AND ", $whereClause) : "";
    $offset = ($page - 1) * $items_per_page;
    
    // Sắp xếp dựa trên tham số
    $orderBy = "d.id DESC"; // Mặc định sắp xếp theo ID giảm dần
    if (!empty($filter['sort'])) {
        switch ($filter['sort']) {
            case 'name':
                $orderBy = "d.ten_dichvu ASC";
                break;
            case 'price':
                $orderBy = "d.gia_coban ASC";
                break;
            case 'date':
                $orderBy = "d.ngay_tao DESC";
                break;
        }
    }
    
    $sql = "SELECT d.*, c.ten_chuyenkhoa 
            FROM dichvu d
            LEFT JOIN chuyenkhoa c ON d.chuyenkhoa_id = c.id
            $whereClauseStr
            ORDER BY $orderBy
            LIMIT $offset, $items_per_page";
    
    $result = $conn->query($sql);
    $services = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
    
    return $services;
}

/**
 * Lấy thông tin một dịch vụ theo ID
 */
function getServiceById($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM dichvu WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Thêm dịch vụ mới
 */
function addService($data, $image = null) {
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $tenDichVu = $conn->real_escape_string($data['tenDichVu']);
    $chuyenKhoaId = $conn->real_escape_string($data['chuyenKhoaId']);
    $giaCoBan = $conn->real_escape_string($data['giaCoBan']);
    $moTaNgan = $conn->real_escape_string($data['moTaNgan']);
    $chiTiet = $conn->real_escape_string($data['chiTiet']);
    $trangThai = $conn->real_escape_string($data['trangThai']);
    
    // Xử lý upload hình ảnh nếu có
    $hinhAnh = '';
    if (isset($image) && $image['name'] != '') {
        $target_dir = "../../assets/img/services/";
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($image["name"]);
        $target_file = $target_dir . $fileName;
        
        // Kiểm tra loại file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return ["success" => false, "message" => "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF."];
        }
        
        // Upload file
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $hinhAnh = "assets/img/services/" . $fileName;
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }
    
    // Thêm vào cơ sở dữ liệu
    $sql = "INSERT INTO dichvu (ten_dichvu, chuyenkhoa_id, gia_coban, hinh_anh, mota_ngan, chi_tiet, trangthai, ngay_tao) 
            VALUES ('$tenDichVu', '$chuyenKhoaId', '$giaCoBan', '$hinhAnh', '$moTaNgan', '$chiTiet', '$trangThai', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Thêm dịch vụ thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Cập nhật dịch vụ
 */
function updateService($id, $data, $image = null) {
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $id = $conn->real_escape_string($id);
    $tenDichVu = $conn->real_escape_string($data['tenDichVu']);
    $chuyenKhoaId = $conn->real_escape_string($data['chuyenKhoaId']);
    $giaCoBan = $conn->real_escape_string($data['giaCoBan']);
    $moTaNgan = $conn->real_escape_string($data['moTaNgan']);
    $chiTiet = $conn->real_escape_string($data['chiTiet']);
    $trangThai = $conn->real_escape_string($data['trangThai']);
    
    // Xử lý upload hình ảnh nếu có
    $imageUpdate = "";
    if (isset($image) && $image['name'] != '') {
        $target_dir = "../../assets/img/services/";
        
        // Tạo thư mục nếu chưa tồn tại
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($image["name"]);
        $target_file = $target_dir . $fileName;
        
        // Kiểm tra loại file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return ["success" => false, "message" => "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF."];
        }
        
        // Upload file
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Lấy và xóa ảnh cũ nếu có
            $oldImage = getServiceById($id)['hinh_anh'];
            if (!empty($oldImage) && file_exists("../../" . $oldImage)) {
                unlink("../../" . $oldImage);
            }
            
            $imageUpdate = ", hinh_anh = 'assets/img/services/$fileName'";
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }
    
    // Cập nhật trong cơ sở dữ liệu
    $sql = "UPDATE dichvu SET 
            ten_dichvu = '$tenDichVu', 
            chuyenkhoa_id = '$chuyenKhoaId', 
            gia_coban = '$giaCoBan', 
            mota_ngan = '$moTaNgan', 
            chi_tiet = '$chiTiet', 
            trangthai = '$trangThai'
            $imageUpdate, 
            ngay_capnhat = NOW() 
            WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Cập nhật dịch vụ thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Xóa dịch vụ
 */
function deleteService($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    // Lấy thông tin hình ảnh để xóa file
    $service = getServiceById($id);
    
    // Xóa trong cơ sở dữ liệu
    $sql = "DELETE FROM dichvu WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        // Xóa file hình ảnh nếu có
        if (!empty($service['hinh_anh']) && file_exists("../../" . $service['hinh_anh'])) {
            unlink("../../" . $service['hinh_anh']);
        }
        return ["success" => true, "message" => "Xóa dịch vụ thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Lấy tất cả chuyên khoa
 */
function getAllSpecialties() {
    global $conn;
    
    $sql = "SELECT * FROM chuyenkhoa ORDER BY ten_chuyenkhoa";
    $result = $conn->query($sql);
    $specialties = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $specialties[] = $row;
        }
    }
    
    return $specialties;
}

/**
 * Xử lý yêu cầu AJAX
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            echo json_encode(addService($_POST, isset($_FILES['hinhAnh']) ? $_FILES['hinhAnh'] : null));
            break;
        case 'update':
            echo json_encode(updateService($_POST['id'], $_POST, isset($_FILES['hinhAnh']) ? $_FILES['hinhAnh'] : null));
            break;
        case 'delete':
            echo json_encode(deleteService($_POST['id']));
            break;
        default:
            echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
            break;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'get_service') {
    // API endpoint để lấy thông tin một dịch vụ theo id
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $service = getServiceById($id);
    
    echo json_encode($service ?: ["error" => "Không tìm thấy dịch vụ"]);
    exit;
}
?>
