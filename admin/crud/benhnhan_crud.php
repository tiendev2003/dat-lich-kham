<?php
if (!isset($db_already_connected)) {
    require_once '../includes/db_connect.php';
}
/**
 * Lấy tất cả bệnh nhân từ cơ sở dữ liệu
 */
function getAllPatients($filter = []) {
    global $conn;
    
    $whereClause = "WHERE 1";
    
    // Áp dụng bộ lọc
    if (!empty($filter['search_name'])) {
        $searchName = $conn->real_escape_string($filter['search_name']);
        $whereClause .= " AND ho_ten LIKE '%$searchName%'";
    }
    
    if (!empty($filter['search_phone'])) {
        $searchPhone = $conn->real_escape_string($filter['search_phone']);
        $whereClause .= " AND dien_thoai LIKE '%$searchPhone%'";
    }
    
    $sql = "SELECT bn.*, 
            (SELECT MAX(lh.ngay_hen) FROM lichhen lh WHERE lh.benhnhan_id = bn.id) as lan_kham_gannhat
            FROM benhnhan bn
            $whereClause
            ORDER BY bn.id DESC";
    
    $result = $conn->query($sql);
    $patients = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $patients[] = $row;
        }
    }
    
    return $patients;
}

/**
 * Lấy thông tin một bệnh nhân theo ID
 */
function getPatientById($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM benhnhan WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Cập nhật thông tin bệnh nhân
 */
function updatePatient($id, $data) {
    global $conn;
    
    // Xử lý dữ liệu đầu vào
    $id = $conn->real_escape_string($id);
    $hoTen = $conn->real_escape_string($data['hoTen']);
    $namSinh = $conn->real_escape_string($data['namSinh']);
    $gioiTinh = $conn->real_escape_string($data['gioiTinh']);
    $dienThoai = $conn->real_escape_string($data['dienThoai']);
    $email = !empty($data['email']) ? $conn->real_escape_string($data['email']) : '';
    $diaChi = $conn->real_escape_string($data['diaChi']);
    $nhomMau = !empty($data['nhomMau']) ? $conn->real_escape_string($data['nhomMau']) : '';
    $diUng = !empty($data['diUng']) ? $conn->real_escape_string($data['diUng']) : '';
    
    // Cập nhật trong cơ sở dữ liệu
    $sql = "UPDATE benhnhan SET 
            ho_ten = '$hoTen', 
            nam_sinh = '$namSinh', 
            gioi_tinh = '$gioiTinh', 
            dien_thoai = '$dienThoai', 
            email = '$email', 
            dia_chi = '$diaChi', 
            nhom_mau = '$nhomMau', 
            di_ung = '$diUng',
            ngay_capnhat = NOW() 
            WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        return ["success" => true, "message" => "Cập nhật thông tin bệnh nhân thành công"];
    } else {
        return ["success" => false, "message" => "Lỗi: " . $conn->error];
    }
}

/**
 * Lấy lịch sử khám bệnh của bệnh nhân
 */
function getPatientMedicalHistory($patientId) {
    global $conn;
    
    $patientId = $conn->real_escape_string($patientId);
    
    $sql = "SELECT lh.*, bs.ho_ten as ten_bacsi, dv.ten_dichvu, 
            kq.chan_doan, kq.don_thuoc, kq.ghi_chu 
            FROM lichhen lh
            LEFT JOIN bacsi bs ON lh.bacsi_id = bs.id
            LEFT JOIN dichvu dv ON lh.dichvu_id = dv.id
            LEFT JOIN ketqua_kham kq ON lh.id = kq.lichhen_id
            WHERE lh.benhnhan_id = '$patientId'
            ORDER BY lh.ngay_hen DESC, lh.gio_hen DESC";
    
    $result = $conn->query($sql);
    $history = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
    }
    
    return $history;
}

/**
 * Xem đơn thuốc của lịch hẹn
 */
function getPrescription($appointmentId) {
    global $conn;
    
    $appointmentId = $conn->real_escape_string($appointmentId);
    
    $sql = "SELECT dt.*, t.ten_thuoc, t.don_vi, t.huong_dan_chung 
            FROM don_thuoc dt
            LEFT JOIN thuoc t ON dt.thuoc_id = t.id
            WHERE dt.lichhen_id = '$appointmentId'
            ORDER BY dt.id ASC";
    
    $result = $conn->query($sql);
    $prescription = [];
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $prescription[] = $row;
        }
    }
    
    return $prescription;
}

/**
 * Xử lý yêu cầu AJAX
 */
// Xử lý yêu cầu POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch ($action) {
        case 'update':
            echo json_encode(updatePatient($_POST['id'], $_POST));
            break;
        case 'get_history':
            echo json_encode(getPatientMedicalHistory($_POST['patient_id']));
            break;
        case 'get_prescription':
            echo json_encode(getPrescription($_POST['appointment_id']));
            break;
        default:
            echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
            break;
    }
}
// Xử lý yêu cầu GET
elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch ($action) {
        case 'get_patient':
            if (isset($_GET['id'])) {
                $patient = getPatientById($_GET['id']);
                echo json_encode($patient);
            } else {
                echo json_encode(null);
            }
            break;
        default:
            echo json_encode(["success" => false, "message" => "Hành động không hợp lệ"]);
            break;
    }
}
?>
