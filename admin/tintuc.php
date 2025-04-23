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
    <!-- Custom CSS -->
    <style>
        .content {
            padding: 20px;
        }
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-10 content">
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
                                <h3>48</h3>
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
                                <h3>42</h3>
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
                                <h3>4</h3>
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
                                <h3>2</h3>
                                <p>Lên lịch đăng</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Tất cả danh mục</option>
                                <option value="health">Sức khỏe</option>
                                <option value="nutrition">Dinh dưỡng</option>
                                <option value="medicine">Y học</option>
                                <option value="lifestyle">Lối sống</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select class="form-select" id="statusFilter">
                                <option value="">Tất cả trạng thái</option>
                                <option value="published">Đã xuất bản</option>
                                <option value="draft">Bản nháp</option>
                                <option value="scheduled">Lên lịch</option>
                            </select>
                        </div>
                        <div class="col-md-5 mb-2">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Tìm kiếm bài viết...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- News List -->
                <div class="row">
                    <!-- News Card 1 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="news-card">
                            <div class="news-image">
                                <img src="../assets/img/hohap.webp" alt="Phòng ngừa bệnh hô hấp">
                            </div>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> 15/03/2025
                                </div>
                                <h5 class="news-title">Phòng ngừa các bệnh hô hấp mùa nắng nóng</h5>
                                <div>
                                    <span class="news-category category-health">Sức khỏe</span>
                                    <span class="news-status badge badge-published text-white">Đã xuất bản</span>
                                </div>
                                <div class="news-actions mt-2">
                                    <div>
                                        <i class="far fa-eye"></i> 1.5K
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- News Card 2 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="news-card">
                            <div class="news-image">
                                <img src="../assets/img/tintuc_timmach.jpg" alt="Chế độ ăn cho người bệnh tim">
                            </div>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> 14/03/2025
                                </div>
                                <h5 class="news-title">Chế độ ăn cho người bệnh tim mạch</h5>
                                <div>
                                    <span class="news-category category-nutrition">Dinh dưỡng</span>
                                    <span class="news-status badge badge-published text-white">Đã xuất bản</span>
                                </div>
                                <div class="news-actions mt-2">
                                    <div>
                                        <i class="far fa-eye"></i> 2.3K
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- News Card 3 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="news-card">
                            <div class="news-image">
                                <img src="../assets/img/rang.jpg" alt="Chăm sóc răng miệng">
                            </div>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> 13/03/2025
                                </div>
                                <h5 class="news-title">Chăm sóc răng miệng đúng cách</h5>
                                <div>
                                    <span class="news-category category-health">Sức khỏe</span>
                                    <span class="news-status badge badge-published text-white">Đã xuất bản</span>
                                </div>
                                <div class="news-actions mt-2">
                                    <div>
                                        <i class="far fa-eye"></i> 1.8K
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- News Card 4 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="news-card">
                            <div class="news-image">
                                <img src="../assets/img/blog-1.png" alt="Vaccine mới">
                            </div>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> 12/03/2025
                                </div>
                                <h5 class="news-title">Vaccine mới trong phòng chống các bệnh truyền nhiễm</h5>
                                <div>
                                    <span class="news-category category-medicine">Y học</span>
                                    <span class="news-status badge badge-scheduled text-white">Lên lịch</span>
                                </div>
                                <div class="news-actions mt-2">
                                    <div>
                                        <i class="fas fa-clock"></i> 25/04/2025
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- News Card 5 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="news-card">
                            <div class="news-image">
                                <img src="../assets/img/blog-2.png" alt="Yoga và sức khỏe">
                            </div>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> 10/03/2025
                                </div>
                                <h5 class="news-title">Yoga và các bài tập thể dục cho người bận rộn</h5>
                                <div>
                                    <span class="news-category category-lifestyle">Lối sống</span>
                                    <span class="news-status badge badge-draft text-white">Bản nháp</span>
                                </div>
                                <div class="news-actions mt-2">
                                    <div>
                                        <i class="fas fa-pencil-alt"></i> Đang soạn
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- News Card 6 -->
                    <div class="col-md-4 col-sm-6">
                        <div class="news-card">
                            <div class="news-image">
                                <img src="../assets/img/blog-3.png" alt="Dinh dưỡng cho trẻ">
                            </div>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i> 08/03/2025
                                </div>
                                <h5 class="news-title">Dinh dưỡng cho trẻ em trong giai đoạn phát triển</h5>
                                <div>
                                    <span class="news-category category-nutrition">Dinh dưỡng</span>
                                    <span class="news-status badge badge-published text-white">Đã xuất bản</span>
                                </div>
                                <div class="news-actions mt-2">
                                    <div>
                                        <i class="far fa-eye"></i> 3.2K
                                    </div>
                                    <div>
                                        <a href="#" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editNewsModal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Trước</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Sau</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Add News Modal -->
    <div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewsModalLabel">Thêm bài viết mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="newsTitle" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="newsTitle" placeholder="Nhập tiêu đề bài viết">
                            </div>
                            <div class="col-md-4">
                                <label for="newsCategory" class="form-label">Danh mục</label>
                                <select class="form-select" id="newsCategory">
                                    <option value="">Chọn danh mục</option>
                                    <option value="health">Sức khỏe</option>
                                    <option value="nutrition">Dinh dưỡng</option>
                                    <option value="medicine">Y học</option>
                                    <option value="lifestyle">Lối sống</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="newsContent" class="form-label">Nội dung</label>
                            <textarea id="newsContent" class="form-control summernote" rows="10"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="newsImage" class="form-label">Hình ảnh bài viết</label>
                                <input type="file" class="form-control" id="newsImage">
                                <div class="form-text">Kích thước đề xuất: 800x400px</div>
                            </div>
                            <div class="col-md-6">
                                <label for="newsStatus" class="form-label">Trạng thái</label>
                                <select class="form-select" id="newsStatus">
                                    <option value="published">Xuất bản ngay</option>
                                    <option value="draft">Lưu bản nháp</option>
                                    <option value="scheduled">Lên lịch đăng</option>
                                </select>
                                <div id="scheduledTimeContainer" class="mt-2" style="display: none;">
                                    <label for="scheduledTime" class="form-label">Thời gian đăng bài</label>
                                    <input type="datetime-local" class="form-control" id="scheduledTime">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="metaTitle" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="metaTitle">
                            </div>
                            <div class="col-md-6">
                                <label for="metaDescription" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="metaDescription" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="tags" placeholder="Nhập các thẻ cách nhau bởi dấu phẩy">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Lưu bài viết</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit News Modal -->
    <div class="modal fade" id="editNewsModal" tabindex="-1" aria-labelledby="editNewsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNewsModalLabel">Chỉnh sửa bài viết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="editNewsTitle" class="form-label">Tiêu đề</label>
                                <input type="text" class="form-control" id="editNewsTitle" value="Phòng ngừa các bệnh hô hấp mùa nắng nóng">
                            </div>
                            <div class="col-md-4">
                                <label for="editNewsCategory" class="form-label">Danh mục</label>
                                <select class="form-select" id="editNewsCategory">
                                    <option value="">Chọn danh mục</option>
                                    <option value="health" selected>Sức khỏe</option>
                                    <option value="nutrition">Dinh dưỡng</option>
                                    <option value="medicine">Y học</option>
                                    <option value="lifestyle">Lối sống</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editNewsContent" class="form-label">Nội dung</label>
                            <textarea id="editNewsContent" class="form-control summernote" rows="10">
                                <h2>Phòng ngừa các bệnh hô hấp mùa nắng nóng</h2>
                                <p>Mùa nắng nóng không chỉ là thời điểm gây khó chịu vì nhiệt độ cao mà còn tiềm ẩn nhiều nguy cơ mắc các bệnh về đường hô hấp. Các bệnh như viêm họng, viêm phế quản, hen suyễn có thể trở nên trầm trọng hơn trong thời tiết nắng nóng...</p>
                                <h3>Nguyên nhân gây bệnh hô hấp mùa nắng nóng</h3>
                                <p>Có nhiều yếu tố gây bệnh đường hô hấp trong mùa nắng nóng:</p>
                                <ul>
                                    <li>Ô nhiễm không khí tăng cao</li>
                                    <li>Sự phát triển của các loại vi khuẩn, virus</li>
                                    <li>Thay đổi nhiệt độ đột ngột khi ra vào các phòng điều hòa</li>
                                    <li>Cơ thể mất nước, giảm sức đề kháng</li>
                                </ul>
                            </textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editNewsImage" class="form-label">Hình ảnh bài viết</label>
                                <div class="d-flex align-items-center">
                                    <img src="../assets/img/hohap.webp" alt="Hình hiện tại" width="100" class="me-2">
                                    <input type="file" class="form-control" id="editNewsImage">
                                </div>
                                <div class="form-text">Kích thước đề xuất: 800x400px</div>
                            </div>
                            <div class="col-md-6">
                                <label for="editNewsStatus" class="form-label">Trạng thái</label>
                                <select class="form-select" id="editNewsStatus">
                                    <option value="published" selected>Đã xuất bản</option>
                                    <option value="draft">Bản nháp</option>
                                    <option value="scheduled">Lên lịch đăng</option>
                                </select>
                                <div id="editScheduledTimeContainer" class="mt-2" style="display: none;">
                                    <label for="editScheduledTime" class="form-label">Thời gian đăng bài</label>
                                    <input type="datetime-local" class="form-control" id="editScheduledTime">
                                </div>
                                <div class="form-text">Xuất bản: 15/03/2025 | Chỉnh sửa lần cuối: 18/03/2025</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="editMetaTitle" class="form-label">Meta Title</label>
                                <input type="text" class="form-control" id="editMetaTitle" value="Phòng ngừa các bệnh hô hấp mùa nắng nóng - Bí quyết bảo vệ sức khỏe">
                            </div>
                            <div class="col-md-6">
                                <label for="editMetaDescription" class="form-label">Meta Description</label>
                                <textarea class="form-control" id="editMetaDescription" rows="3">Tìm hiểu các biện pháp phòng ngừa bệnh hô hấp hiệu quả trong thời tiết nắng nóng. Bảo vệ sức khỏe đường hô hấp với những lời khuyên từ chuyên gia.</textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editTags" class="form-label">Tags</label>
                            <input type="text" class="form-control" id="editTags" value="hô hấp, nắng nóng, phòng bệnh, viêm phổi, hen suyễn">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary">Cập nhật bài viết</button>
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
    <script>
        $(document).ready(function() {
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
            $('#newsStatus').change(function() {
                if ($(this).val() === 'scheduled') {
                    $('#scheduledTimeContainer').show();
                } else {
                    $('#scheduledTimeContainer').hide();
                }
            });

            $('#editNewsStatus').change(function() {
                if ($(this).val() === 'scheduled') {
                    $('#editScheduledTimeContainer').show();
                } else {
                    $('#editScheduledTimeContainer').hide();
                }
            });
        });
    </script>
</body>
</html>