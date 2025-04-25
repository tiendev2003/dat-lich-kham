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
    static $settings = null;
    
    // Nếu chưa lấy cài đặt, thử nạp từ file đã đồng bộ trước
    if ($settings === null) {
        // Kiểm tra xem file settings_data.php đã được tạo chưa
        if (file_exists(__DIR__ . '/settings_data.php')) {
            include_once __DIR__ . '/settings_data.php';
            if (isset($settings_data) && is_array($settings_data)) {
                $settings = $settings_data;
                // Kiểm tra xem có cần làm mới dữ liệu không
                if (file_exists(__DIR__ . '/settings_cache.php')) {
                    include_once __DIR__ . '/settings_cache.php';
                    
                    // Nếu cache đã hết hạn hoặc cần làm mới
                    if (isset($settings_needs_refresh) && $settings_needs_refresh) {
                        // Lấy dữ liệu mới từ database
                        $settings = refresh_settings_from_database();
                    }
                }
            }
        }
        
        // Nếu không có file hoặc cần làm mới, lấy từ database
        if ($settings === null) {
            $settings = refresh_settings_from_database();
        }
    }
    
    // Trả về giá trị cài đặt hoặc giá trị mặc định
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Làm mới cài đặt từ cơ sở dữ liệu và cập nhật file cache
 *
 * @return array Mảng cài đặt từ database
 */
function refresh_settings_from_database() {
    global $conn;
    $settings = [];
    $result = $conn->query("SELECT ten_key, ten_value FROM caidat_website");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $settings[$row['ten_key']] = $row['ten_value'];
        }
    }
    
    // Cập nhật file settings_data.php
    update_settings_data_file($settings);
    
    // Cập nhật thông tin cache
    update_settings_cache_file();
    
    return $settings;
}

/**
 * Cập nhật file settings_data.php với dữ liệu mới
 *
 * @param array $settings Dữ liệu cài đặt
 * @return bool Thành công hay thất bại
 */
function update_settings_data_file($settings) {
    $file_path = __DIR__ . '/settings_data.php';
    $content = "<?php\n// File được sinh tự động từ phần cài đặt hệ thống\n";
    $content .= "// Cập nhật lần cuối: " . date('Y-m-d H:i:s') . "\n\n";
    $content .= "\$settings_data = " . var_export($settings, true) . ";\n?>";
    
    return file_put_contents($file_path, $content) !== false;
}

/**
 * Cập nhật file settings_cache.php với timestamp hiện tại
 *
 * @return bool Thành công hay thất bại
 */
function update_settings_cache_file() {
    $file_path = __DIR__ . '/settings_cache.php';
    $current_time = time();
    
    $content = "<?php\n// Thông tin cache cài đặt hệ thống\n";
    $content .= "// File này được sử dụng để kiểm tra xem settings_data.php có cần được cập nhật không\n\n";
    $content .= "// Thời gian cập nhật cuối cùng (UNIX timestamp)\n";
    $content .= "\$settings_last_updated = $current_time; // Tương đương với " . date('Y-m-d H:i:s', $current_time) . "\n\n";
    $content .= "// Thời gian cache hết hạn (tính bằng giây)\n";
    $content .= "\$settings_cache_expiry = 3600; // 1 giờ\n\n";
    $content .= "// Kiểm tra xem cache còn hạn hay không\n";
    $content .= "\$settings_cache_valid = (time() - \$settings_last_updated) < \$settings_cache_expiry;\n\n";
    $content .= "// Đánh dấu cần làm mới nếu cache không còn hạn\n";
    $content .= "\$settings_needs_refresh = !\$settings_cache_valid;\n?>";
    
    return file_put_contents($file_path, $content) !== false;
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

/**
 * Kiểm tra xem người dùng đã đăng nhập chưa
 * 
 * @return bool True nếu đã đăng nhập, false nếu chưa
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Lấy thông tin người dùng đang đăng nhập
 * 
 * @return array|null Thông tin người dùng hoặc null nếu chưa đăng nhập
 */
function get_logged_in_user() {
    global $conn;
    
    if (!is_logged_in()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM nguoidung WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Lấy thông tin bệnh nhân từ tài khoản người dùng
 * 
 * @param int $user_id ID của người dùng
 * @return array|null Thông tin bệnh nhân hoặc null nếu không tìm thấy
 */
function get_patient_info($user_id) {
    global $conn;
    
    $query = "SELECT * FROM benhnhan WHERE nguoidung_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Đăng nhập người dùng
 * 
 * @param string $email Email đăng nhập
 * @param string $password Mật khẩu đăng nhập
 * @return array Kết quả đăng nhập [success: bool, message: string, user_data: array|null]
 */
function login_user($email, $password) {
    global $conn;
    
    // Kiểm tra email
    $query = "SELECT * FROM nguoidung WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return [
            'success' => false,
            'message' => 'Email không tồn tại trong hệ thống',
            'user_data' => null
        ];
    }
    
    $user = $result->fetch_assoc();
    
    // Kiểm tra mật khẩu
    if (!password_verify($password, $user['mat_khau'])) {
        return [
            'success' => false,
            'message' => 'Mật khẩu không chính xác',
            'user_data' => null
        ];
    }
    
    // Kiểm tra trạng thái tài khoản
    if ($user['trang_thai'] != 1) {
        return [
            'success' => false,
            'message' => 'Tài khoản đã bị khóa hoặc chưa kích hoạt',
            'user_data' => null
        ];
    }
    
    // Lưu thông tin người dùng vào session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['vai_tro'] = $user['vai_tro'];
    $_SESSION['email'] = $user['email'];
    
    // Cập nhật thời gian đăng nhập (nếu cần)
    // $stmt = $conn->prepare("UPDATE nguoidung SET last_login = NOW() WHERE id = ?");
    // $stmt->bind_param("i", $user['id']);
    // $stmt->execute();
    
    return [
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'user_data' => $user
    ];
}

/**
 * Đăng ký người dùng mới
 * 
 * @param array $user_data Thông tin người dùng
 * @param array $patient_data Thông tin bệnh nhân
 * @return array Kết quả đăng ký [success: bool, message: string, user_id: int|null]
 */
function register_user($user_data, $patient_data) {
    global $conn;
    
    // Bắt đầu transaction
    $conn->begin_transaction();
    
    try {
        // Kiểm tra email đã tồn tại chưa
        $query = "SELECT id FROM nguoidung WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $user_data['email']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'Email đã được đăng ký, vui lòng sử dụng email khác',
                'user_id' => null
            ];
        }
        
        // Mã hóa mật khẩu
        $hashed_password = password_hash($user_data['password'], PASSWORD_DEFAULT);
        
        // Thêm người dùng
        $query = "INSERT INTO nguoidung (email, mat_khau, vai_tro, trang_thai) VALUES (?, ?, 'benhnhan', 1)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $user_data['email'], $hashed_password);
        $stmt->execute();
        
        $user_id = $conn->insert_id;
        
        // Thêm thông tin bệnh nhân
        $query = "INSERT INTO benhnhan (nguoidung_id, ho_ten, nam_sinh, gioi_tinh, dien_thoai, email, dia_chi) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "iisssss", 
            $user_id, 
            $patient_data['fullname'], 
            $patient_data['birth_year'],
            $patient_data['gender'],
            $patient_data['phone'],
            $user_data['email'],
            $patient_data['address']
        );
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        return [
            'success' => true,
            'message' => 'Đăng ký thành công',
            'user_id' => $user_id
        ];
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        
        return [
            'success' => false,
            'message' => 'Đã xảy ra lỗi: ' . $e->getMessage(),
            'user_id' => null
        ];
    }
}

/**
 * Đăng xuất người dùng
 */
function logout_user() {
    // Xóa tất cả dữ liệu session
    $_SESSION = [];
    
    // Xóa cookie session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Hủy session
    session_destroy();
}

/**
 * Chuyển hướng người dùng đến trang phù hợp sau khi đăng nhập
 * 
 * @param string $role Vai trò của người dùng
 */
function redirect_by_role($role) {
    switch ($role) {
        case 'admin':
            header('Location: admin/tongquan.php');
            break;
        case 'bacsi':
            header('Location: doctor/index.php');
            break;
        case 'benhnhan':
            header('Location: index.php');
            break;
        default:
            header('Location: index.php');
    }
    exit;
}
