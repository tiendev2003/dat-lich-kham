<?php
// Lấy thông tin từ cài đặt nếu chưa có từ header
if (!function_exists('get_setting')) {
    require_once 'functions.php';
}

// Lấy các thông số cài đặt website
$site_name = get_setting('site_name', 'Hệ thống đặt lịch khám bệnh');
$site_address = get_setting('site_address', '67 Minh Khai, Lộc Bình, Lạng Sơn');
$site_phone = get_setting('site_phone', '0253 836 836');
$site_email = get_setting('site_email', 'phongkhamlocbinh@gmail.com');
$site_facebook = get_setting('site_facebook', '#');
$site_twitter = get_setting('site_twitter', '#');
$site_instagram = get_setting('site_instagram', '#');
$site_youtube = get_setting('site_youtube', '#');
$site_description = get_setting('site_description', 'Hệ thống đặt lịch khám bệnh trực tuyến - Giải pháp chăm sóc sức khỏe thông minh và tiện lợi cho mọi người.');
?>
<footer class="footer bg-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Về chúng tôi</h5>
                <p><?php echo $site_description; ?></p>
            </div>
            <div class="col-md-4">
                <h5>Liên kết nhanh</h5>
                <ul class="list-unstyled">
                    <li><a style="text-decoration: none;" href="about.php">Giới thiệu</a></li>
                    <li><a style="text-decoration: none;" href="bacsi.php">Đội ngũ bác sĩ</a></li>
                    <li><a style="text-decoration: none;" href="chuyenkhoa.php">Chuyên khoa</a></li>
                    <li><a style="text-decoration: none;" href="contact.php">Liên hệ</a></li>
                    <li><a style="text-decoration: none;" href="privacy.php">Chính sách bảo mật</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Thông tin liên hệ</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt"></i> <?php echo $site_address; ?></li>
                    <li><i class="fas fa-phone"></i> Hotline: <?php echo $site_phone; ?></li>
                    <li><i class="fas fa-envelope"></i> Email: <?php echo $site_email; ?></li>
                </ul>
                <div class="social-links mt-3">
                    <?php if ($site_facebook): ?>
                    <a href="<?php echo $site_facebook; ?>" class="me-3" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    
                    <?php if ($site_twitter): ?>
                    <a href="<?php echo $site_twitter; ?>" class="me-3" target="_blank"><i class="fab fa-twitter"></i></a>
                    <?php endif; ?>
                    
                    <?php if ($site_instagram): ?>
                    <a href="<?php echo $site_instagram; ?>" class="me-3" target="_blank"><i class="fab fa-instagram"></i></a>
                    <?php endif; ?>
                    
                    <?php if ($site_youtube): ?>
                    <a href="<?php echo $site_youtube; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo $site_name; ?>. Đã đăng ký bản quyền.</p>
        </div>
    </div>
</footer>