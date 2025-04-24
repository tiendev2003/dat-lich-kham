<?php
// Đảm bảo chỉ bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load các hàm cần thiết
if (!function_exists('get_setting')) {
    require_once 'functions.php';
}

/**
 * Tạo tiêu đề cho trang web
 * 
 * @param string $page_title Tiêu đề riêng của trang hiện tại
 * @return string Tiêu đề đầy đủ
 */
function generate_title($page_title = '') {
    $site_name = get_setting('site_name', 'Hệ thống đặt lịch khám bệnh');
    
    if (!empty($page_title)) {
        return htmlspecialchars($page_title) . ' - ' . htmlspecialchars($site_name);
    }
    
    return htmlspecialchars($site_name);
}

// Lấy các thông số cài đặt website
$site_name = get_setting('site_name', 'Hệ thống đặt lịch khám bệnh');
$site_description = get_setting('site_description', 'Hệ thống đặt lịch khám bệnh trực tuyến');
$site_keywords = get_setting('site_keywords', 'đặt lịch khám, bác sĩ, phòng khám, dịch vụ y tế');
$primary_color = get_setting('primary_color', '#005bac');
$secondary_color = get_setting('secondary_color', '#6c757d');
$accent_color = get_setting('accent_color', '#28a745');
$font_family = get_setting('font_family', 'Roboto, sans-serif');
$favicon = get_setting('site_favicon', 'assets/img/favicon.png');

// Lấy tiêu đề trang từ biến toàn cục nếu có, hoặc mặc định là tên trang
$page_title = isset($GLOBALS['page_title']) ? $GLOBALS['page_title'] : '';
$full_title = generate_title($page_title);

// CSS tùy chỉnh từ cài đặt
$custom_css = "
<style>
    :root {
        --primary-color: {$primary_color};
        --secondary-color: {$secondary_color};
        --accent-color: {$accent_color};
        --font-family: {$font_family};
    }
    body {
        font-family: var(--font-family);
    }
    .btn-primary, .bg-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }
    .btn-outline-primary {
        color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }
    .btn-outline-primary:hover {
        background-color: var(--primary-color) !important;
        color: #fff !important;
    }
    .text-primary {
        color: var(--primary-color) !important;
    }
    a {
        color: var(--primary-color);
    }
    a:hover {
        color: " . adjustBrightness($primary_color, -20) . ";
    }
    /* Các quy tắc CSS tùy chỉnh khác */
</style>
";

/**
 * Điều chỉnh độ sáng của màu HEX
 * @param string $hex Mã màu HEX
 * @param int $steps Mức độ điều chỉnh (-255 đến 255)
 * @return string Mã màu HEX mới
 */
function adjustBrightness($hex, $steps) {
    // Loại bỏ dấu # nếu có
    $hex = ltrim($hex, '#');
    
    // Chuyển đổi sang RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Điều chỉnh độ sáng
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    // Chuyển lại thành HEX
    return '#' . sprintf('%02x%02x%02x', $r, $g, $b);
}
?>
<!-- Meta Tags -->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="<?php echo htmlspecialchars($site_description); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($site_keywords); ?>">
<meta name="author" content="<?php echo htmlspecialchars($site_name); ?>">

<!-- Favicon -->
<link rel="shortcut icon" href="<?php echo $favicon; ?>" type="image/x-icon">

<!-- Title -->
<title><?php echo $full_title; ?></title>

<!-- Custom CSS -->
<?php echo $custom_css; ?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Custom styles -->
<link rel="stylesheet" href="assets/css/style.css">