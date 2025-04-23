-- Tạo cơ sở dữ liệu nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS dat_lich_kham_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng cơ sở dữ liệu
USE dat_lich_kham_db;

-- Xóa các bảng nếu đã tồn tại (theo thứ tự ngược lại để tránh lỗi khóa ngoại)
DROP TABLE IF EXISTS don_thuoc;
DROP TABLE IF EXISTS thuoc;
DROP TABLE IF EXISTS ketqua_kham;
DROP TABLE IF EXISTS lichhen;
DROP TABLE IF EXISTS tintuc;
DROP TABLE IF EXISTS dichvu;
DROP TABLE IF EXISTS bacsi;
DROP TABLE IF EXISTS benhnhan;
DROP TABLE IF EXISTS chuyenkhoa;
DROP TABLE IF EXISTS nguoidung;
DROP TABLE IF EXISTS caidat_website;

-- Tạo bảng người dùng (admin, bacsi, benhnhan)
CREATE TABLE nguoidung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    vai_tro ENUM('admin', 'bacsi', 'benhnhan') NOT NULL,
    trang_thai TINYINT DEFAULT 1,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng chuyên khoa
CREATE TABLE chuyenkhoa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_chuyenkhoa VARCHAR(100) NOT NULL,
    icon VARCHAR(50),
    mota TEXT,
    hinh_anh VARCHAR(255),
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng bệnh nhân
CREATE TABLE benhnhan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoidung_id INT,
    ho_ten VARCHAR(100) NOT NULL,
    nam_sinh INT NOT NULL,
    gioi_tinh ENUM('Nam', 'Nữ', 'Khác') DEFAULT 'Nam',
    dien_thoai VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    dia_chi TEXT,
    cmnd_cccd VARCHAR(20),
    nhom_mau VARCHAR(10),
    di_ung TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng bác sĩ
CREATE TABLE bacsi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nguoidung_id INT,
    ho_ten VARCHAR(100) NOT NULL,
    chuyenkhoa_id INT,
    nam_sinh INT,
    gioi_tinh ENUM('Nam', 'Nữ', 'Khác') DEFAULT 'Nam',
    dien_thoai VARCHAR(20),
    email VARCHAR(100),
    dia_chi TEXT,
    hinh_anh VARCHAR(255),
    mo_ta TEXT,
    bang_cap TEXT,
    kinh_nghiem TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nguoidung_id) REFERENCES nguoidung(id) ON DELETE SET NULL,
    FOREIGN KEY (chuyenkhoa_id) REFERENCES chuyenkhoa(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng dịch vụ
CREATE TABLE dichvu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_dichvu VARCHAR(150) NOT NULL,
    chuyenkhoa_id INT,
    gia_coban DECIMAL(10, 2) NOT NULL,
    hinh_anh VARCHAR(255),
    mota_ngan TEXT,
    chi_tiet TEXT,
    trangthai TINYINT DEFAULT 1,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (chuyenkhoa_id) REFERENCES chuyenkhoa(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng tin tức
CREATE TABLE tintuc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tieu_de VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    danh_muc VARCHAR(50) NOT NULL,
    noi_dung TEXT NOT NULL,
    hinh_anh VARCHAR(255),
    meta_title VARCHAR(255),
    meta_description TEXT,
    tags VARCHAR(255),
    trang_thai ENUM('published', 'draft', 'scheduled') DEFAULT 'draft',
    luot_xem INT DEFAULT 0,
    nguoi_tao INT,
    ngay_dang DATETIME,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng lịch hẹn
CREATE TABLE lichhen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ma_lichhen VARCHAR(20) UNIQUE NOT NULL,
    benhnhan_id INT NOT NULL,
    bacsi_id INT NOT NULL,
    dichvu_id INT,
    ngay_hen DATE NOT NULL,
    gio_hen TIME NOT NULL,
    ly_do TEXT,
    phi_kham DECIMAL(10, 2),
    trang_thai ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    thoi_diem_hoanthanh DATETIME,
    thoi_diem_huy DATETIME,
    ghi_chu TEXT,
    phong_kham VARCHAR(50),
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (benhnhan_id) REFERENCES benhnhan(id) ON DELETE CASCADE,
    FOREIGN KEY (bacsi_id) REFERENCES bacsi(id) ON DELETE CASCADE,
    FOREIGN KEY (dichvu_id) REFERENCES dichvu(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng kết quả khám
CREATE TABLE ketqua_kham (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lichhen_id INT NOT NULL,
    chan_doan TEXT NOT NULL,
    mo_ta TEXT,
    don_thuoc TEXT,
    ghi_chu TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lichhen_id) REFERENCES lichhen(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng thuốc
CREATE TABLE thuoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_thuoc VARCHAR(150) NOT NULL,
    don_vi VARCHAR(20) NOT NULL,
    gia DECIMAL(10, 2) NOT NULL,
    huong_dan_chung TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng đơn thuốc
CREATE TABLE don_thuoc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lichhen_id INT NOT NULL,
    thuoc_id INT NOT NULL,
    lieu_dung VARCHAR(100) NOT NULL,
    so_luong INT NOT NULL,
    cach_dung TEXT,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lichhen_id) REFERENCES lichhen(id) ON DELETE CASCADE,
    FOREIGN KEY (thuoc_id) REFERENCES thuoc(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng cài đặt website
CREATE TABLE caidat_website (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_key VARCHAR(50) NOT NULL UNIQUE,
    ten_value TEXT,
    mo_ta VARCHAR(255),
    loai ENUM('text', 'textarea', 'image', 'boolean', 'number', 'color') DEFAULT 'text',
    nhom VARCHAR(50) DEFAULT 'general',
    thu_tu INT DEFAULT 0,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    ngay_capnhat DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chèn dữ liệu mẫu cho bảng người dùng
INSERT INTO nguoidung (email, mat_khau, vai_tro) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Chèn dữ liệu mẫu cho bảng chuyên khoa
INSERT INTO chuyenkhoa (ten_chuyenkhoa, icon, mota) VALUES
('Tim mạch', 'fa-heart', 'Chuyên điều trị các bệnh về tim mạch và mạch máu.'),
('Nhi khoa', 'fa-child', 'Chuyên khám và điều trị các bệnh ở trẻ em.'),
('Da liễu', 'fa-allergies', 'Chuyên điều trị các bệnh về da.'),
('Nội tổng quát', 'fa-stethoscope', 'Khám tổng quát và điều trị các bệnh thông thường.'),
('Mắt', 'fa-eye', 'Chuyên khám và điều trị các bệnh về mắt.');

-- Chèn dữ liệu mẫu cho bảng bác sĩ
INSERT INTO bacsi (ho_ten, chuyenkhoa_id, nam_sinh, gioi_tinh, dien_thoai, email, mo_ta) VALUES
('BS. Nguyễn Thế Lâm', 1, 1980, 'Nam', '0987654321', 'bs.lam@example.com', 'Bác sĩ chuyên khoa Tim mạch với hơn 15 năm kinh nghiệm'),
('BS. Trần Thị Mai', 2, 1985, 'Nữ', '0987654322', 'bs.mai@example.com', 'Bác sĩ chuyên khoa Nhi với 10 năm kinh nghiệm'),
('BS. Lê Văn Hùng', 3, 1975, 'Nam', '0987654323', 'bs.hung@example.com', 'Bác sĩ chuyên khoa Da liễu với hơn 20 năm kinh nghiệm'),
('BS. Phạm Thị Hoa', 4, 1982, 'Nữ', '0987654324', 'bs.hoa@example.com', 'Bác sĩ Nội tổng quát với 12 năm kinh nghiệm'),
('BS. Hoàng Văn Minh', 5, 1978, 'Nam', '0987654325', 'bs.minh@example.com', 'Bác sĩ chuyên khoa Mắt với 15 năm kinh nghiệm');

-- Chèn dữ liệu mẫu cho bảng dịch vụ
INSERT INTO dichvu (ten_dichvu, chuyenkhoa_id, gia_coban, mota_ngan) VALUES
('Khám tim mạch tổng quát', 1, 500000, 'Kiểm tra sức khỏe tim mạch toàn diện'),
('Khám và tư vấn sức khỏe trẻ em', 2, 300000, 'Khám tổng quát cho trẻ và tư vấn dinh dưỡng'),
('Điều trị các bệnh da liễu', 3, 400000, 'Điều trị các bệnh về da thông thường'),
('Khám sức khỏe tổng quát', 4, 600000, 'Kiểm tra sức khỏe tổng thể và tư vấn'),
('Khám và điều trị các bệnh về mắt', 5, 450000, 'Kiểm tra thị lực và điều trị các bệnh về mắt');

-- Chèn dữ liệu mẫu cho bảng bệnh nhân
INSERT INTO benhnhan (ho_ten, nam_sinh, gioi_tinh, dien_thoai, email, dia_chi) VALUES
('Nguyễn Văn A', 1990, 'Nam', '0123456789', 'nguyenvana@email.com', 'Hà Nội'),
('Trần Thị B', 1995, 'Nữ', '0987654321', 'tranthib@email.com', 'Hồ Chí Minh'),
('Lê Văn C', 1988, 'Nam', '0934567890', 'levanc@email.com', 'Đà Nẵng'),
('Phạm Thị D', 1992, 'Nữ', '0912345678', 'phamthid@email.com', 'Hải Phòng');

-- Chèn dữ liệu mẫu cho bảng lịch hẹn
INSERT INTO lichhen (ma_lichhen, benhnhan_id, bacsi_id, dichvu_id, ngay_hen, gio_hen, ly_do, phi_kham, trang_thai, phong_kham) VALUES
('APT12345', 1, 1, 1, CURDATE(), '09:00:00', 'Đau ngực khi vận động', 500000, 'pending', 'Phòng 101'),
('APT12346', 2, 5, 5, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:30:00', 'Giảm thị lực', 450000, 'confirmed', 'Phòng 202'),
('APT12347', 3, 4, 4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '14:00:00', 'Khám sức khỏe định kỳ', 600000, 'confirmed', 'Phòng 301'),
('APT12348', 4, 3, 3, DATE_ADD(CURDATE(), INTERVAL -7 DAY), '08:00:00', 'Nổi mẩn đỏ trên da', 400000, 'completed', 'Phòng 105'),
('APT12349', 1, 2, 2, DATE_ADD(CURDATE(), INTERVAL -14 DAY), '15:30:00', 'Kiểm tra sức khỏe tổng quát', 300000, 'cancelled', 'Phòng 201');

-- Chèn dữ liệu mẫu cho bảng tin tức
INSERT INTO tintuc (tieu_de, danh_muc, noi_dung, trang_thai, ngay_dang) VALUES
('Phòng ngừa các bệnh hô hấp mùa nắng nóng', 'health', '<p>Mùa nắng nóng không chỉ là thời điểm gây khó chịu vì nhiệt độ cao mà còn tiềm ẩn nhiều nguy cơ mắc các bệnh về đường hô hấp. Các bệnh như viêm họng, viêm phế quản, hen suyễn có thể trở nên trầm trọng hơn trong thời tiết nắng nóng...</p>', 'published', NOW()),
('Chế độ ăn cho người bệnh tim mạch', 'nutrition', '<p>Người bệnh tim mạch cần có chế độ ăn khoa học và lành mạnh để hỗ trợ điều trị bệnh...</p>', 'published', NOW()),
('Chăm sóc răng miệng đúng cách', 'health', '<p>Răng miệng khỏe mạnh là cửa ngõ cho sức khỏe tổng thể. Việc chăm sóc răng miệng đúng cách giúp phòng ngừa nhiều bệnh...</p>', 'published', NOW()),
('Vaccine mới trong phòng chống các bệnh truyền nhiễm', 'medicine', '<p>Các loại vaccine mới đã và đang được phát triển nhằm phòng chống hiệu quả các bệnh truyền nhiễm...</p>', 'scheduled', DATE_ADD(NOW(), INTERVAL 7 DAY)),
('Yoga và các bài tập thể dục cho người bận rộn', 'lifestyle', '<p>Với lịch trình bận rộn, nhiều người khó có thời gian để tập thể dục. Bài viết giới thiệu các bài tập yoga ngắn phù hợp cho người bận rộn...</p>', 'draft', NULL);
INSERT INTO `tintuc` (`tieu_de`, `noi_dung`, `hinh_anh`, `danh_muc`, `meta_title`, `meta_description`, `tags`, `trang_thai`, `ngay_dang`, `ngay_tao`) VALUES
('Phòng ngừa các bệnh hô hấp mùa nắng nóng', '<h2>Phòng ngừa các bệnh hô hấp mùa nắng nóng</h2><p>Mùa nắng nóng không chỉ là thời điểm gây khó chịu vì nhiệt độ cao mà còn tiềm ẩn nhiều nguy cơ mắc các bệnh về đường hô hấp.</p><p>Các bệnh như viêm họng, viêm phế quản, hen suyễn có thể trở nên trầm trọng hơn trong thời tiết nắng nóng...</p>', 'assets/img/hohap.webp', 'health', 'Phòng ngừa các bệnh hô hấp mùa nắng nóng - Bí quyết bảo vệ sức khỏe', 'Tìm hiểu các biện pháp phòng ngừa bệnh hô hấp hiệu quả trong thời tiết nắng nóng. Bảo vệ sức khỏe đường hô hấp với những lời khuyên từ chuyên gia.', 'hô hấp, nắng nóng, phòng bệnh, viêm phổi, hen suyễn', 'published', NOW(), NOW()),
('Chế độ ăn cho người bệnh tim mạch', '<h2>Chế độ ăn cho người bệnh tim mạch</h2><p>Bệnh tim mạch là một trong những nguyên nhân gây tử vong hàng đầu trên thế giới. Chế độ ăn uống đóng vai trò quan trọng trong việc kiểm soát và phòng ngừa các bệnh tim mạch.</p><p>Người bệnh tim mạch nên ăn gì và kiêng gì? Hãy cùng tìm hiểu trong bài viết này.</p>', 'assets/img/tintuc_timmach.jpg', 'nutrition', 'Chế độ ăn cho người bệnh tim mạch - Lựa chọn thực phẩm tốt cho tim', 'Hướng dẫn chi tiết về chế độ ăn uống dành cho người mắc bệnh tim mạch. Các loại thực phẩm nên ăn và nên tránh để bảo vệ sức khỏe tim mạch.', 'tim mạch, dinh dưỡng, chế độ ăn, bệnh tim', 'published', NOW(), NOW()),
('Chăm sóc răng miệng đúng cách', '<h2>Chăm sóc răng miệng đúng cách</h2><p>Một nụ cười khỏe mạnh bắt đầu từ việc chăm sóc răng miệng đúng cách. Bài viết này sẽ hướng dẫn bạn các bước chăm sóc răng miệng hiệu quả tại nhà và khi nào bạn nên đến gặp nha sĩ.</p>', 'assets/img/rang.jpg', 'health', 'Chăm sóc răng miệng đúng cách - Hướng dẫn từ chuyên gia', 'Khám phá các phương pháp chăm sóc răng miệng hiệu quả và đúng cách. Bảo vệ nụ cười của bạn với những lời khuyên từ các chuyên gia nha khoa.', 'răng miệng, nha khoa, sức khỏe răng, chăm sóc răng', 'published', NOW(), NOW());

-- Chèn dữ liệu mẫu cho bảng thuốc
INSERT INTO thuoc (ten_thuoc, don_vi, gia, huong_dan_chung) VALUES
('Paracetamol', 'Viên', 10000, 'Giảm đau, hạ sốt'),
('Amoxicillin', 'Viên', 15000, 'Kháng sinh điều trị các bệnh nhiễm trùng'),
('Omeprazole', 'Viên', 20000, 'Điều trị bệnh dạ dày'),
('Loratadine', 'Viên', 12000, 'Trị dị ứng'),
('Simvastatin', 'Viên', 25000, 'Giảm cholesterol');

-- Chèn dữ liệu mẫu cho kết quả khám
INSERT INTO ketqua_kham (lichhen_id, chan_doan, mo_ta, ghi_chu) VALUES
(4, 'Viêm da dị ứng', 'Phát ban đỏ trên vùng cổ và tay', 'Nên tránh tiếp xúc với chất tẩy rửa có hóa chất mạnh');

-- Chèn dữ liệu mẫu cho đơn thuốc
INSERT INTO don_thuoc (lichhen_id, thuoc_id, lieu_dung, so_luong, cach_dung) VALUES
(4, 4, '1 viên/ngày', 10, 'Uống sau bữa ăn sáng'),
(4, 1, '1 viên x 3 lần/ngày', 30, 'Uống sau bữa ăn');

-- Chèn dữ liệu mẫu cho bảng cài đặt website
INSERT INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('site_name', 'Phòng khám Lộc Bình', 'Tên website', 'text', 'general', 1),
('site_description', 'Chăm sóc sức khỏe toàn diện', 'Mô tả ngắn về website', 'textarea', 'general', 2),
('site_logo', 'assets/img/logo.png', 'Logo website', 'image', 'general', 3),
('site_favicon', 'assets/img/favicon.png', 'Favicon website', 'image', 'general', 4),
('site_phone', '0987 654 321', 'Số điện thoại liên hệ', 'text', 'contact', 5),
('site_email', 'info@phongkhamlocbinh.vn', 'Email liên hệ', 'text', 'contact', 6),
('site_address', '123 Nguyễn Văn Linh, Quận 7, TP.HCM', 'Địa chỉ phòng khám', 'textarea', 'contact', 7),
('site_working_hours', 'Thứ 2 - Thứ 6: 7:30 - 17:00\nThứ 7: 7:30 - 12:00\nChủ nhật: Nghỉ', 'Giờ làm việc', 'textarea', 'contact', 8),
('site_facebook', 'https://facebook.com/phongkhamlocbinh', 'Facebook', 'text', 'social', 9),
('site_maps', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.6288145795835!2d106.68022805804552!3d10.765789454775936!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1b7c3ed289%3A0xa06651894598e488!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBTw6BpIEfDsm4!5e0!3m2!1svi!2s!4v1680059434003!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', 'Google Maps Embed', 'textarea', 'contact', 10),
('site_primary_color', '#0d6efd', 'Màu chủ đạo', 'color', 'appearance', 11),
('site_enable_appointment', '1', 'Bật/tắt chức năng đặt lịch khám', 'boolean', 'feature', 12);
