<?php
if (!isset($db_already_connected)) {
    require_once '../includes/db_connect.php';
}

/**
 * Lấy tất cả tin tức từ cơ sở dữ liệu
 */
function getAllNews($filter = [])
{
    global $conn;

    $whereClause = "WHERE 1";

    // Áp dụng bộ lọc
    if (!empty($filter['category'])) {
        $category = $conn->real_escape_string($filter['category']);
        $whereClause .= " AND danh_muc = '$category'";
    }

    if (!empty($filter['status'])) {
        $status = $conn->real_escape_string($filter['status']);
        $whereClause .= " AND trang_thai = '$status'";
    }

    if (!empty($filter['search'])) {
        $search = $conn->real_escape_string($filter['search']);
        $whereClause .= " AND (tieu_de LIKE '%$search%' OR noi_dung LIKE '%$search%' OR meta_description LIKE '%$search%')";
    }
    
    // Thêm bộ lọc theo tag
    if (!empty($filter['tag'])) {
        $tag = $conn->real_escape_string($filter['tag']);
        $whereClause .= " AND tags LIKE '%$tag%'";
    }

    $sql = "SELECT * FROM tintuc $whereClause ORDER BY ngay_dang DESC, id DESC";
    $result = $conn->query($sql);
    $news = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $news[] = $row;
        }
    }

    return $news;
}

/**
 * Lấy thông tin một tin tức theo ID
 */
function getNewsById($id)
{
    global $conn;

    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM tintuc WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

/**
 * Thêm tin tức mới
 */
function addNews($data, $image)
{
    global $conn;

    // Xử lý dữ liệu đầu vào
    $tieuDe = $conn->real_escape_string($data['tieuDe']);
    $danhMuc = $conn->real_escape_string($data['danhMuc']);
    $noiDung = $conn->real_escape_string($data['noiDung']);
    $metaTitle = $conn->real_escape_string($data['metaTitle']);
    $metaDescription = $conn->real_escape_string($data['metaDescription']);
    $tags = $conn->real_escape_string($data['tags']);
    $trangThai = $conn->real_escape_string($data['trangThai']);

    // Xử lý trạng thái đăng tin
    $ngayDang = 'NULL';
    if ($trangThai == 'published') {
        $ngayDang = 'NOW()';
    } elseif ($trangThai == 'scheduled' && !empty($data['scheduledTime'])) {
        $scheduledTime = $conn->real_escape_string($data['scheduledTime']);
        $ngayDang = "'$scheduledTime'";
    }

    // Xử lý upload hình ảnh nếu có
    $hinhAnh = '';
    if ($image['name'] != '') {
        $target_dir = "../../assets/img/tintuc/";
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
            $hinhAnh = "assets/img/tintuc/" . $fileName;
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }

    // Thêm vào cơ sở dữ liệu
    $sql = "INSERT INTO tintuc (tieu_de, danh_muc, noi_dung, hinh_anh, meta_title, meta_description, tags, trang_thai, ngay_dang, ngay_tao) 
            VALUES ('$tieuDe', '$danhMuc', '$noiDung', '$hinhAnh', '$metaTitle', '$metaDescription', '$tags', '$trangThai', $ngayDang, NOW())";

    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Thêm tin tức thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Cập nhật tin tức
 */
function updateNews($id, $data, $image)
{
    global $conn;

    // Xử lý dữ liệu đầu vào
    $id = $conn->real_escape_string($id);
    $tieuDe = $conn->real_escape_string($data['tieuDe']);
    $danhMuc = $conn->real_escape_string($data['danhMuc']);
    $noiDung = $conn->real_escape_string($data['noiDung']);
    $metaTitle = $conn->real_escape_string($data['metaTitle']);
    $metaDescription = $conn->real_escape_string($data['metaDescription']);
    $tags = $conn->real_escape_string($data['tags']);
    $trangThai = $conn->real_escape_string($data['trangThai']);

    // Xử lý trạng thái đăng tin
    $ngayDangUpdate = '';
    if ($trangThai == 'published') {
        // Kiểm tra nếu trước đó chưa xuất bản thì cập nhật ngày đăng
        $currentStatus = getNewsById($id)['trang_thai'];
        if ($currentStatus != 'published') {
            $ngayDangUpdate = ", ngay_dang = NOW()";
        }
    } elseif ($trangThai == 'scheduled' && !empty($data['scheduledTime'])) {
        $scheduledTime = $conn->real_escape_string($data['scheduledTime']);
        $ngayDangUpdate = ", ngay_dang = '$scheduledTime'";
    } elseif ($trangThai == 'draft') {
        $ngayDangUpdate = ", ngay_dang = NULL";
    }

    // Xử lý upload hình ảnh nếu có
    $imageUpdate = "";
    if ($image['name'] != '') {
        $target_dir = "../../assets/img/tintuc/";
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
            $oldImage = getNewsById($id)['hinh_anh'];
            if (!empty($oldImage) && file_exists("../../" . $oldImage)) {
                unlink("../../" . $oldImage);
            }

            $imageUpdate = ", hinh_anh = 'assets/img/tintuc/$fileName'";
        } else {
            return ["success" => false, "message" => "Lỗi khi tải lên hình ảnh."];
        }
    }

    // Cập nhật trong cơ sở dữ liệu
    $sql = "UPDATE tintuc SET 
            tieu_de = '$tieuDe', 
            danh_muc = '$danhMuc', 
            noi_dung = '$noiDung', 
            meta_title = '$metaTitle', 
            meta_description = '$metaDescription', 
            tags = '$tags', 
            trang_thai = '$trangThai'
            $imageUpdate
            $ngayDangUpdate, 
            ngay_capnhat = NOW() 
            WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Cập nhật tin tức thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Xóa tin tức
 */
function deleteNews($id)
{
    global $conn;

    $id = $conn->real_escape_string($id);

    // Lấy thông tin hình ảnh để xóa file
    $news = getNewsById($id);

    // Xóa trong cơ sở dữ liệu
    $sql = "DELETE FROM tintuc WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        // Xóa file hình ảnh nếu có
        if (!empty($news['hinh_anh']) && file_exists("../../" . $news['hinh_anh'])) {
            unlink("../../" . $news['hinh_anh']);
        }
        return ["success" => true, "message" => "Xóa tin tức thành công"];
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
            echo json_encode(addNews($_POST, $_FILES['hinhAnh']));
            break;
        case 'update':
            echo json_encode(updateNews($_POST['id'], $_POST, $_FILES['hinhAnh']));
            break;
        case 'delete':
            echo json_encode(deleteNews($_POST['id']));
            break;
        default:
            echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
            break;
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['action']) && $_GET['action'] == 'get_news' && isset($_GET['id'])) {
        echo json_encode(getNewsById($_GET['id']));
    }
}
?>