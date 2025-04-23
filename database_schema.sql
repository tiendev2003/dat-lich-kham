-- Cơ sở dữ liệu: dat_lich_kham

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `TaiKhoan` (Accounts/Users)
-- Lưu trữ thông tin đăng nhập và vai trò
--
CREATE TABLE `TaiKhoan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ten_dang_nhap` VARCHAR(100) UNIQUE NOT NULL,
  `mat_khau` VARCHAR(255) NOT NULL, -- Nên lưu mật khẩu đã được hash
  `email` VARCHAR(150) UNIQUE NOT NULL,
  `vai_tro` ENUM('patient', 'doctor', 'admin') NOT NULL DEFAULT 'patient',
  `ngay_tao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ChuyenKhoa` (Specialties)
--
CREATE TABLE `ChuyenKhoa` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ten_chuyen_khoa` VARCHAR(255) NOT NULL UNIQUE,
  `mo_ta` TEXT,
  `icon` VARCHAR(100) NULL, -- Lưu class Font Awesome hoặc path ảnh nhỏ
  `hinh_anh` VARCHAR(255) NULL -- Path ảnh lớn hơn cho trang chi tiết
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `BacSi` (Doctors)
--
CREATE TABLE `BacSi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_tai_khoan` INT UNIQUE NOT NULL, -- Liên kết với bảng TaiKhoan
  `ho_ten` VARCHAR(150) NOT NULL,
  `id_chuyen_khoa` INT NOT NULL, -- Liên kết với bảng ChuyenKhoa
  `mo_ta` TEXT NULL, -- Kinh nghiệm, bằng cấp,...
  `so_dien_thoai` VARCHAR(20) NULL,
  `anh_dai_dien` VARCHAR(255) NULL, -- Path tới file ảnh
  `lich_lam_viec` TEXT NULL, -- Mô tả lịch làm việc (có thể tách bảng riêng nếu phức tạp)
  FOREIGN KEY (`id_tai_khoan`) REFERENCES `TaiKhoan`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_chuyen_khoa`) REFERENCES `ChuyenKhoa`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `BenhNhan` (Patients)
--
CREATE TABLE `BenhNhan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_tai_khoan` INT UNIQUE NOT NULL, -- Liên kết với bảng TaiKhoan
  `ho_ten` VARCHAR(150) NOT NULL,
  `ngay_sinh` DATE NULL,
  `gioi_tinh` ENUM('Nam', 'Nữ', 'Khác') NULL,
  `so_dien_thoai` VARCHAR(20) NULL,
  `dia_chi` TEXT NULL,
  `anh_dai_dien` VARCHAR(255) NULL, -- Path tới file ảnh
  FOREIGN KEY (`id_tai_khoan`) REFERENCES `TaiKhoan`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `DichVu` (Services)
--
CREATE TABLE `DichVu` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ten_dich_vu` VARCHAR(255) NOT NULL,
  `mo_ta` TEXT NULL,
  `gia` DECIMAL(12, 2) NULL, -- Giá dịch vụ
  `id_chuyen_khoa` INT NULL, -- Dịch vụ có thể thuộc chuyên khoa nào đó
  FOREIGN KEY (`id_chuyen_khoa`) REFERENCES `ChuyenKhoa`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `LichHen` (Appointments)
--
CREATE TABLE `LichHen` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_benh_nhan` INT NOT NULL, -- Liên kết với bảng BenhNhan
  `id_bac_si` INT NOT NULL, -- Liên kết với bảng BacSi
  `id_dich_vu` INT NULL, -- Có thể chọn dịch vụ cụ thể khi đặt lịch
  `ngay_gio_kham` DATETIME NOT NULL,
  `ly_do_kham` TEXT NULL,
  `trang_thai` ENUM('Chờ xác nhận', 'Đã xác nhận', 'Đã hủy', 'Đã hoàn thành') NOT NULL DEFAULT 'Chờ xác nhận',
  `ghi_chu_bac_si` TEXT NULL, -- Ghi chú của bác sĩ sau khi khám
  `ngay_dat` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_benh_nhan`) REFERENCES `BenhNhan`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_bac_si`) REFERENCES `BacSi`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`id_dich_vu`) REFERENCES `DichVu`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `TinTuc` (News)
--
CREATE TABLE `TinTuc` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `tieu_de` VARCHAR(255) NOT NULL,
  `noi_dung` TEXT NOT NULL,
  `hinh_anh` VARCHAR(255) NULL, -- Path tới ảnh minh họa
  `ngay_dang` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `id_tac_gia` INT NULL, -- Người đăng tin (có thể là admin hoặc bác sĩ)
  FOREIGN KEY (`id_tac_gia`) REFERENCES `TaiKhoan`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;