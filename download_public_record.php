<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check if record ID and security code are provided
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['code'])) {
    header('Location: tracuu.php?error=missing_params');
    exit;
}

$record_id = intval($_GET['id']);
$security_code = $_GET['code'];

// Get the medical record and verify access token
$stmt = $conn->prepare("
    SELECT k.*, l.ngay_hen, l.gio_hen, l.ly_do, l.trang_thai, l.phi_kham, l.ma_lichhen,
           b.ho_ten AS doctor_name, b.id AS doctor_id, c.ten_chuyenkhoa AS specialty_name, 
           c.id AS specialty_id, d.ten_dichvu AS service_name, d.id AS service_id,
           bn.ho_ten AS patient_name, bn.nam_sinh, bn.gioi_tinh, bn.cmnd_cccd, bn.dien_thoai, bn.email
    FROM ketqua_kham k 
    JOIN lichhen l ON k.lichhen_id = l.id 
    JOIN bacsi b ON l.bacsi_id = b.id 
    JOIN chuyenkhoa c ON b.chuyenkhoa_id = c.id
    JOIN benhnhan bn ON l.benhnhan_id = bn.id
    LEFT JOIN dichvu d ON l.dichvu_id = d.id
    WHERE k.id = ?
");
$stmt->bind_param('i', $record_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Record not found
    header('Location: tracuu.php?error=record_not_found');
    exit;
}

$record = $result->fetch_assoc();
$stmt->close();

// Verify security code
$valid_code = md5($record['ma_lichhen'] . $record['dien_thoai']);
if ($security_code !== $valid_code) {
    header('Location: tracuu.php?error=invalid_access');
    exit;
}

// Get medications for this record
$med_stmt = $conn->prepare("
    SELECT d.*, t.ten_thuoc, t.don_vi, t.gia, t.huong_dan_chung
    FROM don_thuoc d
    JOIN thuoc t ON d.thuoc_id = t.id
    WHERE d.lichhen_id = ?
");
$med_stmt->bind_param('i', $record['lichhen_id']);
$med_stmt->execute();
$med_result = $med_stmt->get_result();
$medications = $med_result->fetch_all(MYSQLI_ASSOC);
$med_stmt->close();

// Calculate medication total cost
$medication_cost = 0;
foreach ($medications as $med) {
    $medication_cost += ($med['gia'] * $med['so_luong']);
}

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="medical_record_'.$record_id.'.pdf"');

// If you have a PDF generation library like FPDF or TCPDF, you would use it here
// For this example, we'll output an HTML version that can be printed/saved as PDF by the browser

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
        .medication-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .medication-table th, .medication-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .medication-table th {
            background-color: #f0f0f0;
            text-align: left;
        }
        .medication-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-row {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
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
                <td>Mã lịch hẹn:</td>
                <td><?php echo htmlspecialchars($record['ma_lichhen']); ?></td>
            </tr>
            <tr>
                <td>Năm sinh:</td>
                <td><?php echo htmlspecialchars($record['nam_sinh']); ?> (<?php echo date('Y') - $record['nam_sinh']; ?> tuổi)</td>
                <td>Giới tính:</td>
                <td><?php echo htmlspecialchars($record['gioi_tinh']); ?></td>
            </tr>
            <tr>
                <td>Ngày khám:</td>
                <td><?php echo date('d/m/Y', strtotime($record['ngay_hen'])); ?></td>
                <td>Giờ khám:</td>
                <td><?php echo date('H:i', strtotime($record['gio_hen'])); ?></td>
            </tr>
            <tr>
                <td>Bác sĩ khám:</td>
                <td>BS. <?php echo htmlspecialchars($record['doctor_name']); ?></td>
                <td>Chuyên khoa:</td>
                <td><?php echo htmlspecialchars($record['specialty_name']); ?></td>
            </tr>
            <?php if (!empty($record['service_name'])): ?>
            <tr>
                <td>Dịch vụ:</td>
                <td colspan="3"><?php echo htmlspecialchars($record['service_name']); ?></td>
            </tr>
            <?php endif; ?>
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
        
        <?php if (count($medications) > 0): ?>
        <div class="section-title">ĐƠN THUỐC</div>
        <table class="medication-table">
            <tr>
                <th>STT</th>
                <th>Tên thuốc</th>
                <th>Liều dùng</th>
                <th>Số lượng</th>
                <th>Cách dùng</th>
                <th class="text-right">Đơn giá</th>
                <th class="text-right">Thành tiền</th>
            </tr>
            <?php 
            $stt = 1;
            foreach ($medications as $med):
                $thanh_tien = $med['gia'] * $med['so_luong'];
            ?>
            <tr>
                <td><?php echo $stt++; ?></td>
                <td>
                    <?php echo htmlspecialchars($med['ten_thuoc']); ?><br>
                    <small><?php echo htmlspecialchars($med['don_vi']); ?></small>
                </td>
                <td><?php echo htmlspecialchars($med['lieu_dung']); ?></td>
                <td class="text-right"><?php echo htmlspecialchars($med['so_luong']); ?></td>
                <td><?php echo htmlspecialchars($med['cach_dung']); ?></td>
                <td class="text-right"><?php echo number_format($med['gia'], 0, ',', '.'); ?> đ</td>
                <td class="text-right"><?php echo number_format($thanh_tien, 0, ',', '.'); ?> đ</td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="5"></td>
                <td class="text-right">Tổng tiền thuốc:</td>
                <td class="text-right"><?php echo number_format($medication_cost, 0, ',', '.'); ?> đ</td>
            </tr>
        </table>
        <?php endif; ?>
        
        <?php if (!empty($record['ghi_chu'])): ?>
        <div class="section-title">LỜI DẶN</div>
        <div class="section-content"><?php echo htmlspecialchars($record['ghi_chu']); ?></div>
        <?php endif; ?>
        
        <div class="section-title">THÔNG TIN PHÍ</div>
        <table class="medication-table">
            <tr>
                <td width="50%">Phí khám:</td>
                <td class="text-right"><?php echo number_format($record['phi_kham'], 0, ',', '.'); ?> đ</td>
            </tr>
            <?php if (count($medications) > 0): ?>
            <tr>
                <td>Tiền thuốc:</td>
                <td class="text-right"><?php echo number_format($medication_cost, 0, ',', '.'); ?> đ</td>
            </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td>Tổng chi phí:</td>
                <td class="text-right"><?php echo number_format($record['phi_kham'] + $medication_cost, 0, ',', '.'); ?> đ</td>
            </tr>
        </table>
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