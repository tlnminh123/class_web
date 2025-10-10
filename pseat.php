<?php
session_start();
header('Content-Type: application/json');

// Kiểm tra đăng nhập
$username = $_SESSION['username'] ?? null;
if (!$username) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập.']);
    exit;
}

// Kiểm tra ghế được gửi lên
$seat_name = $_POST['seat'] ?? null;
if (!$seat_name) {
    echo json_encode(['success' => false, 'message' => 'Thiếu tên ghế.']);
    exit;
}

// Kiểm tra quyền admin
$user_file = __DIR__ . "/users/user.xml";
if (!file_exists($user_file)) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy dữ liệu người dùng.']);
    exit;
}

$user_data = simplexml_load_file($user_file);
$is_admin = false;
foreach ($user_data->user as $u) {
    if ((string)$u->username === $username && (string)$u->role === 'yes') {
        $is_admin = true;
        break;
    }
}

// Đảm bảo file seat.xml tồn tại
$seat_file = __DIR__ . '/seat.xml';
if (!file_exists($seat_file)) {
    file_put_contents($seat_file, '<seats></seats>');
}

$xml = @simplexml_load_file($seat_file);
if (!$xml) {
    echo json_encode(['success' => false, 'message' => 'Không thể đọc file ghế.']);
    exit;
}

// Tìm ghế
$found = null;
foreach ($xml->seat as $seat) {
    if ((string)$seat->name === $seat_name) {
        $found = $seat;
        break;
    }
}

if ($found) {
    $current_owner = (string)$found->owner;

    if ($current_owner === $username || $is_admin) {
        // Nếu là người chọn hoặc admin thì bỏ ghế
        $dom = dom_import_simplexml($found);
        $dom->parentNode->removeChild($dom);
    } else {
        // Ghế đã có người chọn khác
        echo json_encode(['success' => false, 'message' => 'Ghế này đã có người chọn.']);
        exit;
    }
} else {
    // Thêm ghế mới
    $new = $xml->addChild('seat');
    $new->addChild('name', $seat_name);
    $new->addChild('owner', $username);
}

// Lưu lại
$xml->asXML($seat_file);
echo json_encode(['success' => true]);
