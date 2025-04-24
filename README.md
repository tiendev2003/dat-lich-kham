# HỆ THỐNG ĐẶT LỊCH KHÁM BỆNH TRỰC TUYẾN

## 1. GIỚI THIỆU

### 1.1 Mục đích
Hệ thống đặt lịch khám bệnh trực tuyến được phát triển nhằm giúp bệnh nhân dễ dàng đặt lịch khám bệnh tại cơ sở y tế, giúp quản lý lịch hẹn hiệu quả cho đội ngũ y tế và cải thiện trải nghiệm khám chữa bệnh cho người dùng.

### 1.2 Phạm vi
Hệ thống cung cấp các chức năng:
- Đăng ký, đăng nhập tài khoản người dùng
- Xem thông tin về chuyên khoa và bác sĩ
- Đặt lịch khám bệnh trực tuyến
- Quản lý lịch hẹn cho bác sĩ
- Quản trị hệ thống toàn diện cho quản trị viên

### 1.3 Đối tượng sử dụng
- **Bệnh nhân/Người dùng cuối**: Người có nhu cầu đặt lịch khám bệnh
- **Bác sĩ**: Người quản lý và xác nhận lịch hẹn khám
- **Quản trị viên**: Người quản lý toàn bộ hệ thống

## 2. MÔ TẢ TỔNG QUAN

### 2.1 Mô hình hệ thống
Hệ thống được phát triển theo mô hình web application với các thành phần:
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5
- **Backend**: PHP
- **Cơ sở dữ liệu**: MySQL

### 2.2 Các chức năng chính
1. **Xem thông tin y tế**
   - Xem danh sách chuyên khoa
   - Xem danh sách bác sĩ theo chuyên khoa
   - Xem thông tin chi tiết bác sĩ
   - Xem tin tức và dịch vụ y tế

2. **Đặt lịch khám**
   - Chọn chuyên khoa và bác sĩ
   - Chọn ngày và giờ khám
   - Điền thông tin cá nhân
   - Mô tả triệu chứng
   - Xác nhận và theo dõi lịch hẹn



## 3. YÊU CẦU CHỨC NĂNG CHI TIẾT

### 3.1 Phân hệ người dùng (Bệnh nhân)

#### 3.1.1 Đăng ký và đăng nhập
- Đăng ký tài khoản với họ tên, email, mật khẩu
- Đăng nhập bằng email và mật khẩu
- Khôi phục mật khẩu qua email

#### 3.1.2 Xem thông tin
- Xem danh sách chuyên khoa
- Xem thông tin chi tiết về chuyên khoa
- Xem danh sách bác sĩ của từng chuyên khoa
- Xem thông tin chi tiết về bác sĩ (chuyên môn, kinh nghiệm, lịch làm việc)
- Xem tin tức và bài viết về sức khỏe
- Xem thông tin về các dịch vụ khám chữa bệnh

#### 3.1.3 Đặt lịch khám
- Chọn chuyên khoa
- Chọn bác sĩ
- Chọn ngày và giờ khám
- Điền thông tin cá nhân (họ tên, giới tính, năm sinh, số điện thoại, địa chỉ)
- Mô tả triệu chứng
- Xem chi phí dịch vụ
- Xác nhận đặt lịch
- Nhận thông báo xác nhận qua email

#### 3.1.4 Quản lý lịch hẹn
- Xem danh sách lịch hẹn đã đặt
- Xem chi tiết lịch hẹn
- Hủy hoặc thay đổi lịch hẹn

### 3.2 Phân hệ bác sĩ

#### 3.2.1 Quản lý lịch hẹn
- Xem danh sách lịch hẹn theo ngày
- Lọc lịch hẹn theo trạng thái (chờ xác nhận, đã xác nhận, đã hủy)
- Xem chi tiết lịch hẹn và thông tin bệnh nhân
- Xác nhận hoặc hủy lịch hẹn
- Thêm ghi chú cho lịch hẹn

#### 3.2.2 Quản lý thông tin cá nhân
- Cập nhật thông tin cá nhân
- Cập nhật lịch làm việc

### 3.3 Phân hệ quản trị viên

#### 3.3.1 Tổng quan hệ thống
- Xem thống kê tổng quan (số lượng lịch hẹn, số bác sĩ, số bệnh nhân)
#### 3.3.2 Quản lý bác sĩ
- Thêm, sửa, xóa thông tin bác sĩ
- Phân công bác sĩ theo chuyên khoa
- Quản lý tài khoản bác sĩ

#### 3.3.3 Quản lý bệnh nhân
- Xem danh sách bệnh nhân
- Xem chi tiết thông tin bệnh nhân
- Quản lý tài khoản bệnh nhân

#### 3.3.4 Quản lý chuyên khoa
- Thêm, sửa, xóa chuyên khoa
- Quản lý thông tin chi tiết về chuyên khoa

#### 3.3.5 Quản lý dịch vụ
- Thêm, sửa, xóa dịch vụ khám
- Cập nhật giá dịch vụ

## 4. YÊU CẦU PHI CHỨC NĂNG

### 4.1 Giao diện người dùng
- Giao diện thân thiện, dễ sử dụng
- Tương thích với các thiết bị di động (responsive design)
- Thời gian phản hồi nhanh

### 4.2 Bảo mật
- Mã hóa thông tin đăng nhập
- Bảo mật thông tin cá nhân của bệnh nhân
- Phân quyền truy cập cho từng loại người dùng

### 4.3 Hiệu năng
- Hệ thống có khả năng xử lý đồng thời nhiều người dùng
- Thời gian phản hồi các thao tác dưới 3 giây
- Sao lưu dữ liệu định kỳ

## 5. KIẾN TRÚC HỆ THỐNG

### 5.1 Cấu trúc thư mục
```
/
├── about.php              # Trang giới thiệu
├── bacsi.php              # Trang danh sách bác sĩ
├── chitiet_bacsi.php      # Trang chi tiết bác sĩ
├── chitiet_dichvu.php     # Trang chi tiết dịch vụ
├── chitiet_tintuc.php     # Trang chi tiết tin tức
├── chuyenkhoa_chitiet.php # Trang chi tiết chuyên khoa
├── chuyenkhoa.php         # Trang danh sách chuyên khoa
├── dangky.php             # Trang đăng ký
├── dangnhap.php           # Trang đăng nhập
├── datlich.php            # Trang đặt lịch khám
├── dichvu.php             # Trang dịch vụ
├── index.php              # Trang chủ
├── tintuc.php             # Trang tin tức
├── admin/                 # Phân hệ quản trị viên
│   ├── bacsi.php          # Quản lý bác sĩ
│   ├── benhnhan.php       # Quản lý bệnh nhân
│   ├── chuyenkhoa.php     # Quản lý chuyên khoa
│   ├── dichvu.php         # Quản lý dịch vụ
│   ├── taikhoan.php       # Quản lý tài khoản
│   ├── tongquan.php       # Trang tổng quan
│   ├── asset/             # CSS cho admin
│   └── includes/          # Các thành phần dùng chung
├── doctor/                # Phân hệ bác sĩ
│   ├── lichkham.php       # Quản lý lịch khám
│   ├── asset/             # CSS cho bác sĩ
│   └── includes/          # Các thành phần dùng chung
├── assets/                # Tài nguyên cho frontend
│   ├── css/              # CSS chung và riêng từng trang
│   └── img/              # Hình ảnh
└── includes/              # Các thành phần dùng chung
    ├── footer.php        # Footer của trang
    └── header.php        # Header của trang
```

### 5.2 Cấu trúc cơ sở dữ liệu
- **users**: Lưu thông tin người dùng (bệnh nhân)
- **doctors**: Lưu thông tin bác sĩ
- **specialties**: Lưu thông tin chuyên khoa
- **services**: Lưu thông tin dịch vụ khám
- **appointments**: Lưu thông tin lịch hẹn
- **news**: Lưu thông tin tin tức sức khỏe

## 6. KẾ HOẠCH TRIỂN KHAI

### 6.1 Yêu cầu hệ thống
- **Web server**: Apache
- **PHP**: Phiên bản 7.4 trở lên
- **MySQL**: Phiên bản 5.7 trở lên

### 6.2 Hướng dẫn cài đặt
1. Cài đặt XAMPP (bao gồm Apache, MySQL, PHP)
2. Clone mã nguồn vào thư mục htdocs
3. Tạo cơ sở dữ liệu và import schema
4. Cấu hình kết nối cơ sở dữ liệu
5. Truy cập trang web qua localhost

## 7. NGƯỜI PHỤ TRÁCH

- **Người phát triển**: [Tên người phát triển]
- **Liên hệ**: [Email liên hệ]

---

© 2025 Hệ thống đặt lịch khám bệnh trực tuyến. Bản quyền thuộc về [Tên công ty/tổ chức].