<?php
if (!isset($db_already_connected)) {
    require_once '../includes/db_connect.php';
}

/**
 * Đếm số chuyên khoa trong cơ sở dữ liệu
 */
function countSpecialties($filter = [])
{
    global $conn;
    
    $whereClause = "WHERE 1";
    
    // Áp dụng bộ lọc nếu có
    if (!empty($filter['search'])) {
        $search = $conn->real_escape_string($filter['search']);
        $whereClause .= " AND (ten_chuyenkhoa LIKE '%$search%' OR mota LIKE '%$search%')";
    }
    
    $sql = "SELECT COUNT(*) as total FROM chuyenkhoa $whereClause";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    return 0;
}

/**
 * Lấy tất cả chuyên khoa từ cơ sở dữ liệu
 */
function getAllSpecialties()
{
    global $conn;
    $sql = "SELECT ck.*, COUNT(bs.id) as so_bacsi 
            FROM chuyenkhoa ck 
            LEFT JOIN bacsi bs ON ck.id = bs.chuyenkhoa_id 
            GROUP BY ck.id 
            ORDER BY ck.ten_chuyenkhoa";
    
    $result = $conn->query($sql);
    $specialties = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $specialties[] = $row;
        }
    }

    return $specialties;
}

/**
 * Lấy thông tin một chuyên khoa theo ID
 */
function getSpecialtyById($id)
{
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
 * Lấy danh sách bác sĩ theo chuyên khoa
 */
function getDoctorsBySpecialty($specialty_id)
{
    global $conn;
    $specialty_id = $conn->real_escape_string($specialty_id);
    $sql = "SELECT * FROM bacsi WHERE chuyenkhoa_id = '$specialty_id' ORDER BY ho_ten";
    $result = $conn->query($sql);
    $doctors = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }

    return $doctors;
}

/**
 * Lấy danh sách dịch vụ theo chuyên khoa
 */
function getServicesBySpecialty($specialty_id)
{
    global $conn;
    $specialty_id = $conn->real_escape_string($specialty_id);
    $sql = "SELECT * FROM dichvu WHERE chuyenkhoa_id = '$specialty_id' AND trangthai = 1 ORDER BY ten_dichvu";
    $result = $conn->query($sql);
    $services = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }

    return $services;
}

/**
 * Thêm chuyên khoa mới
 */
function addSpecialty($data, $image)
{
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $tenChuyenkhoa = $conn->real_escape_string($data['tenChuyenkhoa']);
    $icon = $conn->real_escape_string($data['icon']);
    $mota = $conn->real_escape_string($data['mota']);
    
    // Xử lý upload hình ảnh nếu có
    $hinhAnh = '';
    if ($image['name'] != '') {
        $target_dir = "../../assets/img/chuyenkhoa/";
        $fileName = time() . '_' . basename($image["name"]);
        $target_file = $target_dir . $fileName;
        
        // Kiểm tra thư mục đích
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Kiểm tra loại file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return ["success" => false, "message" => "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF."];
        }
        
        // Upload file
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $hinhAnh = "assets/img/chuyenkhoa/" . $fileName;
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }
    
    // Thêm vào cơ sở dữ liệu
    $sql = "INSERT INTO chuyenkhoa (ten_chuyenkhoa, icon, mota, hinh_anh, ngay_tao) 
            VALUES ('$tenChuyenkhoa', '$icon', '$mota', '$hinhAnh', NOW())";
            
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Thêm chuyên khoa thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Cập nhật chuyên khoa
 */
function updateSpecialty($id, $data, $image)
{
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $id = $conn->real_escape_string($id);
    $tenChuyenkhoa = $conn->real_escape_string($data['tenChuyenkhoa']);
    $icon = $conn->real_escape_string($data['icon']);
    $mota = $conn->real_escape_string($data['mota']);
    
    // Xử lý upload hình ảnh nếu có
    $imageUpdate = "";
    if ($image['name'] != '') {
        $target_dir = "../../assets/img/chuyenkhoa/";
        $fileName = time() . '_' . basename($image["name"]);
        $target_file = $target_dir . $fileName;
        
        // Kiểm tra thư mục đích
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Kiểm tra loại file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            return ["success" => false, "message" => "Chỉ chấp nhận file ảnh JPG, JPEG, PNG & GIF."];
        }
        
        // Upload file
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            // Lấy và xóa ảnh cũ nếu có
            $oldImage = getSpecialtyById($id)['hinh_anh'];
            if (!empty($oldImage) && file_exists("../../" . $oldImage)) {
                unlink("../../" . $oldImage);
            }
            
            $imageUpdate = ", hinh_anh = 'assets/img/chuyenkhoa/$fileName'";
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }
    
    // Cập nhật trong cơ sở dữ liệu
    $sql = "UPDATE chuyenkhoa SET 
            ten_chuyenkhoa = '$tenChuyenkhoa', 
            icon = '$icon', 
            mota = '$mota'
            $imageUpdate, 
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
function deleteSpecialty($id)
{
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    // Lấy thông tin hình ảnh để xóa file
    $specialty = getSpecialtyById($id);
    
    // Kiểm tra xem có dữ liệu liên quan không
    $check_related = $conn->query("SELECT COUNT(*) as count FROM bacsi WHERE chuyenkhoa_id = '$id'");
    $related_data = $check_related->fetch_assoc();
    
    if ($related_data['count'] > 0) {
        return ["success" => false, "message" => "Không thể xóa chuyên khoa này vì có bác sĩ liên quan."];
    }
    
    $check_related = $conn->query("SELECT COUNT(*) as count FROM dichvu WHERE chuyenkhoa_id = '$id'");
    $related_data = $check_related->fetch_assoc();
    
    if ($related_data['count'] > 0) {
        return ["success" => false, "message" => "Không thể xóa chuyên khoa này vì có dịch vụ liên quan."];
    }
    
    // Xóa trong cơ sở dữ liệu
    $sql = "DELETE FROM chuyenkhoa WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        // Xóa file hình ảnh nếu có
        if (!empty($specialty['hinh_anh']) && file_exists("../../" . $specialty['hinh_anh'])) {
            unlink("../../" . $specialty['hinh_anh']);
        }
        return ["success" => true, "message" => "Xóa chuyên khoa thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Xử lý yêu cầu AJAX
 */
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'add':
            echo json_encode(addSpecialty($_POST, $_FILES['hinhAnh']));
            break;
        case 'update':
            echo json_encode(updateSpecialty($_POST['id'], $_POST, $_FILES['hinhAnh']));
            break;
        case 'delete':
            echo json_encode(deleteSpecialty($_POST['id']));
            break;
        default:
            echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
            break;
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action'])) {
    if ($_GET['action'] == 'get_specialty' && isset($_GET['id'])) {
        echo json_encode(getSpecialtyById($_GET['id']));
    } else if ($_GET['action'] == 'get_all') {
        echo json_encode(getAllSpecialties());
    }
}
?>
