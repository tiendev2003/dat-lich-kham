<?php
/**
 * Các hàm tiện ích cho hệ thống
 */

/**
 * Lấy giá trị của một cài đặt website từ cơ sở dữ liệu
 * 
 * @param string $key Khóa cài đặt
 * @param mixed $default Giá trị mặc định nếu không tìm thấy
 * @return mixed Giá trị cài đặt
 */
function get_setting($key, $default = '') {
    global $conn;
    static $settings = null;
    
    // Nếu chưa lấy cài đặt từ database, lấy và lưu vào cache
    if ($settings === null) {
        $settings = [];
        $result = $conn->query("SELECT ten_key, ten_value FROM caidat_website");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['ten_key']] = $row['ten_value'];
            }
        }
    }
    
    // Trả về giá trị cài đặt hoặc giá trị mặc định
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Hiển thị logo của website
 * 
 * @param string $class Class CSS bổ sung
 * @param int $height Chiều cao tối đa của logo
 * @return string HTML của logo
 */
function display_logo($class = '', $height = 60) {
    $logo_url = get_setting('site_logo', 'assets/img/logo.png');
    $site_name = get_setting('site_name', 'Phòng khám Lộc Bình');
    
    return '<img src="' . $logo_url . '" alt="' . $site_name . '" class="' . $class . '" style="max-height: ' . $height . 'px;">';
}

/**
 * Lấy thông tin liên hệ dưới dạng mảng
 * 
 * @return array Thông tin liên hệ
 */
function get_contact_info() {
    return [
        'address' => get_setting('site_address'),
        'phone' => get_setting('site_phone'),
        'email' => get_setting('site_email'),
        'working_hours' => get_setting('site_working_hours'),
        'facebook' => get_setting('site_facebook'),
        'maps' => get_setting('site_maps')
    ];
}

/**
 * Tạo tiêu đề HTML cho trang web
 * 
 * @param string $page_title Tiêu đề trang cụ thể
 * @return string Tiêu đề HTML đầy đủ
 */
function get_page_title($page_title = '') {
    $site_name = get_setting('site_name', 'Phòng khám Lộc Bình');
    
    if (!empty($page_title)) {
        return $page_title . ' - ' . $site_name;
    }
    
    return $site_name;
}

/**
 * Hiển thị favicon trong thẻ head
 * 
 * @return string HTML để thêm favicon
 */
function display_favicon() {
    $favicon = get_setting('site_favicon', 'assets/img/favicon.png');
    return '<link rel="shortcut icon" href="' . $favicon . '" type="image/x-icon">';
}
