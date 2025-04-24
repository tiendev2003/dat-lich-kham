<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!is_logged_in()) {
    header('Location: dangnhap.php');
    exit;
}

// Get user and patient data
$user = get_logged_in_user();
$patient = get_patient_info($user['id']);

// Check if record ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: user_profile.php#medical');
    exit;
}

$record_id = intval($_GET['id']);

// Get the medical record and verify it belongs to this patient
$stmt = $conn->prepare("
    SELECT k.*, l.ngay_hen, l.ly_do, b.ho_ten AS doctor_name, 
           c.ten_chuyenkhoa AS specialty_name, bn.ho_ten AS patient_name, 
           bn.nam_sinh, bn.gioi_tinh, bn.cmnd_cccd
    FROM ketqua_kham k 
    JOIN lichhen l ON k.lichhen_id = l.id 
    JOIN bacsi b ON l.bacsi_id = b.id 
    JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id
    JOIN benhnhan bn ON l.benhnhan_id = bn.id
    WHERE k.id = ? AND bn.id = ?
");
$stmt->bind_param('ii', $record_id, $patient['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Record not found or doesn't belong to this patient
    header('Location: user_profile.php#medical');
    exit;
}

$record = $result->fetch_assoc();

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="medical_record_'.$record_id.'.pdf"');

// If you have a PDF generation library like FPDF or TCPDF, you would use it here
// For this example, we'll output an HTML version that can be printed/saved as PDF by the browser

// You can add your PDF generation code here
// For example with FPDF:
// require('fpdf/fpdf.php');
// $pdf = new FPDF();
// $pdf->AddPage();
// ...
// $pdf->Output();

// Simple HTML version as placeholder
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả khám bệnh - <?php echo htmlspecialchars($record['patient_name']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
        .clinic-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .clinic-info {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .patient-info {
            margin-bottom: 30px;
        }
        .patient-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .patient-info td {
            padding: 5px 10px;
        }
        .patient-info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .medical-info {
            margin-bottom: 30px;
        }
        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin: 15px 0 10px;
            background-color: #f5f5f5;
            padding: 5px;
        }
        .section-content {
            padding: 0 10px;
            white-space: pre-line;
        }
        .signature {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-name {
            margin-top: 50px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="clinic-name">PHÒNG KHÁM ĐA KHOA LỘC BÌNH</div>
        <div class="clinic-info">Địa chỉ: 123 Nguyễn Văn Linh, Quận 7, TP.HCM</div>
        <div class="clinic-info">Điện thoại: 0987 654 321 | Email: info@phongkhamlocbinh.vn</div>
    </div>
    
    <div class="document-title">PHIẾU KẾT QUẢ KHÁM BỆNH</div>
    
    <div class="patient-info">
        <table>
            <tr>
                <td>Họ và tên:</td>
                <td><?php echo htmlspecialchars($record['patient_name']); ?></td>
                <td>Mã bệnh nhân:</td>
                <td>BN<?php echo sprintf('%06d', $patient['id']); ?></td>
            </tr>
            <tr>
                <td>Năm sinh:</td>
                <td><?php echo htmlspecialchars($record['nam_sinh']); ?> (<?php echo date('Y') - $record['nam_sinh']; ?> tuổi)</td>
                <td>Giới tính:</td>
                <td><?php echo htmlspecialchars($record['gioi_tinh']); ?></td>
            </tr>
            <tr>
                <td>CMND/CCCD:</td>
                <td colspan="3"><?php echo htmlspecialchars($record['cmnd_cccd']); ?></td>
            </tr>
            <tr>
                <td>Ngày khám:</td>
                <td><?php echo date('d/m/Y', strtotime($record['ngay_hen'])); ?></td>
                <td>Bác sĩ khám:</td>
                <td>BS. <?php echo htmlspecialchars($record['doctor_name']); ?></td>
            </tr>
            <tr>
                <td>Chuyên khoa:</td>
                <td colspan="3"><?php echo htmlspecialchars($record['specialty_name']); ?></td>
            </tr>
            <?php if (!empty($record['ly_do'])): ?>
            <tr>
                <td>Lý do khám:</td>
                <td colspan="3"><?php echo htmlspecialchars($record['ly_do']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>
    
    <div class="medical-info">
        <div class="section-title">CHẨN ĐOÁN</div>
        <div class="section-content"><?php echo htmlspecialchars($record['chan_doan']); ?></div>
        
        <?php if (!empty($record['mo_ta'])): ?>
        <div class="section-title">KẾT QUẢ KHÁM</div>
        <div class="section-content"><?php echo htmlspecialchars($record['mo_ta']); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($record['don_thuoc'])): ?>
        <div class="section-title">ĐƠN THUỐC</div>
        <div class="section-content"><?php echo htmlspecialchars($record['don_thuoc']); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($record['loi_dan']) || !empty($record['ghi_chu'])): ?>
        <div class="section-title">LỜI DẶN</div>
        <div class="section-content"><?php echo htmlspecialchars($record['loi_dan'] ?: $record['ghi_chu']); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="signature">
        <div class="signature-box">
            <div>Bệnh nhân</div>
            <div class="signature-name"><?php echo htmlspecialchars($record['patient_name']); ?></div>
        </div>
        <div class="signature-box">
            <div>Bác sĩ khám bệnh</div>
            <div class="signature-name">BS. <?php echo htmlspecialchars($record['doctor_name']); ?></div>
        </div>
    </div>
    
    <div class="footer">
        Phiếu này có giá trị từ ngày <?php echo date('d/m/Y', strtotime($record['ngay_tao'])); ?>
        <br>
        Cảm ơn quý khách đã tin tưởng và sử dụng dịch vụ của Phòng khám đa khoa Lộc Bình
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print();" style="padding: 10px 20px; cursor: pointer;">In phiếu kết quả</button>
    </div>
</body>
</html>
