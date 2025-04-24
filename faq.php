<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
$isLogged = is_logged_in();
if ($isLogged) {
    $user = get_logged_in_user();
}

// Thiết lập tiêu đề trang cho head.php
$GLOBALS['page_title'] = 'Câu hỏi thường gặp (FAQ)';

// Get settings
$site_name = get_setting('site_name', 'Phòng Khám Lộc Bình');
$primary_color = get_setting('primary_color', '#0d6efd');
$primary_color_rgb = hex_to_rgb($primary_color);

// Helper function to convert hex color to RGB
function hex_to_rgb($hex) {
    $hex = str_replace('#', '', $hex);
    
    if(strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    
    return "$r, $g, $b";
}

// Dữ liệu mẫu cho danh mục FAQ
$dummy_categories = [
    ['id' => 1, 'ten_danhmuc' => 'Đặt lịch'],
    ['id' => 2, 'ten_danhmuc' => 'Thanh toán'],
    ['id' => 3, 'ten_danhmuc' => 'Y tế & Điều trị'],
    ['id' => 4, 'ten_danhmuc' => 'Tài khoản'],
    ['id' => 5, 'ten_danhmuc' => 'Thông tin chung']
];

// Dữ liệu mẫu cho các câu hỏi thường gặp
$dummy_faqs = [
    // Đặt lịch
    [
        'id' => 1,
        'danhmuc_id' => 1,
        'cauhoi' => 'Làm thế nào để đặt lịch khám?',
        'cautraloi' => 'Để đặt lịch khám tại phòng khám, bạn có thể thực hiện theo các cách sau:
1. Đặt lịch online qua website bằng cách điền thông tin vào form đặt lịch
2. Gọi điện đến số hotline 1900 1234
3. Đến trực tiếp phòng khám để đăng ký lịch khám

Khi đặt lịch, bạn cần cung cấp đầy đủ thông tin cá nhân, chọn bác sĩ, chuyên khoa và thời gian mong muốn khám.',
        'phobien' => 1
    ],
    [
        'id' => 2,
        'danhmuc_id' => 1,
        'cauhoi' => 'Tôi có thể đặt lịch khám trước bao lâu?',
        'cautraloi' => 'Bạn có thể đặt lịch khám trước tối đa 30 ngày kể từ ngày hiện tại. Chúng tôi khuyến khích đặt lịch sớm để có nhiều lựa chọn về thời gian và bác sĩ.',
        'phobien' => 0
    ],
    [
        'id' => 3,
        'danhmuc_id' => 1,
        'cauhoi' => 'Làm thế nào để hủy hoặc thay đổi lịch khám?',
        'cautraloi' => 'Để hủy hoặc thay đổi lịch khám, bạn có thể:
1. Đăng nhập vào tài khoản và vào mục "Lịch sử đặt lịch" để thực hiện hủy hoặc đổi lịch
2. Gọi điện đến số hotline 1900 1234
3. Liên hệ trực tiếp với phòng khám

Lưu ý: Việc hủy lịch nên được thực hiện ít nhất 24 giờ trước thời gian khám đã đặt để tránh mất phí đặt cọc.',
        'phobien' => 1
    ],
    [
        'id' => 4,
        'danhmuc_id' => 1,
        'cauhoi' => 'Tôi có cần đặt lịch trước khi đến khám không?',
        'cautraloi' => 'Chúng tôi khuyến khích bệnh nhân đặt lịch trước khi đến khám để giảm thời gian chờ đợi và đảm bảo được gặp bác sĩ vào thời gian phù hợp. Tuy nhiên, phòng khám vẫn tiếp nhận các trường hợp khám không có lịch hẹn trước, nhưng sẽ phải chờ đợi theo thứ tự ưu tiên sau các bệnh nhân đã có lịch hẹn.',
        'phobien' => 0
    ],
    [
        'id' => 5,
        'danhmuc_id' => 1,
        'cauhoi' => 'Có cần mang theo giấy tờ gì khi đến khám không?',
        'cautraloi' => 'Khi đến khám, bạn cần mang theo:
1. CMND/CCCD hoặc hộ chiếu
2. Giấy tờ BHYT (nếu có)
3. Các kết quả xét nghiệm, chẩn đoán hình ảnh trước đó (nếu có)
4. Các đơn thuốc và hồ sơ bệnh án liên quan (nếu có)',
        'phobien' => 0
    ],

    // Thanh toán
    [
        'id' => 6,
        'danhmuc_id' => 2,
        'cauhoi' => 'Phòng khám chấp nhận những hình thức thanh toán nào?',
        'cautraloi' => 'Phòng khám chấp nhận các hình thức thanh toán sau:
1. Tiền mặt
2. Thẻ ATM nội địa
3. Thẻ tín dụng/ghi nợ quốc tế (Visa, Mastercard, JCB)
4. Chuyển khoản ngân hàng
5. Các ví điện tử: MoMo, ZaloPay, VNPay

Chúng tôi đang trong quá trình phát triển hệ thống thanh toán online trên website để thuận tiện hơn cho bệnh nhân.',
        'phobien' => 1
    ],
    [
        'id' => 7,
        'danhmuc_id' => 2,
        'cauhoi' => 'Chi phí khám và điều trị được tính như thế nào?',
        'cautraloi' => 'Chi phí khám bệnh bao gồm:
1. Phí khám chuyên khoa: từ 300.000đ đến 500.000đ tùy theo chuyên khoa và bác sĩ
2. Chi phí xét nghiệm (nếu có): theo chỉ định của bác sĩ
3. Chi phí thuốc và vật tư y tế (nếu có): theo đơn thuốc

Phòng khám cam kết công khai, minh bạch mọi khoản chi phí trước khi thực hiện dịch vụ. Bạn sẽ được tư vấn đầy đủ về chi phí dự kiến trước khi quyết định sử dụng dịch vụ.',
        'phobien' => 1
    ],
    [
        'id' => 8,
        'danhmuc_id' => 2,
        'cauhoi' => 'Phòng khám có thanh toán qua bảo hiểm y tế không?',
        'cautraloi' => 'Hiện tại phòng khám đang trong quá trình đăng ký trở thành đơn vị khám chữa bệnh BHYT. Hiện chưa áp dụng thanh toán trực tiếp qua BHYT.

Tuy nhiên, chúng tôi sẽ cung cấp đầy đủ hóa đơn, chứng từ để bạn có thể làm thủ tục yêu cầu bồi hoàn từ công ty bảo hiểm tư nhân (nếu hợp đồng bảo hiểm của bạn có áp dụng).',
        'phobien' => 0
    ],
    [
        'id' => 9,
        'danhmuc_id' => 2,
        'cauhoi' => 'Tôi có được hoàn trả phí đặt lịch khi hủy lịch hẹn không?',
        'cautraloi' => 'Chính sách hoàn phí đặt lịch như sau:
- Hủy lịch trước 24 giờ so với thời gian hẹn: hoàn trả 100% phí đặt lịch
- Hủy lịch trong vòng 12-24 giờ: hoàn trả 50% phí đặt lịch
- Hủy lịch dưới 12 giờ: không hoàn trả phí đặt lịch

Đối với các trường hợp bất khả kháng như tình trạng sức khỏe khẩn cấp (có giấy tờ chứng minh), chúng tôi sẽ xem xét hoàn phí đặt lịch đầy đủ.',
        'phobien' => 0
    ],

    // Y tế & Điều trị
    [
        'id' => 10,
        'danhmuc_id' => 3,
        'cauhoi' => 'Phòng khám có những chuyên khoa nào?',
        'cautraloi' => 'Phòng khám cung cấp các dịch vụ khám và điều trị cho các chuyên khoa sau:
1. Da liễu
2. Hô hấp
3. Mắt
4. Răng Hàm Mặt
5. Tim mạch
6. Tiêu hóa
7. Thần kinh
8. Xét nghiệm và chẩn đoán hình ảnh

Mỗi chuyên khoa đều có các bác sĩ chuyên môn cao, nhiều năm kinh nghiệm và trang thiết bị hiện đại đảm bảo chất lượng khám chữa bệnh.',
        'phobien' => 1
    ],
    [
        'id' => 11,
        'danhmuc_id' => 3,
        'cauhoi' => 'Quy trình khám bệnh tại phòng khám như thế nào?',
        'cautraloi' => 'Quy trình khám bệnh tại phòng khám gồm các bước:
1. Đăng ký/check-in tại quầy lễ tân (xuất trình giấy tờ và mã lịch hẹn nếu đã đặt trước)
2. Thanh toán phí khám ban đầu
3. Chờ đến lượt khám theo số thứ tự
4. Gặp bác sĩ khám và tư vấn
5. Thực hiện các xét nghiệm, chẩn đoán hình ảnh theo chỉ định (nếu có)
6. Nhận kết quả và quay lại gặp bác sĩ
7. Nhận đơn thuốc và hướng dẫn điều trị
8. Thanh toán chi phí còn lại
9. Đặt lịch tái khám (nếu cần)',
        'phobien' => 0
    ],
    [
        'id' => 12,
        'danhmuc_id' => 3,
        'cauhoi' => 'Làm thế nào để lấy kết quả xét nghiệm?',
        'cautraloi' => 'Bạn có thể nhận kết quả xét nghiệm bằng các cách sau:
1. Nhận trực tiếp tại phòng khám khi có kết quả
2. Đăng nhập vào tài khoản trên website/ứng dụng để xem và tải kết quả (đối với bệnh nhân đã có tài khoản)
3. Nhận qua email (nếu đã đăng ký nhận kết quả qua email)

Thời gian có kết quả xét nghiệm thông thường là 1-3 ngày làm việc tùy loại xét nghiệm. Với các xét nghiệm cơ bản, kết quả sẽ có trong ngày.',
        'phobien' => 1
    ],
    [
        'id' => 13,
        'danhmuc_id' => 3,
        'cauhoi' => 'Phòng khám có phòng cấp cứu 24/7 không?',
        'cautraloi' => 'Phòng khám không có dịch vụ cấp cứu 24/7. Chúng tôi chỉ tiếp nhận khám và điều trị theo giờ làm việc từ 8:00 đến 17:00 các ngày từ thứ 2 đến thứ 7.

Đối với các trường hợp cấp cứu, vui lòng đến các bệnh viện có khoa cấp cứu. Chúng tôi sẽ cung cấp thông tin và hỗ trợ chuyển viện nếu phát hiện trường hợp cần cấp cứu trong giờ làm việc của phòng khám.',
        'phobien' => 0
    ],

    // Tài khoản
    [
        'id' => 14,
        'danhmuc_id' => 4,
        'cauhoi' => 'Làm thế nào để tạo tài khoản trên website phòng khám?',
        'cautraloi' => 'Bạn có thể tạo tài khoản trên website của phòng khám bằng cách:
1. Vào trang "Đăng ký" hoặc "Tạo tài khoản"
2. Điền đầy đủ thông tin cá nhân theo yêu cầu
3. Xác thực email hoặc số điện thoại
4. Đăng nhập với thông tin tài khoản vừa tạo

Việc có tài khoản sẽ giúp bạn dễ dàng đặt lịch, theo dõi lịch sử khám chữa bệnh, xem kết quả xét nghiệm và nhận các thông tin hữu ích từ phòng khám.',
        'phobien' => 0
    ],
    [
        'id' => 15,
        'danhmuc_id' => 4,
        'cauhoi' => 'Tôi quên mật khẩu tài khoản phải làm thế nào?',
        'cautraloi' => 'Nếu bạn quên mật khẩu tài khoản, bạn có thể thực hiện các bước sau:
1. Vào trang "Đăng nhập" và nhấp vào liên kết "Quên mật khẩu"
2. Nhập email hoặc số điện thoại bạn đã dùng để đăng ký tài khoản
3. Kiểm tra email/tin nhắn để nhận mã xác nhận hoặc đường dẫn đặt lại mật khẩu
4. Tạo mật khẩu mới và đăng nhập lại

Nếu bạn vẫn gặp vấn đề, vui lòng liên hệ với bộ phận hỗ trợ kỹ thuật theo số 1900 1234.',
        'phobien' => 0
    ],
    [
        'id' => 16,
        'danhmuc_id' => 4,
        'cauhoi' => 'Tài khoản của tôi có lưu thông tin bệnh án không?',
        'cautraloi' => 'Có, tài khoản của bạn sẽ lưu trữ các thông tin về bệnh án, lịch sử khám chữa bệnh, đơn thuốc và kết quả xét nghiệm tại phòng khám. Điều này giúp bạn và bác sĩ dễ dàng theo dõi tình trạng sức khỏe qua thời gian.

Thông tin được bảo mật nghiêm ngặt và chỉ bạn và nhân viên y tế có thẩm quyền mới có thể truy cập. Chúng tôi tuân thủ các quy định về bảo vệ thông tin y tế cá nhân.',
        'phobien' => 1
    ],

    // Thông tin chung
    [
        'id' => 17,
        'danhmuc_id' => 5,
        'cauhoi' => 'Giờ làm việc của phòng khám?',
        'cautraloi' => 'Phòng khám làm việc từ 8:00 đến 17:00 các ngày từ thứ 2 đến thứ 7. 
Sáng: 8:00 - 11:30
Chiều: 14:00 - 17:00
Nghỉ trưa: 11:30 - 14:00
Chủ nhật và các ngày lễ: nghỉ (trừ các trường hợp có thông báo làm việc đặc biệt)',
        'phobien' => 1
    ],
    [
        'id' => 18,
        'danhmuc_id' => 5,
        'cauhoi' => 'Phòng khám có bãi đỗ xe không?',
        'cautraloi' => 'Có, phòng khám có bãi đỗ xe dành cho bệnh nhân và người nhà. Bãi đỗ xe máy miễn phí và có sức chứa lớn. 

Đối với ô tô, chúng tôi có bãi đỗ xe riêng với mức phí 20.000đ/lượt. Bãi đỗ xe ô tô có số lượng giới hạn nên trong trường hợp đông bệnh nhân, chúng tôi khuyến khích sử dụng các bãi đỗ xe công cộng gần phòng khám.',
        'phobien' => 0
    ],
    [
        'id' => 19,
        'danhmuc_id' => 5,
        'cauhoi' => 'Phòng khám có dịch vụ khám tại nhà không?',
        'cautraloi' => 'Hiện tại phòng khám có cung cấp dịch vụ khám tại nhà cho một số trường hợp đặc biệt như bệnh nhân cao tuổi, bệnh nhân khó khăn trong việc di chuyển, hoặc theo yêu cầu với chi phí phụ thu.

Để đặt dịch vụ khám tại nhà, bạn cần liên hệ trước ít nhất 48 giờ qua hotline 1900 1234 hoặc email info@locbinh.vn. Phạm vi phục vụ hiện tại giới hạn trong khu vực nội thành.',
        'phobien' => 0
    ],
    [
        'id' => 20,
        'danhmuc_id' => 5,
        'cauhoi' => 'Địa chỉ của phòng khám?',
        'cautraloi' => 'Địa chỉ của phòng khám: 123 Đường Lộc Bình, Phường Tân Phú, Quận 7, TP. Hồ Chí Minh.

Các phương tiện công cộng đến phòng khám:
- Các tuyến xe buýt: 39, 53, 79
- Cách trạm metro (đang xây dựng): 500m
- Có thể đi Grab, taxi đến trực tiếp phòng khám',
        'phobien' => 1
    ]
];

// Function để lấy danh mục FAQs
function get_dummy_categories() {
    global $dummy_categories;
    return $dummy_categories;
}

// Function để lấy các câu hỏi phổ biến
function get_dummy_popular_faqs() {
    global $dummy_faqs;
    $popular = array_filter($dummy_faqs, function($faq) {
        return isset($faq['phobien']) && $faq['phobien'] == 1;
    });
    return array_slice($popular, 0, 5);
}

// Function để lấy FAQs theo danh mục
function get_dummy_faqs_by_category($category_id) {
    global $dummy_faqs;
    return array_filter($dummy_faqs, function($faq) use ($category_id) {
        return $faq['danhmuc_id'] == $category_id;
    });
}

// Function tìm kiếm FAQs
function search_dummy_faqs($query) {
    global $dummy_faqs, $dummy_categories;
    $results = [];
    
    foreach ($dummy_faqs as $faq) {
        if (stripos($faq['cauhoi'], $query) !== false || stripos($faq['cautraloi'], $query) !== false) {
            // Add category name
            foreach ($dummy_categories as $cat) {
                if ($cat['id'] == $faq['danhmuc_id']) {
                    $faq['ten_danhmuc'] = $cat['ten_danhmuc'];
                    break;
                }
            }
            $results[] = $faq;
        }
    }
    
    return $results;
}

// Log search query if provided
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search_query = trim($_GET['query']);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $user_id = $isLogged ? $user['id'] : 0;
    
    // Log search query to a file instead of database
    $log_dir = 'logs';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/search_logs_' . date('Y-m') . '.log';
    $log_data = date('Y-m-d H:i:s') . '|' . $search_query . '|' . $ip_address . '|' . $user_agent . '|' . $user_id . '|faq' . PHP_EOL;
    file_put_contents($log_file, $log_data, FILE_APPEND);
    
    // Search FAQs
    $search_results = search_dummy_faqs($search_query);
}

// Get dummy data
$categories = get_dummy_categories();
$popular_faqs = get_dummy_popular_faqs();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?php include 'includes/head.php'; ?>
    <style>
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --primary-color-rgb: <?php echo $primary_color_rgb; ?>;
        }

        /* FAQ Styles */
        .faq-container {
            padding: 50px 0;
            background-color: #f8f9fa;
        }
        
        .faq-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .faq-header h1 {
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .search-container {
            max-width: 700px;
            margin: 0 auto 40px;
        }
        
        .search-form-wrapper {
            background-color: #fff;
            padding: 10px;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: all 0.3s ease;
        }
        
        .search-form-wrapper:hover, 
        .search-form-wrapper:focus-within {
            box-shadow: 0 8px 25px rgba(var(--primary-color-rgb), 0.2);
            transform: translateY(-2px);
        }
        
        .search-form {
            display: flex;
            align-items: center;
        }
        
        .search-icon {
            padding: 0 15px;
            font-size: 1.2rem;
            color: rgba(var(--primary-color-rgb), 0.7);
        }
        
        .search-form .form-control {
            height: 50px;
            border: none;
            box-shadow: none;
            font-size: 1rem;
            padding: 0;
            flex-grow: 1;
            background: transparent;
        }
        
        .search-form .form-control:focus {
            box-shadow: none;
            outline: none;
        }
        
        .search-form .btn {
            height: 42px;
            min-width: 120px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(var(--primary-color-rgb), 0.3);
            margin-right: 5px;
        }
        
        .search-form .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(var(--primary-color-rgb), 0.4);
        }
        
        .search-form .btn .fas {
            margin-right: 6px;
        }
        
        .search-suggestions {
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
            margin-top: 15px;
        }
        
        .search-suggestions span {
            display: inline-block;
            background-color: rgba(var(--primary-color-rgb), 0.1);
            color: var(--primary-color);
            padding: 3px 12px;
            border-radius: 15px;
            margin: 5px 3px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .search-suggestions span:hover {
            background-color: rgba(var(--primary-color-rgb), 0.2);
        }
        
        @media (max-width: 768px) {
            .search-form .form-control {
                font-size: 0.9rem;
            }
            
            .search-form .btn {
                min-width: 90px;
                font-size: 0.75rem;
            }
            
            .search-icon {
                padding: 0 10px;
                font-size: 1rem;
            }
        }

        /* Search results */
        .search-results-container {
            margin-bottom: 40px;
        }
        
        .search-results-header {
            margin-bottom: 20px;
            color: #212529;
        }
        
        .search-query {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .search-count {
            font-weight: 600;
        }
        
        .search-category {
            display: inline-block;
            padding: 4px 10px;
            background-color: rgba(var(--primary-color-rgb), 0.1);
            color: var(--primary-color);
            font-weight: 600;
            border-radius: 15px;
            margin-bottom: 10px;
            font-size: 0.85rem;
        }
        
        /* Popular FAQs */
        .popular-faqs {
            background-color: rgba(var(--primary-color-rgb), 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .popular-faqs h3 {
            color: var(--primary-color);
            margin-bottom: 20px;
            border-bottom: 2px solid rgba(var(--primary-color-rgb), 0.2);
            padding-bottom: 10px;
        }
        
        /* No results */
        .no-results {
            text-align: center;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .no-results-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <div class="faq-container">
        <div class="container">
            <div class="faq-header">
                <h1>Câu hỏi thường gặp (FAQ)</h1>
                <p class="lead">Tìm câu trả lời nhanh chóng cho các câu hỏi của bạn về dịch vụ khám chữa bệnh tại <?php echo htmlspecialchars($site_name); ?></p>
            </div>
            
            <!-- Search form -->
            <div class="search-container">
                <div class="search-form-wrapper">
                    <form class="search-form" method="GET" action="faq.php">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" class="form-control" name="query" id="searchQuery" placeholder="Nhập câu hỏi cần tìm..." value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                    </form>
                </div>
                <div class="search-suggestions">
                    <p>Từ khóa phổ biến:</p>
                    <span onclick="setSearchQuery('đặt lịch')">đặt lịch</span>
                    <span onclick="setSearchQuery('thanh toán')">thanh toán</span>
                    <span onclick="setSearchQuery('bảo hiểm')">bảo hiểm</span>
                    <span onclick="setSearchQuery('hồ sơ')">hồ sơ</span>
                    <span onclick="setSearchQuery('hủy lịch')">hủy lịch</span>
                </div>
            </div>
            
            <?php if (isset($_GET['query']) && !empty($_GET['query'])): ?>
                <!-- Search results -->
                <div class="search-results-container">
                    <div class="search-results-header">
                        <h2>Kết quả tìm kiếm cho "<span class="search-query"><?php echo htmlspecialchars($_GET['query']); ?></span>"</h2>
                        <p class="search-count">
                            <?php 
                            $result_count = count($search_results);
                            echo "Tìm thấy $result_count kết quả"; 
                            ?>
                        </p>
                    </div>
                    
                    <?php if ($result_count > 0): ?>
                        <div class="accordion" id="searchResultsAccordion">
                            <?php 
                            $i = 0;
                            foreach ($search_results as $faq):
                                $i++;
                                $highlighted_question = preg_replace('/('.preg_quote($search_query, '/').')/i', '<span class="search-highlight">$1</span>', htmlspecialchars($faq['cauhoi']));
                                $highlighted_answer = preg_replace('/('.preg_quote($search_query, '/').')/i', '<span class="search-highlight">$1</span>', htmlspecialchars($faq['cautraloi']));
                            ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="searchHeading<?php echo $i; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#searchCollapse<?php echo $i; ?>" aria-expanded="false" aria-controls="searchCollapse<?php echo $i; ?>">
                                            <?php echo $highlighted_question; ?>
                                        </button>
                                    </h2>
                                    <div id="searchCollapse<?php echo $i; ?>" class="accordion-collapse collapse" aria-labelledby="searchHeading<?php echo $i; ?>" data-bs-parent="#searchResultsAccordion">
                                        <div class="accordion-body">
                                            <span class="search-category"><?php echo htmlspecialchars($faq['ten_danhmuc']); ?></span>
                                            <div><?php echo nl2br($highlighted_answer); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <div class="no-results-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <h4>Không tìm thấy kết quả nào</h4>
                            <p>Vui lòng thử lại với từ khóa khác hoặc liên hệ với chúng tôi để được hỗ trợ.</p>
                            <a href="contact.php" class="btn btn-outline-primary mt-3">Liên hệ hỗ trợ</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Popular FAQs -->
                <?php if (!empty($popular_faqs)): ?>
                    <div class="popular-faqs">
                        <h3><i class="fas fa-star"></i> Câu hỏi phổ biến</h3>
                        <div class="accordion" id="popularFaqsAccordion">
                            <?php 
                            $i = 0;
                            foreach ($popular_faqs as $faq):
                                $i++;
                            ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="popularHeading<?php echo $i; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#popularCollapse<?php echo $i; ?>" aria-expanded="false" aria-controls="popularCollapse<?php echo $i; ?>">
                                            <?php echo htmlspecialchars($faq['cauhoi']); ?>
                                        </button>
                                    </h2>
                                    <div id="popularCollapse<?php echo $i; ?>" class="accordion-collapse collapse" aria-labelledby="popularHeading<?php echo $i; ?>" data-bs-parent="#popularFaqsAccordion">
                                        <div class="accordion-body">
                                            <?php echo nl2br(htmlspecialchars($faq['cautraloi'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- FAQ Categories -->
                <div class="category-tabs-container">
                    <ul class="nav nav-tabs category-tabs" id="faqCategoryTabs" role="tablist">
                        <?php 
                        $first = true;
                        foreach ($categories as $category):
                            $category_id = $category['id'];
                            $category_name = $category['ten_danhmuc'];
                            $icon_class = '';
                            
                            // Assign appropriate icon based on category name
                            switch (strtolower(trim($category_name))) {
                                case 'đặt lịch':
                                    $icon_class = 'fas fa-calendar-alt';
                                    break;
                                case 'thanh toán':
                                    $icon_class = 'fas fa-credit-card';
                                    break;
                                case 'y tế & điều trị':
                                    $icon_class = 'fas fa-stethoscope';
                                    break;
                                case 'tài khoản':
                                    $icon_class = 'fas fa-user';
                                    break;
                                case 'thông tin chung':
                                    $icon_class = 'fas fa-info-circle';
                                    break;
                                default:
                                    $icon_class = 'fas fa-question-circle';
                            }
                        ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $first ? 'active' : ''; ?>" id="tab-<?php echo $category_id; ?>" data-bs-toggle="tab" data-bs-target="#category-<?php echo $category_id; ?>" type="button" role="tab" aria-controls="category-<?php echo $category_id; ?>" aria-selected="<?php echo $first ? 'true' : 'false'; ?>">
                                    <span class="category-icon"><i class="<?php echo $icon_class; ?>"></i></span>
                                    <span><?php echo htmlspecialchars($category_name); ?></span>
                                </button>
                            </li>
                        <?php 
                            $first = false;
                        endforeach;
                        ?>
                    </ul>
                </div>

                <div class="tab-content" id="faqCategoryTabContent">
                    <?php 
                    $first = true;
                    foreach ($categories as $category):
                        $category_id = $category['id'];
                        $category_name = $category['ten_danhmuc'];
                        $faqs = get_dummy_faqs_by_category($category_id);
                    ?>
                        <div class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>" id="category-<?php echo $category_id; ?>" role="tabpanel" aria-labelledby="tab-<?php echo $category_id; ?>">
                            <?php if (!empty($faqs)): ?>
                                <div class="accordion" id="accordion-<?php echo $category_id; ?>">
                                    <?php 
                                    $i = 0;
                                    foreach ($faqs as $faq):
                                        $i++;
                                    ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="heading-<?php echo $category_id; ?>-<?php echo $i; ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $category_id; ?>-<?php echo $i; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $category_id; ?>-<?php echo $i; ?>">
                                                    <?php echo htmlspecialchars($faq['cauhoi']); ?>
                                                </button>
                                            </h2>
                                            <div id="collapse-<?php echo $category_id; ?>-<?php echo $i; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $category_id; ?>-<?php echo $i; ?>" data-bs-parent="#accordion-<?php echo $category_id; ?>">
                                                <div class="accordion-body">
                                                    <?php echo nl2br(htmlspecialchars($faq['cautraloi'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info mt-4">
                                    Không có câu hỏi nào trong danh mục này.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php 
                        $first = false;
                    endforeach;
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="faq-footer">
                <h4>Không tìm thấy câu trả lời bạn cần?</h4>
                <p>Vui lòng liên hệ với chúng tôi nếu bạn không tìm thấy câu trả lời cho câu hỏi của mình.</p>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <h4>Gọi cho chúng tôi</h4>
                            <p class="contact-text">Tổng đài hỗ trợ 24/7</p>
                            <a href="tel:19001234" class="btn btn-primary contact-btn">1900 1234</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h4>Email</h4>
                            <p class="contact-text">Phản hồi trong vòng 24 giờ</p>
                            <a href="mailto:support@locbinh.vn" class="btn btn-primary contact-btn">support@locbinh.vn</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-card">
                            <div class="contact-icon">
                                <i class="fas fa-comment-dots"></i>
                            </div>
                            <h4>Live chat</h4>
                            <p class="contact-text">Chat trực tiếp với nhân viên</p>
                            <a href="contact.php" class="btn btn-primary contact-btn">Bắt đầu chat</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to search results if query exists
        <?php if(isset($_GET['query']) && !empty($_GET['query'])): ?>
        document.querySelector('.search-results-container').scrollIntoView({ behavior: 'smooth', block: 'start' });
        <?php endif; ?>
        
        // Auto expand first search result if results exist
        <?php if(isset($search_results) && !empty($search_results)): ?>
        document.querySelector('#searchResultsAccordion .accordion-button').click();
        <?php endif; ?>
        
        // Make active tab visible when off-screen on mobile
        const activateTab = (tab) => {
            if (tab) {
                setTimeout(() => {
                    tab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                }, 100);
            }
        };
        
        // Initially scroll to active tab
        const activeTab = document.querySelector('.category-tabs .nav-link.active');
        activateTab(activeTab);
        
        // Scroll to active tab when changed
        const tabs = document.querySelectorAll('.category-tabs .nav-link');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => {
                activateTab(e.target);
            });
        });
    });
    
    // Function to set search query from suggestions
    window.setSearchQuery = function(query) {
        document.getElementById('searchQuery').value = query;
        document.querySelector('.search-form').submit();
    }
    
    // Focus search field when clicking on the search icon
    document.querySelector('.search-icon').addEventListener('click', function() {
        document.getElementById('searchQuery').focus();
    });
    </script>
</body>
</html>