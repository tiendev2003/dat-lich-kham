<?php
// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
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
    
    // Thông báo thành công
    $success_message = "Lưu cài đặt thành công!";
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
    <title>Cài đặt Website - Quản trị hệ thống</title>
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
            <div class="col-md-12 main-content ms-sm-auto p-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Cài đặt Website</h1>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
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
