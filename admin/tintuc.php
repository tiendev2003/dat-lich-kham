<?php
// Kết nối đến cơ sở dữ liệu
require_once 'includes/db_connect.php';

// Thiết lập phân trang
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$items_per_page = 6; // Số tin tức hiển thị trên mỗi trang
$offset = ($current_page - 1) * $items_per_page;

// Lấy các tham số lọc
$category = isset($_GET['category']) ? $_GET['category'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$db_already_connected = true;

// Include file CRUD
require_once 'crud/tintuc_crud.php';

// Lấy danh sách tin tức với bộ lọc
$filter = [
    'category' => $category,
    'status' => $status,
    'search' => $search
];
$all_news = getAllNews($filter);
$total_news = count($all_news);

// Tính tổng số trang
$total_pages = ceil($total_news / $items_per_page);

// Lấy tin tức cho trang hiện tại
$news_list = array_slice($all_news, $offset, $items_per_page);

// Tính toán số lượng theo trạng thái
$published_count = 0;
$draft_count = 0;
$scheduled_count = 0;

foreach ($all_news as $news_item) {
    if ($news_item['trang_thai'] == 'published') {
        $published_count++;
    } elseif ($news_item['trang_thai'] == 'draft') {
        $draft_count++;
    } elseif ($news_item['trang_thai'] == 'scheduled') {
        $scheduled_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Tin tức - Quản trị hệ thống</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/admin.css">

    <!-- Custom CSS -->
    <style>
        .stats-card {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stats-icon {
            font-size: 24px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .stats-info h3 {
            margin: 0;
            font-size: 20px;
        }

        .stats-info p {
            margin: 0;
            font-size: 14px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .news-card {
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .news-card:hover {
            transform: translateY(-5px);
        }

        .news-image {
            height: 160px;
            overflow: hidden;
        }

        .news-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .news-content {
            padding: 15px;
            background-color: #fff;
        }

        .news-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .news-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .news-category {
            display: inline-block;
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 12px;
            margin-right: 5px;
        }

        .news-actions {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .news-status {
            font-size: 12px;
            font-weight: 600;
        }

        .badge-published {
            background-color: #28a745;
        }

        .badge-draft {
            background-color: #6c757d;
        }

        .badge-scheduled {
            background-color: #17a2b8;
        }

        .category-health {
            background-color: #dcf5dc;
            color: #28a745;
        }

        .category-nutrition {
            background-color: #fff3cd;
            color: #ffc107;
        }

        .category-medicine {
            background-color: #cce5ff;
            color: #007bff;
        }

        .category-lifestyle {
            background-color: #f5dcdc;
            color: #dc3545;
        }

        .filter-section {
            margin-bottom: 20px;
        }
        
        /* Responsive styles */
        @media (max-width: 992px) {
            .stats-card {
                padding: 12px;
            }
            
            .stats-icon {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
            
            .stats-info h3 {
                font-size: 18px;
            }
            
            .stats-info p {
                font-size: 12px;
            }
        }
        
        @media (max-width: 768px) {
            .content-header {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .content-header h2 {
                margin-bottom: 10px;
            }
            
            .row.mb-4 {
                margin-right: -10px;
                margin-left: -10px;
            }
            
            .col-md-3 {
                padding-right: 10px;
                padding-left: 10px;
            }
            
            .filter-section .row {
                gap: 8px;
            }
            
            .filter-section .col-md-4,
            .filter-section .col-md-3,
            .filter-section .col-md-5 {
                padding: 0 5px;
            }
            
            .news-card {
                margin-bottom: 15px;
            }
            
            .news-image {
                height: 140px;
            }
        }
        
        @media (max-width: 576px) {
            .stats-card {
                flex-direction: column;
                text-align: center;
                padding: 15px 10px;
            }
            
            .stats-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .news-actions {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
            
            .news-actions div:last-child {
                width: 100%;
                display: flex;
                justify-content: space-between;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .modal-content {
                border-radius: 0;
            }
            
            .summernote-wrapper .note-toolbar {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-12 main-content">
                <div class="content-header">
                    <h2>Quản lý Tin tức</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                        <i class="fas fa-plus"></i> Thêm bài viết mới
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card bg-primary text-white">
                            <div class="stats-icon">
                                <i class="fas fa-newspaper"></i>
                            </div>
                            <div class="stats-info">
                                <h3><?php echo $total_news; ?></h3>
                                <p>Tổng số bài viết</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-success text-white">
                            <div class="stats-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-info">
                                <h3><?php echo $published_count; ?></h3>
                                <p>Đã xuất bản</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-warning text-white">
                            <div class="stats-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stats-info">
                                <h3><?php echo $draft_count; ?></h3>
                                <p>Bản nháp</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card bg-info text-white">
                            <div class="stats-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="stats-info">
                                <h3><?php echo $scheduled_count; ?></h3>
                                <p>Lên lịch đăng</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row">
                        <form action="" method="GET" class="row">
                            <div class="col-md-4 mb-2">
                                <select class="form-select" id="categoryFilter" name="category">
                                    <option value="">Tất cả danh mục</option>
                                    <option value="health" <?php echo $category == 'health' ? 'selected' : ''; ?>>Sức khỏe
                                    </option>
                                    <option value="nutrition" <?php echo $category == 'nutrition' ? 'selected' : ''; ?>>
                                        Dinh dưỡng</option>
                                    <option value="medicine" <?php echo $category == 'medicine' ? 'selected' : ''; ?>>Y
                                        học</option>
                                    <option value="lifestyle" <?php echo $category == 'lifestyle' ? 'selected' : ''; ?>>
                                        Lối sống</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select class="form-select" id="statusFilter" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="published" <?php echo $status == 'published' ? 'selected' : ''; ?>>Đã
                                        xuất bản</option>
                                    <option value="draft" <?php echo $status == 'draft' ? 'selected' : ''; ?>>Bản nháp
                                    </option>
                                    <option value="scheduled" <?php echo $status == 'scheduled' ? 'selected' : ''; ?>>Lên
                                        lịch</option>
                                </select>
                            </div>
                            <div class="col-md-5 mb-2">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="search"
                                        placeholder="Tìm kiếm bài viết..."
                                        value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- News List -->
                <div class="row">
                    <?php if (count($news_list) > 0): ?>
                        <?php foreach ($news_list as $news): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="news-card">
                                    <div class="news-image">
                                        <?php if (!empty($news['hinh_anh'])): ?>
                                            <img src="../<?php echo htmlspecialchars($news['hinh_anh']); ?>"
                                                alt="<?php echo htmlspecialchars($news['tieu_de']); ?>">
                                        <?php else: ?>
                                            <img src="../assets/img/blog-1.png" alt="Default Image">
                                        <?php endif; ?>
                                    </div>
                                    <div class="news-content">
                                        <div class="news-date">
                                            <i class="far fa-calendar-alt"></i>
                                            <?php
                                            if ($news['trang_thai'] == 'published' && !empty($news['ngay_dang'])) {
                                                echo date('d/m/Y', strtotime($news['ngay_dang']));
                                            } elseif ($news['trang_thai'] == 'scheduled' && !empty($news['ngay_dang'])) {
                                                echo 'Lên lịch: ' . date('d/m/Y', strtotime($news['ngay_dang']));
                                            } else {
                                                echo date('d/m/Y', strtotime($news['ngay_tao']));
                                            }
                                            ?>
                                        </div>
                                        <h5 class="news-title"><?php echo htmlspecialchars($news['tieu_de']); ?></h5>
                                        <div>
                                            <span
                                                class="news-category category-<?php echo htmlspecialchars($news['danh_muc']); ?>">
                                                <?php
                                                switch ($news['danh_muc']) {
                                                    case 'health':
                                                        echo 'Sức khỏe';
                                                        break;
                                                    case 'nutrition':
                                                        echo 'Dinh dưỡng';
                                                        break;
                                                    case 'medicine':
                                                        echo 'Y học';
                                                        break;
                                                    case 'lifestyle':
                                                        echo 'Lối sống';
                                                        break;
                                                    default:
                                                        echo $news['danh_muc'];
                                                }
                                                ?>
                                            </span>
                                            <span class="news-status badge badge-<?php echo $news['trang_thai']; ?> text-white">
                                                <?php
                                                switch ($news['trang_thai']) {
                                                    case 'published':
                                                        echo 'Đã xuất bản';
                                                        break;
                                                    case 'draft':
                                                        echo 'Bản nháp';
                                                        break;
                                                    case 'scheduled':
                                                        echo 'Lên lịch';
                                                        break;
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <div class="news-actions mt-2">
                                            <div>
                                                <?php if ($news['trang_thai'] == 'published'): ?>
                                                    <i class="far fa-eye"></i> <?php echo rand(100, 9999); ?>
                                                <?php elseif ($news['trang_thai'] == 'scheduled'): ?>
                                                    <i class="fas fa-clock"></i>
                                                    <?php echo date('d/m/Y', strtotime($news['ngay_dang'])); ?>
                                                <?php else: ?>
                                                    <i class="fas fa-pencil-alt"></i> Đang soạn
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <a href="#" class="btn btn-sm btn-outline-primary me-1 edit-news-btn"
                                                    data-bs-toggle="modal" data-bs-target="#editNewsModal"
                                                    data-id="<?php echo $news['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-danger delete-news-btn"
                                                    data-id="<?php echo $news['id']; ?>"
                                                    data-title="<?php echo htmlspecialchars($news['tieu_de']); ?>">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <?php if (!empty($search) || !empty($category) || !empty($status)): ?>
                                    Không tìm thấy tin tức nào phù hợp với bộ lọc. <a href="tintuc.php">Xem tất cả tin tức</a>
                                <?php else: ?>
                                    Chưa có tin tức nào. Hãy thêm tin tức mới!
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page=<?php echo $current_page - 1; ?><?php echo !empty($category) ? '&category=' . $category : ''; ?><?php echo !empty($status) ? '&status=' . $status : ''; ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>"
                                        aria-label="Previous">
                                        <span aria-hidden="true">Trước</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Trước</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="?page=<?php echo $i; ?><?php echo !empty($category) ? '&category=' . $category : ''; ?><?php echo !empty($status) ? '&status=' . $status : ''; ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="?page=<?php echo $current_page + 1; ?><?php echo !empty($category) ? '&category=' . $category : ''; ?><?php echo !empty($status) ? '&status=' . $status : ''; ?><?php echo !empty($search) ? '&search=' . htmlspecialchars($search) : ''; ?>"
                                        aria-label="Next">
                                        <span aria-hidden="true">Sau</span>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Sau</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <?php $modal_title = "Thêm bài viết mới"; ?>
                    <?php include 'modals/mobile_header.php'; ?>
                    <h5 class="modal-title d-none d-md-block" id="addNewsModalLabel">Thêm bài viết mới</h5>
                    <button type="button" class="btn-close d-none d-md-block" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addNewsForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="tieuDe" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="tieuDe" name="tieuDe"
                                    placeholder="Nhập tiêu đề bài viết" required>
                            </div>
                            <div class="col-md-4">
                                <label for="danhMuc" class="form-label">Danh mục</label>
                                <select class="form-select" id="danhMuc" name="danhMuc" required>
                                    <option value="">Chọn danh mục</option>
                                    <option value="health">Sức khỏe</option>
                                    <option value="nutrition">Dinh dưỡng</option>
                                    <option value="medicine">Y học</option>
                                    <option value="lifestyle">Lối sống</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="noiDung" class="form-label">Nội dung</label>
                            <textarea id="noiDung" name="noiDung" class="form-control summernote" rows="10"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="hinhAnh" class="form-label">Hình ảnh bài viết</label>
                                <input type="file" class="form-control" id="hinhAnh" name="hinhAnh" accept="image/*">
                                <div class="form-text">Kích thước đề xuất: 800x400px</div>
                            </div>
                            <div class="col-md-6">
                                <label for="trangThai" class="form-label">Trạng thái</label>
                                <select class="form-select" id="trangThai" name="trangThai">
                                    <option value="published">Xuất bản ngay</option>
                                    <option value="draft">Lưu bản nháp</option>
                                    <option value="scheduled">Lên lịch đăng</option>
                                </select>
                                <div id="scheduledTimeContainer" class="mt-2" style="display: none;">
                                    <label for="scheduledTime" class="form-label">Thời gian đăng bài</label>
                                    <input type="datetime-local" class="form-control" id="scheduledTime"
                                        name="scheduledTime">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="metaTitle" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="metaTitle" name="metaTitle">
                            </div>
                            <div class="col-md-6">
                                <label for="metaDescription" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="metaDescription" name="metaDescription"
                                    rows="3"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="tags" name="tags"
                                placeholder="Nhập các thẻ cách nhau bởi dấu phẩy">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitAddNews">Lưu bài viết</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit News Modal -->
    <div class="modal fade" id="editNewsModal" tabindex="-1" aria-labelledby="editNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <?php $modal_title = "Chỉnh sửa bài viết"; ?>
                    <?php include 'modals/mobile_header.php'; ?>
                    <h5 class="modal-title d-none d-md-block" id="editNewsModalLabel">Chỉnh sửa bài viết</h5>
                    <button type="button" class="btn-close d-none d-md-block" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editNewsForm" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="edit_tieuDe" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="edit_tieuDe" name="tieuDe" required>
                            </div>
                            <div class="col-md-4">
                                <label for="edit_danhMuc" class="form-label">Danh mục</label>
                                <select class="form-select" id="edit_danhMuc" name="danhMuc" required>
                                    <option value="">Chọn danh mục</option>
                                    <option value="health">Sức khỏe</option>
                                    <option value="nutrition">Dinh dưỡng</option>
                                    <option value="medicine">Y học</option>
                                    <option value="lifestyle">Lối sống</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_noiDung" class="form-label">Nội dung</label>
                            <textarea id="edit_noiDung" name="noiDung" class="form-control summernote"
                                rows="10"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_hinhAnh" class="form-label">Hình ảnh bài viết</label>
                                <div class="d-flex align-items-center mb-2">
                                    <img id="current_image" src="" alt="Hình hiện tại" width="100" class="me-2 d-none">
                                </div>
                                <input type="file" class="form-control" id="edit_hinhAnh" name="hinhAnh"
                                    accept="image/*">
                                <div class="form-text">Kích thước đề xuất: 800x400px</div>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_trangThai" class="form-label">Trạng thái</label>
                                <select class="form-select" id="edit_trangThai" name="trangThai">
                                    <option value="published">Đã xuất bản</option>
                                    <option value="draft">Bản nháp</option>
                                    <option value="scheduled">Lên lịch đăng</option>
                                </select>
                                <div id="edit_scheduledTimeContainer" class="mt-2" style="display: none;">
                                    <label for="edit_scheduledTime" class="form-label">Thời gian đăng bài</label>
                                    <input type="datetime-local" class="form-control" id="edit_scheduledTime"
                                        name="scheduledTime">
                                </div>
                                <div class="form-text mt-2" id="news_dates"></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit_metaTitle" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="edit_metaTitle" name="metaTitle">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_metaDescription" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="edit_metaDescription" name="metaDescription"
                                    rows="3"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="edit_tags" name="tags"
                                placeholder="Nhập các thẻ cách nhau bởi dấu phẩy">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="submitEditNews">Cập nhật bài viết</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="asset/admin.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Summernote editor
            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Show scheduled time field when scheduled status is selected
            $('#trangThai').change(function () {
                if ($(this).val() === 'scheduled') {
                    $('#scheduledTimeContainer').show();
                } else {
                    $('#scheduledTimeContainer').hide();
                }
            });

            $('#edit_trangThai').change(function () {
                if ($(this).val() === 'scheduled') {
                    $('#edit_scheduledTimeContainer').show();
                } else {
                    $('#edit_scheduledTimeContainer').hide();
                }
            });

            // Submit add news form
            $('#submitAddNews').click(function () {
                var formData = new FormData($('#addNewsForm')[0]);

                $.ajax({
                    type: 'POST',
                    url: 'crud/tintuc_crud.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Đã xảy ra lỗi khi gửi yêu cầu.');
                    }
                });
            });

            // Load news data for editing
            $('.edit-news-btn').click(function () {
                var newsId = $(this).data('id');

                $.ajax({
                    type: 'GET',
                    url: 'crud/tintuc_crud.php',
                    data: {
                        action: 'get_news',
                        id: newsId
                    },
                    dataType: 'json',
                    success: function (news) {
                        if (news) {
                            $('#edit_id').val(news.id);
                            $('#edit_tieuDe').val(news.tieu_de);
                            $('#edit_danhMuc').val(news.danh_muc);
                            $('#edit_noiDung').summernote('code', news.noi_dung);
                            $('#edit_metaTitle').val(news.meta_title);
                            $('#edit_metaDescription').val(news.meta_description);
                            $('#edit_tags').val(news.tags);
                            $('#edit_trangThai').val(news.trang_thai);

                            // Show scheduled time if needed
                            if (news.trang_thai === 'scheduled') {
                                $('#edit_scheduledTimeContainer').show();
                                if (news.ngay_dang) {
                                    // Format datetime for input element
                                    var scheduledDate = new Date(news.ngay_dang);
                                    var formattedDate = scheduledDate.toISOString().slice(0, 16);
                                    $('#edit_scheduledTime').val(formattedDate);
                                }
                            } else {
                                $('#edit_scheduledTimeContainer').hide();
                            }

                            // Display current image if exists
                            if (news.hinh_anh) {
                                $('#current_image').attr('src', '../' + news.hinh_anh).removeClass('d-none');
                            } else {
                                $('#current_image').addClass('d-none');
                            }

                            // Show creation and update dates
                            var datesInfo = '';
                            if (news.ngay_tao) {
                                datesInfo += 'Ngày tạo: ' + new Date(news.ngay_tao).toLocaleDateString('vi-VN');
                            }
                            if (news.ngay_capnhat) {
                                datesInfo += ' | Cập nhật lần cuối: ' + new Date(news.ngay_capnhat).toLocaleDateString('vi-VN');
                            }
                            $('#news_dates').text(datesInfo);
                        } else {
                            alert('Không thể tải thông tin bài viết.');
                        }
                    },
                    error: function () {
                        alert('Đã xảy ra lỗi khi tải thông tin bài viết.');
                    }
                });
            });

            // Submit edit news form
            $('#submitEditNews').click(function () {
                var formData = new FormData($('#editNewsForm')[0]);

                $.ajax({
                    type: 'POST',
                    url: 'crud/tintuc_crud.php',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('Đã xảy ra lỗi khi gửi yêu cầu.');
                    }
                });
            });

            // Delete news
            $('.delete-news-btn').click(function () {
                var newsId = $(this).data('id');
                var newsTitle = $(this).data('title');

                if (confirm('Bạn có chắc chắn muốn xóa bài viết "' + newsTitle + '"?')) {
                    $.ajax({
                        type: 'POST',
                        url: 'crud/tintuc_crud.php',
                        data: {
                            action: 'delete',
                            id: newsId
                        },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                alert(response.message);
                                location.reload();
                            } else {
                                alert(response.message);
                            }
                        },
                        error: function () {
                            alert('Đã xảy ra lỗi khi gửi yêu cầu.');
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>