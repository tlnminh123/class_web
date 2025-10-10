<?php
session_start();
$username = $_SESSION['username'] ?? null;
$is_admin = false;

// --- Đường dẫn file ---
$seat_file = __DIR__ . "/seat.xml";
$user_file = __DIR__ . "/users/user.xml";
$user_info_file = __DIR__ . "/users/user_informations.xml";

// --- Tự tạo nếu thiếu ---
if (!file_exists($seat_file)) {
    $default = new SimpleXMLElement('<?xml version="1.0"?><seats></seats>');
    $default->asXML($seat_file);
}

// --- Load dữ liệu XML ---
$seats = @simplexml_load_file($seat_file);
$users = @simplexml_load_file($user_file);
$user_info = @simplexml_load_file($user_info_file);

// --- Kiểm tra quyền admin ---
if ($username && $users) {
    foreach ($users->user as $u) {
        if ((string)$u->username === $username && (string)$u->role === 'yes') {
            $is_admin = true;
            break;
        }
    }
}

// --- Hàm lấy tên hiển thị ---
function get_name($username, $user_info) {
    if (!$user_info) return $username;
    foreach ($user_info->user as $u) {
        if ((string)$u->username === $username) {
            return (string)$u->name;
        }
    }
    return $username;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sơ đồ lớp 10A6</title>
    <link rel="icon" href="assets/Empty.ico">
    <link rel="stylesheet" href="assets/css/seat.css">
    <link href="assets/css/font.css" rel="stylesheet">
</head>
<body>

<header class="classroom-header">
    <div class="header-content">
        <div class="header-title">
            <h1>Sơ đồ lớp 10A6</h1>
            <p class="subtitle">Quản lý và đăng ký chỗ ngồi</p>
        </div>
        
        <?php if ($username): ?>
            <div class="user-info">
                <p>Xin chào: <strong><?= htmlspecialchars($username) ?></strong></p>
                <a href="/logout.php" class="auth-btn logout">
                    <span>🚪</span> Đăng xuất
                </a>
            </div>
        <?php else: ?>
            <div class="user-info">
                <p>Vui lòng đăng nhập để chọn ghế</p>
                <a href="/login.php" class="auth-btn">
                    <span>🔐</span> Đăng nhập
                </a>
            </div>
        <?php endif; ?>
    </div>
</header>

<main class="classroom-container">
    <div class="classroom-overview">
        <div class="classroom-title">
            <h2>Sơ đồ chỗ ngồi lớp học</h2>
        </div>
        
        <div class="classroom-map">
            <!-- Teacher Desk at bottom center -->
            <div class="teacher-desk">BÀN GIÁO VIÊN</div>
            
            <!-- Seat Grid -->
            <div class="seat-grid">
                <?php
                $positions = [
                    // Hàng F (trên cùng)
                    ['F1', 1, 1], ['F2', 1, 2], ['F3', 1, 3], ['F4', 1, 4],
                    ['F5', 1, 6], ['F6', 1, 7], ['F7', 1, 8], ['F8', 1, 9],
                    // Hàng E
                    ['E1', 2, 1], ['E2', 2, 2], ['E3', 2, 3], ['E4', 2, 4],
                    ['E5', 2, 6], ['E6', 2, 7], ['E7', 2, 8], ['E8', 2, 9],
                    // Hàng D
                    ['D1', 3, 1], ['D2', 3, 2], ['D3', 3, 3], ['D4', 3, 4],
                    ['D5', 3, 6], ['D6', 3, 7], ['D7', 3, 8], ['D8', 3, 9],
                    // Hàng C
                    ['C1', 4, 1], ['C2', 4, 2], ['C3', 4, 3], ['C4', 4, 4],
                    ['C5', 4, 6], ['C6', 4, 7], ['C7', 4, 8], ['C8', 4, 9],
                    // Hàng B
                    ['B1', 5, 1], ['B2', 5, 2], ['B3', 5, 3], ['B4', 5, 4],
                    ['B5', 5, 6], ['B6', 5, 7], ['B7', 5, 8], ['B8', 5, 9],
                    // Hàng A (dưới cùng)
                    ['A1', 6, 1], ['A2', 6, 2], ['A3', 6, 3], ['A4', 6, 4],
                    ['A5', 6, 6], ['A6', 6, 7], ['A7', 6, 8], ['A8', 6, 9],
                ];

                foreach ($positions as [$name, $row, $col]) {
                    $owner = null;
                    if ($seats) {
                        foreach ($seats->seat as $s) {
                            if ((string)$s->name === $name) {
                                $owner = (string)$s->owner;
                                break;
                            }
                        }
                    }

                    $owner_name = $owner ? get_name($owner, $user_info) : "";
                    $is_mine = $owner === $username;
                    $class = "seat-btn";
                    if ($owner) {
                        $class .= $is_mine ? " mine" : " taken";
                    } else {
                        $class .= " available";
                    }
                    
                    if ($is_admin && $owner && !$is_mine) {
                        $class .= " admin";
                    }
                ?>
                    <button class="<?= $class ?>" data-seat="<?= $name ?>" 
                            style="grid-row: <?= $row ?>; grid-column: <?= $col ?>;"
                            <?= ($owner && !$is_mine && !$is_admin) ? 'disabled' : '' ?>>
                        <span class="seat-number"><?= $name ?></span>
                        <span class="seat-owner"><?= htmlspecialchars($owner_name) ?></span>
                    </button>
                <?php } ?>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="seat-legend">
            <h3 class="legend-title">Chú thích</h3>
            <div class="legend-items">
                <div class="legend-item">
                    <div class="legend-color available"></div>
                    <span class="legend-text">Ghế trống</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color taken"></div>
                    <span class="legend-text">Ghế đã có người</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color mine"></div>
                    <span class="legend-text">Ghế của bạn</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color admin"></div>
                    <span class="legend-text">Quản lý (Admin)</span>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="stats-panel">
            <div class="stat-card available">
                <div class="stat-number"><?= count(array_filter($positions, function($pos) use ($seats) {
                    $name = $pos[0];
                    foreach ($seats->seat as $s) {
                        if ((string)$s->name === $name) return false;
                    }
                    return true;
                })) ?></div>
                <div class="stat-label">Ghế trống</div>
            </div>
            <div class="stat-card taken">
                <div class="stat-number"><?= count(array_filter($positions, function($pos) use ($seats) {
                    $name = $pos[0];
                    foreach ($seats->seat as $s) {
                        if ((string)$s->name === $name) return true;
                    }
                    return false;
                })) ?></div>
                <div class="stat-label">Ghế đã đăng ký</div>
            </div>
            <div class="stat-card mine">
                <div class="stat-number"><?= $username ? count(array_filter($positions, function($pos) use ($seats, $username) {
                    $name = $pos[0];
                    foreach ($seats->seat as $s) {
                        if ((string)$s->name === $name && (string)$s->owner === $username) return true;
                    }
                    return false;
                })) : 0 ?></div>
                <div class="stat-label">Ghế của bạn</div>
            </div>
            <div class="stat-card total">
                <div class="stat-number"><?= count($positions) ?></div>
                <div class="stat-label">Tổng số ghế</div>
            </div>
        </div>
    </div>
</main>

<script>
window.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".seat-btn").forEach(button => {
        button.addEventListener("click", e => {
            e.preventDefault();
            
            if (button.disabled) return;
            
            const seatName = button.dataset.seat;
            const originalText = button.innerHTML;
            
            // Show loading state
            button.classList.add('loading');
            button.disabled = true;
            
            fetch("pseat.php", {
                method: "POST",
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: "seat=" + encodeURIComponent(seatName)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification('Đăng ký ghế thành công!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || "Lỗi không xác định.", 'error');
                    button.classList.remove('loading');
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                showNotification('Lỗi kết nối! Vui lòng thử lại.', 'error');
                button.classList.remove('loading');
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });
    });
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `seat-notification ${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 100);
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
});
</script>

</body>
</html>