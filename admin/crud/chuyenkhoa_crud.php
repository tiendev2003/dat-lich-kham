<?php
if (!isset($db_already_connected)) {
    require_once '../includes/db_connect.php';
}

/**
 * Đếm tổng số chuyên khoa, có thể lọc theo từ khóa tìm kiếm
 */
function countSpecialties($search = '') {
    global $conn;
    
    $whereClause = '';
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause = "WHERE ten_chuyenkhoa LIKE '%$search%' OR mota LIKE '%$search%'";
    }
    
    $sql = "SELECT COUNT(*) as total FROM chuyenkhoa $whereClause";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Lấy tất cả chuyên khoa từ cơ sở dữ liệu với phân trang và tìm kiếm
 */
function getAllSpecialties($search = '', $page = 1, $items_per_page = 10) {
    global $conn;
    
    $whereClause = '';
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $whereClause = "WHERE ten_chuyenkhoa LIKE '%$search%' OR mota LIKE '%$search%'";
    }
    
    $offset = ($page - 1) * $items_per_page;
    
    $sql = "SELECT c.*, COUNT(b.id) as so_bacsi 
            FROM chuyenkhoa c
            LEFT JOIN bacsi b ON c.id = b.chuyenkhoa_id
            $whereClause
            GROUP BY c.id
            ORDER BY c.id DESC
            LIMIT $offset, $items_per_page";
    
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
 * Lấy thông tin một chuyên khoa theo ID
 */
function getSpecialtyById($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM chuyenkhoa WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Thêm chuyên khoa mới
 */
function addSpecialty($data) {
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $tenChuyenKhoa = $conn->real_escape_string($data['tenChuyenKhoa']);
    $icon = $conn->real_escape_string($data['icon']);
    $moTa = $conn->real_escape_string($data['moTa']);
    
    // Thêm vào cơ sở dữ liệu
    $sql = "INSERT INTO chuyenkhoa (ten_chuyenkhoa, icon, mota, ngay_tao) 
            VALUES ('$tenChuyenKhoa', '$icon', '$moTa', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Thêm chuyên khoa thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Cập nhật chuyên khoa
 */
function updateSpecialty($id, $data) {
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $id = $conn->real_escape_string($id);
    $tenChuyenKhoa = $conn->real_escape_string($data['tenChuyenKhoa']);
    $icon = $conn->real_escape_string($data['icon']);
    $moTa = $conn->real_escape_string($data['moTa']);
    
    // Cập nhật trong cơ sở dữ liệu
    $sql = "UPDATE chuyenkhoa SET 
            ten_chuyenkhoa = '$tenChuyenKhoa', 
            icon = '$icon', 
            mota = '$moTa', 
            ngay_capnhat = NOW() 
            WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Cập nhật chuyên khoa thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Xóa chuyên khoa
 */
function deleteSpecialty($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    // Kiểm tra xem có bác sĩ thuộc chuyên khoa này không
    $sql_check = "SELECT COUNT(*) as count FROM bacsi WHERE chuyenkhoa_id = '$id'";
    $result = $conn->query($sql_check);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        return ["success" => false, "message" => "Không thể xóa chuyên khoa này vì đã có bác sĩ liên kết"];
    }
    
    // Kiểm tra xem có dịch vụ thuộc chuyên khoa này không
    $sql_check = "SELECT COUNT(*) as count FROM dichvu WHERE chuyenkhoa_id = '$id'";
    $result = $conn->query($sql_check);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        return ["success" => false, "message" => "Không thể xóa chuyên khoa này vì đã có dịch vụ liên kết"];
    }
    
    // Xóa trong cơ sở dữ liệu
    $sql = "DELETE FROM chuyenkhoa WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Xóa chuyên khoa thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Xử lý yêu cầu AJAX
 */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'add':
            echo json_encode(addSpecialty($_POST));
            break;
        case 'update':
            echo json_encode(updateSpecialty($_POST['id'], $_POST));
            break;
        case 'delete':
            echo json_encode(deleteSpecialty($_POST['id']));
            break;
        default:
            echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
            break;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'get_specialty') {
    // API endpoint để lấy thông tin một chuyên khoa theo id
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    $specialty = getSpecialtyById($id);
    
    echo json_encode($specialty ?: ["error" => "Không tìm thấy chuyên khoa"]);
    exit;
}
?>
