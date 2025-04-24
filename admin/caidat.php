<?php
// Kiểm tra quyền truy cập
require_once 'includes/auth_check.php';

// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'save_settings') {
        foreach ($_POST as $key => $value) {
            if ($key !== 'action') {
                // Cập nhật giá trị trong cơ sở dữ liệu
                $stmt = $conn->prepare("UPDATE caidat_website SET ten_value = ? WHERE ten_key = ?");
                $stmt->bind_param("ss", $value, $key);
                $stmt->execute();
            }
        }

        // Xử lý tải lên logo
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../assets/img/";
            
            // Kiểm tra và tạo thư mục nếu không tồn tại
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
            $new_filename = "logo." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Tải lên tệp
            if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target_file)) {
                $logo_path = "assets/img/" . $new_filename;
                $stmt = $conn->prepare("UPDATE caidat_website SET ten_value = ? WHERE ten_key = 'site_logo'");
                $stmt->bind_param("s", $logo_path);
                $stmt->execute();
            }
        }
        
        // Xử lý tải lên favicon
        if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
            $target_dir = "../assets/img/";
            
            // Kiểm tra và tạo thư mục nếu không tồn tại
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION);
            $new_filename = "favicon." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            // Tải lên tệp
            if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $target_file)) {
                $favicon_path = "assets/img/" . $new_filename;
                $stmt = $conn->prepare("UPDATE caidat_website SET ten_value = ? WHERE ten_key = 'site_favicon'");
                $stmt->bind_param("s", $favicon_path);
                $stmt->execute();
            }
        }
        
        // Làm mới cache cài đặt
        clearSettingsCache();
        
        // Thông báo thành công
        $success_message = "Lưu cài đặt thành công!";
    } elseif (isset($_POST['action']) && $_POST['action'] === 'sync_settings') {
        // Đồng bộ cài đặt
        if (syncSettings()) {
            $success_message = "Đồng bộ cài đặt thành công!";
        } else {
            $error_message = "Có lỗi xảy ra khi đồng bộ cài đặt.";
        }
    }
}

/**
 * Xóa cache cài đặt để đảm bảo lấy dữ liệu mới
 */
function clearSettingsCache() {
    // Ghi vào file cache thời gian cập nhật mới nhất
    $cache_file = "../includes/settings_cache.php";
    $cache_content = "<?php\n";
    $cache_content .= "// Thời gian cập nhật cài đặt mới nhất\n";
    $cache_content .= "\$settings_last_updated = ".time().";\n";
    $cache_content .= "?>";
    
    file_put_contents($cache_file, $cache_content);
}

/**
 * Đồng bộ cài đặt trên toàn hệ thống
 */
function syncSettings() {
    global $conn;
    try {
        // 1. Tạo file cache lưu các cài đặt phổ biến
        $settings_query = "SELECT ten_key, ten_value FROM caidat_website";
        $settings_result = $conn->query($settings_query);
        
        if (!$settings_result) {
            return false;
        }
        
        $settings = [];
        while ($row = $settings_result->fetch_assoc()) {
            $settings[$row['ten_key']] = $row['ten_value'];
        }
        
        // 2. Tạo file cài đặt để sử dụng
        $settings_file = "../includes/settings_data.php";
        
        $file_content = "<?php\n";
        $file_content .= "// File được sinh tự động từ phần cài đặt hệ thống\n";
        $file_content .= "// Cập nhật lần cuối: " . date("Y-m-d H:i:s") . "\n\n";
        $file_content .= "\$settings_data = [\n";
        
        foreach ($settings as $key => $value) {
            // Xử lý các giá trị đặc biệt
            if (is_numeric($value)) {
                $file_content .= "    '{$key}' => {$value},\n";
            } else {
                $file_content .= "    '{$key}' => '".addslashes($value)."',\n";
            }
        }
        
        $file_content .= "];\n?>";
        
        file_put_contents($settings_file, $file_content);
        
        // 3. Cập nhật phiên bản cài đặt
        $version_file = "../includes/settings_version.php";
        $version_content = "<?php\n";
        $version_content .= "/**\n";
        $version_content .= " * File quản lý phiên bản cài đặt để đảm bảo đồng bộ trên toàn hệ thống\n";
        $version_content .= " * File này được tự động cập nhật khi cài đặt thay đổi\n";
        $version_content .= " */\n\n";
        $version_content .= "// Phiên bản cài đặt hiện tại\n";
        $version_content .= "\$settings_version = " . time() . ";\n\n";
        $version_content .= "// Thời gian cập nhật cuối cùng\n";
        $version_content .= "\$settings_last_updated = '" . date('Y-m-d H:i:s') . "';\n";
        $version_content .= "?>";
        
        file_put_contents($version_file, $version_content);
        
        // 4. Xóa file cache để đảm bảo lấy dữ liệu mới
        clearSettingsCache();
        
        return true;
    } catch (Exception $e) {
        error_log("Lỗi đồng bộ cài đặt: " . $e->getMessage());
        return false;
    }
}

// Lấy tất cả các cài đặt từ cơ sở dữ liệu
$settings = [];
$result = $conn->query("SELECT * FROM caidat_website ORDER BY nhom, thu_tu");

while ($row = $result->fetch_assoc()) {
    $group = $row['nhom'];
    if (!isset($settings[$group])) {
        $settings[$group] = [];
    }
    $settings[$group][] = $row;
}

// Lấy danh sách các nhóm
$groups = [
    'general' => 'Cài đặt chung',
    'contact' => 'Thông tin liên hệ',
    'social' => 'Mạng xã hội',
    'appearance' => 'Giao diện',
    'feature' => 'Tính năng'
];

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt Website - Phòng khám Lộc Bình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="asset/admin.css">
    <style>
        .settings-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        .tab-pane {
            padding: 20px 0;
        }
        
        .setting-group {
            margin-bottom: 30px;
        }
        
        .setting-item {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .setting-item:last-child {
            border-bottom: none;
        }
        
        .form-label {
            font-weight: 600;
        }
        
        .form-text {
            font-size: 0.85rem;
        }
        
        .nav-pills .nav-link {
            color: #333;
        }
        
        .nav-pills .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            margin-top: 10px;
        }
        
        .color-preview {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 5px;
            margin-left: 10px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-12 main-content  mt-5 ">
            <div class="content-wrapper">

                <div class="content-header d-flex justify-content-between align-items-center ">
                    <h2 class="page-title">Cài đặt Website</h2>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="settings-container">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="save_settings">
                        
                        <!-- Nav tabs -->
                        <ul class="nav nav-pills mb-4" id="settingsTabs" role="tablist">
                            <?php $active = true; foreach($groups as $key => $name): ?>
                                <?php if(isset($settings[$key])): ?>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link <?php echo $active ? 'active' : ''; ?>" 
                                                id="<?php echo $key; ?>-tab" 
                                                data-bs-toggle="pill" 
                                                data-bs-target="#<?php echo $key; ?>" 
                                                type="button" 
                                                role="tab"
                                                aria-controls="<?php echo $key; ?>" 
                                                aria-selected="<?php echo $active ? 'true' : 'false'; ?>">
                                            <?php echo $name; ?>
                                        </button>
                                    </li>
                                    <?php $active = false; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <!-- Tab content -->
                        <div class="tab-content" id="settingsTabContent">
                            <?php $active = true; foreach($groups as $group_key => $group_name): ?>
                                <?php if(isset($settings[$group_key])): ?>
                                    <div class="tab-pane fade <?php echo $active ? 'show active' : ''; ?>" 
                                         id="<?php echo $group_key; ?>" 
                                         role="tabpanel" 
                                         aria-labelledby="<?php echo $group_key; ?>-tab">
                                        
                                        <div class="setting-group">
                                            <?php foreach($settings[$group_key] as $setting): ?>
                                                <div class="setting-item">
                                                    <?php if($setting['loai'] === 'text'): ?>
                                                        <div class="mb-3">
                                                            <label for="<?php echo $setting['ten_key']; ?>" class="form-label">
                                                                <?php echo $setting['mo_ta']; ?>
                                                            </label>
                                                            <input type="text" 
                                                                   class="form-control" 
                                                                   id="<?php echo $setting['ten_key']; ?>" 
                                                                   name="<?php echo $setting['ten_key']; ?>"
                                                                   value="<?php echo htmlspecialchars($setting['ten_value']); ?>">
                                                        </div>
                                                    <?php elseif($setting['loai'] === 'textarea'): ?>
                                                        <div class="mb-3">
                                                            <label for="<?php echo $setting['ten_key']; ?>" class="form-label">
                                                                <?php echo $setting['mo_ta']; ?>
                                                            </label>
                                                            <textarea class="form-control" 
                                                                      id="<?php echo $setting['ten_key']; ?>" 
                                                                      name="<?php echo $setting['ten_key']; ?>" 
                                                                      rows="3"><?php echo htmlspecialchars($setting['ten_value']); ?></textarea>
                                                        </div>
                                                    <?php elseif($setting['loai'] === 'image'): ?>
                                                        <div class="mb-3">
                                                            <label for="<?php echo $setting['ten_key']; ?>" class="form-label">
                                                                <?php echo $setting['mo_ta']; ?>
                                                            </label>
                                                            <input type="file" 
                                                                   class="form-control" 
                                                                   id="<?php echo $setting['ten_key']; ?>" 
                                                                   name="<?php echo $setting['ten_key']; ?>"
                                                                   accept="image/*">
                                                            <div class="form-text">Để trống nếu không muốn thay đổi</div>
                                                            
                                                            <?php if (!empty($setting['ten_value'])): ?>
                                                                <div class="mt-2">
                                                                    <p>Hiện tại:</p>
                                                                    <img src="../<?php echo $setting['ten_value']; ?>" 
                                                                         alt="<?php echo $setting['mo_ta']; ?>" 
                                                                         class="image-preview">
                                                                    <input type="hidden" 
                                                                           name="<?php echo $setting['ten_key']; ?>_current" 
                                                                           value="<?php echo htmlspecialchars($setting['ten_value']); ?>">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php elseif($setting['loai'] === 'boolean'): ?>
                                                        <div class="mb-3 form-check form-switch">
                                                            <input type="checkbox" 
                                                                   class="form-check-input" 
                                                                   id="<?php echo $setting['ten_key']; ?>" 
                                                                   name="<?php echo $setting['ten_key']; ?>"
                                                                   value="1"
                                                                   <?php echo $setting['ten_value'] == '1' ? 'checked' : ''; ?>>
                                                            <label class="form-check-label" for="<?php echo $setting['ten_key']; ?>">
                                                                <?php echo $setting['mo_ta']; ?>
                                                            </label>
                                                        </div>
                                                    <?php elseif($setting['loai'] === 'number'): ?>
                                                        <div class="mb-3">
                                                            <label for="<?php echo $setting['ten_key']; ?>" class="form-label">
                                                                <?php echo $setting['mo_ta']; ?>
                                                            </label>
                                                            <input type="number" 
                                                                   class="form-control" 
                                                                   id="<?php echo $setting['ten_key']; ?>" 
                                                                   name="<?php echo $setting['ten_key']; ?>"
                                                                   value="<?php echo htmlspecialchars($setting['ten_value']); ?>">
                                                        </div>
                                                    <?php elseif($setting['loai'] === 'color'): ?>
                                                        <div class="mb-3">
                                                            <label for="<?php echo $setting['ten_key']; ?>" class="form-label">
                                                                <?php echo $setting['mo_ta']; ?>
                                                            </label>
                                                            <div class="input-group">
                                                                <input type="color" 
                                                                       class="form-control form-control-color" 
                                                                       id="<?php echo $setting['ten_key']; ?>" 
                                                                       name="<?php echo $setting['ten_key']; ?>"
                                                                       value="<?php echo htmlspecialchars($setting['ten_value']); ?>">
                                                                <input type="text" 
                                                                       class="form-control" 
                                                                       value="<?php echo htmlspecialchars($setting['ten_value']); ?>"
                                                                       onchange="document.getElementById('<?php echo $setting['ten_key']; ?>').value = this.value">
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php $active = false; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Lưu cài đặt
                            </button>
                        </div>
                    </form>

                    <form action="" method="POST" class="mt-4">
                        <input type="hidden" name="action" value="sync_settings">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-sync me-2"></i> Đồng bộ cài đặt
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý xem trước hình ảnh khi chọn file
            const imageInputs = document.querySelectorAll('input[type="file"]');
            
            imageInputs.forEach(input => {
                input.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const img = this.parentElement.querySelector('.image-preview');
                        if (img) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                img.src = e.target.result;
                            }
                            reader.readAsDataURL(this.files[0]);
                        } else {
                            const newImg = document.createElement('img');
                            newImg.classList.add('image-preview');
                            
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                newImg.src = e.target.result;
                            }
                            reader.readAsDataURL(this.files[0]);
                            
                            this.parentElement.appendChild(document.createElement('p')).innerText = 'Xem trước:';
                            this.parentElement.appendChild(newImg);
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
