<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: datlich.php');
    exit;
}

// Determine patient ID
if (!is_logged_in()) {
    // Guest patient: insert new patient record
    $fullname = trim($_POST['fullname']);
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $birth_year = intval($_POST['birthyear']);
    // Construct address
    $addr_parts = [];
    if (!empty($_POST['address'])) {
        $addr_parts[] = trim($_POST['address']);
    }
    if (!empty($_POST['ward_text'])) {
        $addr_parts[] = 'Phường/Xã: ' . trim($_POST['ward_text']);
    }
    if (!empty($_POST['district_text'])) {
        $addr_parts[] = 'Quận/Huyện: ' . trim($_POST['district_text']);
    }
    if (!empty($_POST['province_text'])) {
        $addr_parts[] = 'Tỉnh/Thành: ' . trim($_POST['province_text']);
    }
    $address = implode(', ', $addr_parts);
    
    $stmt = $conn->prepare(
        "INSERT INTO benhnhan (nguoidung_id, ho_ten, nam_sinh, gioi_tinh, dien_thoai, email, dia_chi) 
         VALUES (NULL, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sissss", $fullname, $birth_year, $gender, $phone, $email, $address);
    $stmt->execute();
    $benhnhan_id = $stmt->insert_id;
} else {
    // Logged-in patient
    $user = get_logged_in_user();
    $patient = get_patient_info($user['id']);
    $benhnhan_id = $patient['id'];
}

// Build full address from inputs
$addr_parts = [];
if (!empty($_POST['address'])) {
    $addr_parts[] = trim($_POST['address']);
}
if (!empty($_POST['ward_text'])) {
    $addr_parts[] = 'Phường/Xã: ' . trim($_POST['ward_text']);
}
if (!empty($_POST['district_text'])) {
    $addr_parts[] = 'Quận/Huyện: ' . trim($_POST['district_text']);
}
if (!empty($_POST['province_text'])) {
    $addr_parts[] = 'Tỉnh/Thành: ' . trim($_POST['province_text']);
}
$address = implode(', ', $addr_parts);

// If user is logged in, update their saved address
if (is_logged_in() && !empty($address)) {
    $stmt3 = $conn->prepare("UPDATE benhnhan SET dia_chi = ? WHERE id = ?");
    $stmt3->bind_param("si", $address, $benhnhan_id);
    $stmt3->execute();
    $stmt3->close();
}

// Collect booking details
$bacs_id = intval($_POST['doctor']);
$ngay_hen = $_POST['appointment-date'];
$gio_hen = $_POST['appointment-time'] . ':00';
$ly_do   = trim($_POST['reason']);
$service_id = intval($_POST['dichvu']);

// Fetch service price
$stmt2 = $conn->prepare("SELECT gia_coban FROM dichvu WHERE id = ?");
$stmt2->bind_param("i", $service_id);
$stmt2->execute();
$stmt2->bind_result($service_price);
$stmt2->fetch();
$stmt2->close();

$phi_kham = $service_price;

// Generate unique appointment code
$ma_lichhen = 'APT' . strtoupper(substr(uniqid(), -8));

// Insert booking record with service
$stmt = $conn->prepare(
    "INSERT INTO lichhen 
     (ma_lichhen, benhnhan_id, bacsi_id, dichvu_id, ngay_hen, gio_hen, ly_do, phi_kham) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("siiisssd", $ma_lichhen, $benhnhan_id, $bacs_id, $service_id, $ngay_hen, $gio_hen, $ly_do, $phi_kham);
$stmt->execute();

// Redirect to confirmation page
header("Location: xacnhan_datlich.php?ma_lichhen={$ma_lichhen}");
exit;
