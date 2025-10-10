# 🏫 Class Management Engine (PHP + XML)

Một dự án web dành cho **lớp học**, viết bằng PHP thuần — **không cần cơ sở dữ liệu**, chỉ dùng file **XML** để lưu thông tin người dùng, chỗ ngồi và dữ liệu diễn đàn.

---

## ✨ Tính năng chính
- 👤 **Hệ thống tài khoản**: đăng ký, đăng nhập, phân quyền (học sinh / admin)
- 💺 **Sơ đồ lớp học**: click vào ghế để đăng ký chỗ ngồi, đổi màu khi đã chọn
- 💬 **Diễn đàn lớp**: tạo chủ đề, bình luận, phân loại theo chuyên mục
- 📂 **Lưu trữ bằng XML**: dễ backup, dễ chỉnh tay, không cần MySQL
- 🔒 **Phân quyền người dùng**: quản trị viên có thể khóa tài khoản, đổi vai trò
- 🎨 **Giao diện thân thiện**: HTML + CSS tinh gọn, có thể chỉnh màu chủ đề

---
## 👤 Tài khoản dùng thử (Demo Account)

Dưới đây là danh sách các tài khoản được công bố công khai nhằm mục đích kiểm thử hệ thống. Người dùng có thể sử dụng các tài khoản này để đăng nhập vào hệ thống và thử nghiệm các chức năng.

| Loại tài khoản                  | Tên đăng nhập | Mật khẩu     | Quyền hạn                                           |
|---------------------------------|----------------|--------------|----------------------------------------------------|
| 👑 Quản trị viên (Admin cấp 1) | admin           | a            | Toàn quyền (ban user, đổi mật khẩu, chỉnh sửa...)  |
| 👤 Người dùng thường            | user1          | a            | Chat bình thường                                   |

> 📝 **Lưu ý:** Các tài khoản demo có thể bị reset bất kỳ lúc nào. Không nên sử dụng cho mục đích lưu trữ thông tin quan trọng.

---

### 💬 Mở góp ý & sửa lỗi

Nếu bạn gặp lỗi khi dùng tài khoản trên, vui lòng tạo issue hoặc liên hệ qua GitHub. Dự án này luôn chào đón sự đóng góp!



## 🚀 Cài đặt

```bash
git clone https://github.com/tlnminh123/class_web.git
cd class_web
composer install