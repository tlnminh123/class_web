<?php
session_start();

if (!isset($_SESSION["username"])) {
    die("⛔ Bạn chưa đăng nhập.");
}

$username = strtolower($_SESSION["username"]);
$info_file = __DIR__ . "/users/user_informations.xml";

if (!file_exists($info_file)) {
    die("❌ Không tìm thấy user_informations.xml.");
}

$info_data = simplexml_load_file($info_file);
$user_info = null;

foreach ($info_data->user as $user) {
    if ((string)$user->username === $username) {
        $user_info = $user;
        break;
    }
}

if (!$user_info) {
    die("⚠️ Không có thông tin cá nhân. Có thể bạn chưa từng cập nhật.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ cá nhân</title>
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <link rel="stylesheet" href="assets/css/all.min.css">
    <link rel="stylesheet" href="assets/css/profile.css">
	 <script src="js/profile.js"></script>
</head>
<body>
    <div class="profile-container">
        <!-- Header với navigation -->
        <header class="profile-header">
            <nav class="navbar">
                <div class="nav-brand">
                   <!-- <i class="fas fa-user-circle"></i> -->
                    <span>Hồ Sơ Cá Nhân</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php"><i></i> Trang chủ</a></li>
                    <li><a href="logout.php"><i ></i> Đăng xuất</a></li>
                </ul>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Sidebar Profile Summary -->

            <!-- Profile Content -->
            <div class="profile-content">
                <div class="content-header">
                    <h1>👤 Hồ sơ cá nhân</h1>
                    <p>Quản lý thông tin cá nhân và tài khoản của bạn</p>
                </div>

                <div class="profile-grid">
                    <!-- Personal Information Card -->
                    <div class="profile-card personal-info">
                        <div class="card-header">
                            <h2> Thông tin cá nhân</h2>
                            <span class="card-badge">Đã xác minh</span>
                        </div>
                        <div class="card-body">
                            <div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Họ và tên</label>
                                        <span class="info-value"><?= htmlspecialchars($user_info->name) ?></span>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-birthday-cake"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Ngày sinh</label>
                                        <span class="info-value"><?= htmlspecialchars($user_info->date_of_birth) ?></span>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Email</label>
                                        <span class="info-value"><?= htmlspecialchars($user_info->email) ?></span>
                                        <span class="verification-badge verified">
                                        </span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="card-footer">
                            <form method="get" action="updates.php" class="action-form">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-edit"></i> Chỉnh sửa thông tin
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Additional Info Cards -->
                    <div class="profile-card account-info">
                        <div class="card-header">
                            <h2><i class="fas fa-user-shield"></i> Thông tin tài khoản</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <label>Tên đăng nhập</label>
                                <span class="info-value"><?= htmlspecialchars($username) ?></span>
                            </div>
                            <div class="info-item">
                                <label>Trạng thái</label>
                                <span class="status-badge active">Đang hoạt động</span>
                            </div>
                        </div>
                    </div>

                    <!-- Security Card -->
                    <div class="profile-card security-info">
                        <div class="card-header">
                            <h2><i class="fas fa-lock"></i> Bảo mật</h2>
                        </div>
                        <div class="card-body">
                            <div class="security-item">
                                <div class="security-status">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <span>Mật khẩu</span>
                                </div>
                                <button class="btn btn-outline">Thay đổi</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="profile-footer">
            <div class="footer-content">
                <p>&copy; 2025 Thiết kế bởi Ngọc Minh. All rights reserved.</p>
                <div class="footer-links">
                    <a href="index.php">Trang chủ</a>
                    <a href="https://www.facebook.com/tlnminha1">Liên hệ hỗ trợ</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>