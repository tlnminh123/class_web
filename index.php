<?php
session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

$username   = strtolower($_SESSION["username"]);
$user_file  = __DIR__ . "/users/user.xml";
$info_file  = __DIR__ . "/users/user_informations.xml"; // thống nhất tên file

if (!file_exists($user_file) || !file_exists($info_file)) {
    die("❌ Thiếu file user.xml hoặc user_informations.xml.");
}

// --- Kiểm tra cập nhật thông tin và lấy role ---
$users_data = simplexml_load_file($user_file);
$found      = false;
$role       = "no"; // mặc định
foreach ($users_data->user as $user) {
    if ((string)$user->username === $username) {
        $found  = true;
        $status = trim((string)$user->imformation_updates);
        $role   = trim((string)$user->role);

        if ($status === "no") {
            header("Location: updates.php");
            exit();
        }
        break;
    }
}
if (!$found) {
    die("❌ Không tìm thấy người dùng trong user.xml.");
}
$isAdmin1 = ($role === "yes");

// --- Lấy tên đầy đủ từ user_informations.xml ---
$info_data = simplexml_load_file($info_file);
$full_name = $username; // fallback
foreach ($info_data->user as $info_user) {
    if ((string)$info_user->username === $username) {
        $full_name = (string)$info_user->name;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ lớp 10A6</title>
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <link rel="stylesheet" href="assets/css/index.css">
    <link href="assets/css/font.css" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="header-content">
        <div class="welcome-text animate-fadeInUp">
            <h1>Chào mừng <?= htmlspecialchars($full_name) ?></h1>
            <p class="welcome-subtitle">Đến với cổng thông tin lớp 10A6 - Phiên bản 2.0</p>
        </div>
    </div>
</header>

<nav class="main-navigation">
    <div class="nav-container">
        <ul class="nav-menu">
            <div class="nav-left">
                <li><a href="seat.php" class="nav-link"><span class="nav-icon">📌</span> Sơ đồ lớp</a></li>
                <li><a href="chat/index.php" class="nav-link"><span class="nav-icon">💬</span> Phòng chat</a></li>
                <li><a href="https://10a6forum.freeflarum.com" class="nav-link"><span class="nav-icon">📖</span> Diễn đàn</a></li>
            </div>
            <div class="nav-right">
                <li><a href="repass.php" class="nav-link"><span class="nav-icon">🔐</span> Đổi mật khẩu</a></li>
                <li><a href="game/index.php" class="nav-link"><span class="nav-icon">🎮</span> Giải trí</a></li>
				<li><a href="profile.php" class="nav-link"><span class="nav-icon">✉</span> Hồ sơ</a></li>
                <?php if ($isAdmin1): ?>
                    <li><a href="/admin.php" class="nav-link admin"><span class="nav-icon">🔧</span> Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="nav-link logout"><span class="nav-icon">🚪</span> Đăng xuất</a></li>
            </div>
        </ul>
    </div>
</nav>

<main class="main-content">
    <div class="features-grid">
        <div class="feature-card animate-slideInLeft">
            <div class="card-header">
                <div class="card-icon">📌</div>
                <h3>Sơ đồ lớp</h3>
            </div>
            <div class="card-content">
                <p>Xem sơ đồ lớp học và đăng ký chỗ ngồi theo ý muốn. Quản lý vị trí ngồi một cách khoa học và tiện lợi.</p>
            </div>
            <div class="card-actions">
                <a href="seat.php" class="card-link">Xem sơ đồ lớp <span>→</span></a>
            </div>
        </div>

        <div class="feature-card animate-fadeInUp">
            <div class="card-header">
                <div class="card-icon">💬</div>
                <h3>Phòng Chat</h3>
            </div>
            <div class="card-content">
                <p>Trò chuyện, chia sẻ thông tin và kết nối với tất cả thành viên trong lớp 10A6 một cách nhanh chóng.</p>
            </div>
            <div class="card-actions">
                <a href="chat/index.php" class="card-link">Vào phòng chat <span>→</span></a>
            </div>
        </div>

        <div class="feature-card animate-fadeInUp">
            <div class="card-header">
                <div class="card-icon">📖</div>
                <h3>Diễn đàn</h3>
            </div>
            <div class="card-content">
                <p>Thảo luận các vấn đề học tập, chia sẻ kiến thức và trao đổi thông tin quan trọng của lớp.</p>
            </div>
            <div class="card-actions">
                <a href="https://10a6forum.freeflarum.com/" class="card-link">Truy cập diễn đàn <span>→</span></a>
            </div>
        </div>

        <div class="feature-card animate-slideInRight">
            <div class="card-header">
                <div class="card-icon">🎮</div>
                <h3>Khu vực giải trí</h3>
            </div>
            <div class="card-content">
                <p>Thư giãn sau những giờ học căng thẳng với bộ sưu tập game hấp dẫn ngay trên trình duyệt.</p>
            </div>
            <div class="card-actions">
                <a href="game/index.php" class="card-link">Khám phá ngay <span>→</span></a>
            </div>
        </div>
    </div>
</main>

<footer class="site-footer">
    <div class="footer-content">
        <p class="footer-text">&copy; 2025 - Lớp 10A6 - Thiết kế bởi Ngọc Minh</p>
    </div>
</footer>

</body>
</html>