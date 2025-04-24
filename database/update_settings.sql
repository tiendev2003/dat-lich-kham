-- Script cập nhật bảng caidat_website với các thông số mới
-- Thực hiện script này để thêm các cài đặt mới vào cơ sở dữ liệu

-- Thêm các cài đặt mới cho giao diện (appearance)
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('primary_color', '#005bac', 'Màu chính của trang web', 'color', 'appearance', 10),
('secondary_color', '#6c757d', 'Màu phụ của trang web', 'color', 'appearance', 20),
('accent_color', '#28a745', 'Màu nhấn của trang web', 'color', 'appearance', 30),
('font_family', 'Roboto, sans-serif', 'Font chữ chính của trang web', 'text', 'appearance', 40),
('header_bg_color', '#ffffff', 'Màu nền của header', 'color', 'appearance', 50),
('footer_bg_color', '#f8f9fa', 'Màu nền của footer', 'color', 'appearance', 60);

-- Thêm cài đặt cho banner trang chủ
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('banner_title', 'Đặt lịch khám trực tuyến', 'Tiêu đề banner trang chủ', 'text', 'feature', 10),
('banner_subtitle', 'Dễ dàng - Nhanh chóng - Tiện lợi', 'Mô tả ngắn banner trang chủ', 'text', 'feature', 20),
('banner_image', 'assets/img/banner.jpg', 'Ảnh nền của banner trang chủ', 'image', 'feature', 30);

-- Thêm cài đặt cho các thông tin liên hệ
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('site_address', '67 Minh Khai, Lộc Bình, Lạng Sơn', 'Địa chỉ của phòng khám', 'text', 'contact', 10),
('site_phone', '0253 836 836', 'Số điện thoại liên hệ', 'text', 'contact', 20),
('site_email', 'phongkhamlocbinh@gmail.com', 'Email liên hệ', 'text', 'contact', 30),
('site_working_hours', 'Thứ 2 - Thứ 6: 8:00 - 17:00, Thứ 7: 8:00 - 12:00', 'Giờ làm việc', 'text', 'contact', 40),
('site_maps', 'https://maps.google.com/...', 'Link Google Maps', 'text', 'contact', 50);

-- Thêm cài đặt cho mạng xã hội
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('site_facebook', 'https://facebook.com/phongkhamlocbinh', 'Link Facebook', 'text', 'social', 10),
('site_twitter', 'https://twitter.com/phongkhamlocbinh', 'Link Twitter', 'text', 'social', 20),
('site_instagram', 'https://instagram.com/phongkhamlocbinh', 'Link Instagram', 'text', 'social', 30),
('site_youtube', 'https://youtube.com/phongkhamlocbinh', 'Link YouTube', 'text', 'social', 40);

-- Thêm hoặc cập nhật cài đặt chung
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('site_name', 'Phòng Khám Lộc Bình', 'Tên trang web', 'text', 'general', 10),
('site_description', 'Hệ thống đặt lịch khám bệnh trực tuyến - Giải pháp chăm sóc sức khỏe thông minh và tiện lợi cho mọi người.', 'Mô tả trang web', 'textarea', 'general', 20),
('site_keywords', 'phòng khám, đặt lịch khám, bác sĩ, chuyên khoa, dịch vụ y tế, lộc bình, lạng sơn', 'Từ khóa SEO', 'text', 'general', 30),
('site_logo', 'assets/img/logo.jpeg', 'Logo của trang web', 'image', 'general', 40),
('site_favicon', 'assets/img/favicon.PNG', 'Favicon của trang web', 'image', 'general', 50),
('enable_booking', '1', 'Cho phép đặt lịch khám', 'boolean', 'feature', 10),
('enable_registration', '1', 'Cho phép đăng ký tài khoản mới', 'boolean', 'feature', 20),
('auto_confirm_booking', '0', 'Tự động xác nhận đặt lịch', 'boolean', 'feature', 30);

-- Thêm cài đặt cho trang Giới thiệu
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('about_image', 'assets/img/anh-gioithieu.jpg', 'Ảnh trang giới thiệu', 'image', 'feature', 40),
('about_content', '', 'Nội dung trang giới thiệu', 'textarea', 'feature', 50);

-- Thêm cài đặt cho trang Liên hệ
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('site_hotline', '098 765 4321', 'Số hotline khẩn cấp', 'text', 'contact', 25),
('site_support_phone', '012 345 6789', 'Số điện thoại tư vấn', 'text', 'contact', 26),
('site_support_email', 'support@locbinh.com', 'Email hỗ trợ', 'text', 'contact', 35),
('site_advise_email', 'tuvan@locbinh.com', 'Email tư vấn', 'text', 'contact', 36),
('contact_image', 'assets/img/anh-gioithieu.jpg', 'Ảnh nền trang liên hệ', 'image', 'feature', 60);

-- Thêm cài đặt cho trang Dịch vụ
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('service_title', 'Dịch vụ y tế', 'Tiêu đề trang dịch vụ', 'text', 'feature', 70),
('service_subtitle', 'Chăm sóc sức khỏe toàn diện với đội ngũ bác sĩ chuyên nghiệp', 'Mô tả ngắn trang dịch vụ', 'text', 'feature', 80),
('service_banner_image', '', 'Ảnh banner trang dịch vụ', 'image', 'feature', 90);

-- Thêm cài đặt cho trang Bác sĩ
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('doctors_title', 'Đội ngũ bác sĩ', 'Tiêu đề trang bác sĩ', 'text', 'feature', 100),
('doctors_subtitle', 'Các bác sĩ chuyên nghiệp và giàu kinh nghiệm của chúng tôi', 'Mô tả ngắn trang bác sĩ', 'text', 'feature', 110),
('doctors_banner_image', '', 'Ảnh banner trang bác sĩ', 'image', 'feature', 120);

-- Thêm cài đặt cho trang Chuyên khoa
INSERT IGNORE INTO caidat_website (ten_key, ten_value, mo_ta, loai, nhom, thu_tu) VALUES
('specialties_title', 'Chuyên khoa', 'Tiêu đề trang chuyên khoa', 'text', 'feature', 130),
('specialties_subtitle', 'Các chuyên khoa khám và điều trị tại phòng khám', 'Mô tả ngắn trang chuyên khoa', 'text', 'feature', 140),
('specialties_banner_image', '', 'Ảnh banner trang chuyên khoa', 'image', 'feature', 150);