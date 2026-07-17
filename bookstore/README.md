# Arsha Bookstore — Laravel Bookstore Backend & Frontend

Dự án Arsha Bookstore là một hệ thống website bán truyện tranh trực tuyến được xây dựng trên nền tảng Laravel (phiên bản 11.x) kết hợp với Bootstrap 5, đáp ứng đầy đủ các yêu cầu nghiệp vụ về quản lý sản phẩm, giỏ hàng, đặt hàng, và tích hợp cổng thanh toán trực tuyến VNPay.

---

## 🚀 Các Tính Năng Chính

### 1. Giao Diện Khách Hàng (Customer Client)
* **Trang chủ & Danh sách truyện:** Hiển thị lưới truyện (tỷ lệ ảnh bìa 3:4 chuẩn sàn TMĐT) kèm bộ lọc theo Thể loại, Sắp xếp (mới nhất, giá tăng/giảm, bán chạy nhất).
* **Chi tiết truyện:** Xem thông tin sách, số lượng tồn kho, tóm tắt nội dung và các đầu sách cùng thể loại liên quan. Hỗ trợ mua nhanh hoặc thêm vào giỏ hàng.
* **Giỏ hàng & Đặt hàng:** Quản lý số lượng mua bằng AJAX, tự động áp dụng luật giao hàng (miễn phí vận chuyển cho đơn hàng từ 300.000 ₫, dưới 300.000 ₫ tính phí 30.000 ₫).
* **Thanh toán:** Tích hợp phương thức thanh toán COD và Cổng thanh toán VNPay Sandbox.
* **Lịch sử & Tra cứu đơn hàng:** Thành viên đăng nhập có thể xem lịch sử mua hàng và hủy đơn (khi đơn ở trạng thái `processing`). Khách vãng lai có thể tra cứu nhanh trạng thái đơn qua mã đơn và số điện thoại.
* **Thanh toán lại (VNPay Retry):** Cho phép thanh toán lại cho các đơn hàng VNPay bị lỗi hoặc chưa hoàn thành thanh toán.

### 2. Trang Quản Trị (Admin Panel)
* **Dashboard thống kê:** Xem doanh thu thực tế, tổng đơn hàng, số đầu sách và số thành viên đăng ký. Báo cáo trạng thái đơn hàng trực quan.
* **Quản lý Thể loại & Truyện (CRUD):** Thêm, sửa, xóa danh mục và truyện. Hỗ trợ tải lên ảnh bìa, soft delete truyện để bảo toàn dữ liệu khóa ngoại của đơn hàng cũ.
* **Quản lý đơn hàng:** Xem chi tiết đơn hàng và cập nhật trạng thái đơn (processing, shipping, completed, cancelled) & trạng thái thanh toán (pending, paid, failed).
* **Quản lý người dùng:** Danh sách thành viên đăng ký.

---

## 🛠️ Yêu Cầu Hệ Thống

* **PHP:** >= 8.2 (Khuyến nghị PHP 8.3/8.4)
* **Composer**
* **Node.js & npm**
* **SQLite / MySQL**

---

## 📦 Hướng Dẫn Cài Đặt

1. **Cài đặt thư viện dependencies:**
   ```bash
   composer install
   npm install
   ```

2. **Cấu hình môi trường (.env):**
   Copy file `.env.example` thành `.env` và thiết lập các cấu hình cần thiết:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Cấu hình Database SQLite (mặc định):**
   Tạo file database SQLite trống:
   ```bash
   touch database/database.sqlite
   ```
   Đảm bảo cấu hình trong `.env`:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=absolute_path_to_database.sqlite
   ```

4. **Chạy Migration & Seeder dữ liệu mẫu:**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Tạo symbolic link cho thư mục lưu trữ ảnh:**
   ```bash
   php artisan storage:link
   ```

6. **Chạy ứng dụng:**
   * Khởi động server PHP:
     ```bash
     php artisan serve
     ```
   * Chạy Vite cho Frontend:
     ```bash
     npm run dev
     ```

---

## 🔑 Tài Khoản Thử Nghiệm

Hệ thống seeder đã tạo sẵn hai tài khoản sau để phục vụ kiểm thử:

* **Tài khoản Admin (Quản trị viên):**
  * **Email:** `admin@shop.com`
  * **Mật khẩu:** `password`
* **Tài khoản Customer (Khách hàng):**
  * **Email:** `user@shop.com`
  * **Mật khẩu:** `password`

---

## 💳 Thông Tin Thẻ Thử Nghiệm VNPay (Sandbox)

Khi chọn thanh toán qua VNPay, bạn sẽ được chuyển hướng sang cổng thanh toán thử nghiệm của VNPay. Hãy sử dụng thông tin thẻ sau để test:

* **Ngân hàng:** NCB
* **Số thẻ:** `9704198526191432198`
* **Tên chủ thẻ:** `NGUYEN VAN A`
* **Ngày phát hành:** `07/15`
* **Mật khẩu OTP:** `123456`
