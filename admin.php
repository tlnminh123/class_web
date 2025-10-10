<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit;
}

$xmlFile = __DIR__ . '/users/user.xml';
$xml = simplexml_load_file($xmlFile);
$user = null;
foreach ($xml->user as $u) {
    if ((string)$u->username === $_SESSION['username']) {
        $user = $u;
        break;
    }
}

if (!$user || (string)$user->role !== 'yes') { 
    echo "<!DOCTYPE html>
    <html lang='vi'>
    <head>
      <meta charset='UTF-8'>
      <title>403 - Không có quyền</title>
      <meta http-equiv='refresh' content='3;url=../index.php'>
      <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; }
        h1 { color: red; }
      </style>
    </head>
    <body>
      <h1>🚫 Bạn không có quyền truy cập</h1>
      <p>Đang chuyển hướng bạn về trang chủ...</p>
    </body>
    </html>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<script src="assets/js/admin.js"></script>
<link rel="stylesheet" href="assets/css/admin.css">
<link rel="icon" href="assets/Empty.ico">
</head>
<body>
<div class="admin-container">
  <aside class="sidebar"><h2>🔧 Admin panel</h2>
    <ul class="tab-links">
      <li><button data-tab="users" class="active">👥 Quản lý user</button></li>
      <li><button data-tab="forms">📂 Duyệt đơn</button></li>
      <li><button data-tab="register">➕ Tạo tài khoản</button></li>
      <li><button data-tab="userinfo">📑 Thông tin người dùng</button></li>
     <li><a href="/index.php" class="btn-back">🔙 Quay lại</a></li>
    </ul>
  </aside>
  <main class="tab-content">
    <section id="users" class="tab active"><h3>👥 Quản lý người dùng</h3><div id="user-list"><em>Đang tải...</em></div></section>

    <section id="forms" class="tab"><h3>📂 Duyệt đơn</h3>
      <div class="form-tabs">
        <button class="form-tab-btn active" data-form="unban">Gỡ Ban</button>
        <button class="form-tab-btn" data-form="repass">Đổi mật khẩu</button>
        <button class="form-tab-btn" data-form="dk">Đăng ký</button>
      </div>
      <div id="form-list"><em>Đang tải...</em></div>
      <div id="form-content"></div>
    </section>
    <section id="register" class="tab">
    <h3>➕ Tạo tài khoản</h3>
    <form id="register-form">
        <div class="form-group">
            <label class="form-label" for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" class="form-input" placeholder="Nhập tên đăng nhập" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" class="form-input" placeholder="Nhập mật khẩu" required>
            <div class="password-strength">
                <div class="password-strength-bar"></div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="confirm-password">Xác nhận mật khẩu:</label>
            <input type="password" id="confirm-password" name="confirm-password" class="form-input" placeholder="Nhập lại mật khẩu" required>
            <div class="password-match">
                <span class="match-text">Mật khẩu khớp</span>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="role">Quyền:</label>
            <select id="role" name="role" class="form-select" required>
                <option value="no">User</option>
                <option value="role2">Admin 2</option>
                <option value="yes">Admin 1</option>
            </select>
        </div>
        
        <button type="submit" class="form-submit">Tạo tài khoản</button>
    </form>
    <div id="register-msg"></div>
</section>
    <section id="userinfo" class="tab">
		 <button class="export-btn" onclick="window.location.href='export_users.php'" style="margin-bottom: 10px;">📤 Xuất Excel</button>
      <div id="userinfo-list"><em>Đang tải...</em></div>
    </section>
  </main>
</div>
</body>
</html>
