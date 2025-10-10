<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if (!isset($_SESSION['username'])) die(json_encode(['success' => false, 'message' => '🚫 Bạn chưa đăng nhập']));

$xmlFile  = __DIR__ . '/users/user.xml';
$infoFile = __DIR__ . '/users/user_informations.xml';
$action   = $_GET['action'] ?? '';

function loadXML($file) {
    return simplexml_load_file($file);
}

function saveXML($xml, $file) {
    $xml->asXML($file);
}

if (!isset($_SESSION['username'])) die(json_encode(['success' => false, 'message' => '🚫 Bạn chưa đăng nhập']));

$xml = simplexml_load_file($xmlFile);
$currentUser = null;
foreach ($xml->user as $u) {
    if ((string)$u->username === $_SESSION['username']) {
        $currentUser = $u;
        break;
    }
}

$adminActions = ['ban_user', 'unban_user', 'change_role', 'change_password', 'register_user'];
if (in_array($action, $adminActions)) {
    if (!$currentUser || (string)$currentUser->role !== 'yes') {
        die(json_encode(['success' => false, 'message' => '🚫 Bạn không có quyền thực hiện hành động này']));
    }
}

// ===== 1. DANH SÁCH NGƯỜI DÙNG =====
if ($action === 'list_users') {
    $xml = loadXML($xmlFile);

    $out = '<table class="user-table"><tr>
        <th>👤 Tên đăng nhập</th>
        <th>🚫 Ban</th>
        <th>🛡️ Quyền</th>
        <th>🌐 IP gần nhất</th>
        <th>⚙️ Thao tác</th>
    </tr>';

    foreach ($xml->user as $u) {
        $name = (string)$u->username;
        if ($name === 'admin') continue;

        $banned = (string)$u->banned === 'yes' ? '✔️' : '❌';
        $role = match((string)$u->role) {
            'yes' => '🛡️ Admin cấp 1',
            'role2' => '🛡️ Admin cấp 2',
            default => '❎ Không'
        };

        $ip = isset($u->ip) ? htmlspecialchars((string)$u->ip) : '<i>Không có</i>';

        $out .= "<tr>
            <td>{$name}</td>
            <td>{$banned}</td>
            <td>{$role}</td>
            <td><code>{$ip}</code></td>
            <td>
                <button data-user-action=\"ban_user\" data-username=\"{$name}\" class=\"btn-ban\">🚫 Khóa</button>
                <button data-user-action=\"unban_user\" data-username=\"{$name}\" class=\"btn-unban\">✅ Mở</button>
                <button data-user-action=\"change_role\" data-username=\"{$name}\" class=\"btn-role\">🔁 Đổi quyền</button>
                <button data-user-action=\"change_password\" data-username=\"{$name}\" class=\"btn-pass\">🔐 Đổi mật khẩu</button>
            </td>
        </tr>";
    }

    $out .= '</table>';
    echo json_encode(['success' => true, 'html' => $out]);
    exit;
}


// ===== 2. XỬ LÝ TÁC VỤ NGƯỜI DÙNG =====
if (in_array($action, ['ban_user', 'unban_user', 'change_role', 'change_password'])) {
    $uname = $_GET['user'] ?? '';
    $xml = loadXML($xmlFile);
    $ok = false;

    foreach ($xml->user as $u) {
        if ((string)$u->username === $uname) {
            switch ($action) {
                case 'ban_user': $u->banned = 'yes'; break;
                case 'unban_user': $u->banned = 'no'; break;
                case 'change_role':
                    $r = (string)$u->role;
                    $u->role = $r === 'no' ? 'role2' : ($r === 'role2' ? 'yes' : 'no');
                    break;
                case 'change_password':
                    if (isset($_GET['newpass'])) $u->password = $_GET['newpass'];
                    break;
            }
            saveXML($xml, $xmlFile);
            $ok = true;
            break;
        }
    }

    echo json_encode([
        'success' => $ok,
        'message' => $ok ? '✅ Thao tác thành công' : '❌ Không tìm thấy người dùng'
    ]);
    exit;
}

// ===== 3. DANH SÁCH ĐƠN =====
if ($action === 'list_forms') {
    $type = $_GET['type'] ?? '';
    $map = ['unban' => 'unban', 'repass' => 'repass', 'dk' => 'dk'];
    $dir = isset($map[$type]) ? __DIR__ . '/form/' . $map[$type] : '';

    $out = '';
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), ['.', '..']);
        if (empty($files)) {
            $out = '<em>📭 Không có đơn nào trong thư mục.</em>';
        } else {
            foreach ($files as $f) {
                $path = $dir . '/' . $f;
                $relativePath = 'form/' . $map[$type] . '/' . $f;
                $out .= "<div>📄 <b>{$f}</b>
                    <button class=\"btn-view\" data-path=\"{$relativePath}\">👁️ Xem</button>
                    <button class=\"btn-delete\" data-path=\"{$relativePath}\">🗑️ Xóa</button>
                </div>";
            }
        }
    } else {
        $out = '<em>⚠️ Không tìm thấy thư mục chứa đơn</em>';
    }

    echo json_encode(['success' => true, 'html' => $out]);
    exit;
}


// ===== 4. XEM ĐƠN =====
if ($action === 'view_form') {
    $path = $_GET['path'] ?? '';
    if (is_file($path)) {
        $content = htmlspecialchars(file_get_contents($path));
        echo json_encode(['success' => true, 'html' => nl2br($content)]);
    } else {
        echo json_encode(['success' => false, 'html' => '❌ Không tìm thấy file']);
    }
    exit;
}

// ===== 5. XÓA ĐƠN =====
if ($action === 'delete_form') {
    $path = $_GET['path'] ?? '';
    if (is_file($path)) {
        unlink($path);
        ob_clean(); // Xóa mọi thứ đã bị in ra (tránh rác)
        echo json_encode(['success' => true]); // Không có message
    } else {
        ob_clean();
        echo json_encode(['success' => false, 'message' => '❌ File không tồn tại']);
    }
    exit;
}


// ===== 6. TẠO TÀI KHOẢN =====
if ($action === 'register_user') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $r = $_POST['role'] ?? 'no';

    if (!$u || !$p) {
        echo json_encode(['success' => false, 'message' => '⚠️ Không được để trống']);
        exit;
    }

    $xml = loadXML($xmlFile);
    foreach ($xml->user as $x) {
        if ((string)$x->username === $u) {
            echo json_encode(['success' => false, 'message' => '❌ Tên người dùng đã tồn tại']);
            exit;
        }
    }

    $nu = $xml->addChild('user');
    $nu->addChild('username', $u);
    $nu->addChild('password', $p);
    $nu->addChild('banned', 'no');
    $nu->addChild('role', $r);
    saveXML($xml, $xmlFile);

    $ix = file_exists($infoFile) ? loadXML($infoFile) : new SimpleXMLElement('<users></users>');
    $ni = $ix->addChild('user');
    $ni->addChild('stt', count($ix->user) + 1);
    $ni->addChild('username', $u);
    $ni->addChild('name', 'no');
    $ni->addChild('date_of_birth', 'no');
    $ni->addChild('email', 'no');
    saveXML($ix, $infoFile);

    echo json_encode(['success' => true, 'message' => '✅ Tạo tài khoản thành công']);
    exit;
}

// ===== 7. THÔNG TIN NGƯỜI DÙNG =====
if ($action === 'list_infos') {
    $out = '<table><tr><th>STT</th><th>👤 Người dùng</th><th>📛 Họ tên</th><th>🎂 Ngày sinh</th><th>📧 Email</th></tr>';
    $ix = file_exists($infoFile) ? loadXML($infoFile) : null;
    if ($ix) {
        $i = 1;
        foreach ($ix->user as $u) {
            $out .= "<tr><td>{$i}</td><td>{$u->username}</td><td>{$u->name}</td><td>{$u->date_of_birth}</td><td>{$u->email}</td></tr>";
            $i++;
        }
    }
    $out .= '</table>';
    echo json_encode(['success' => true, 'html' => $out]);
    exit;
}

// ===== 8. KHÔNG HỢP LỆ =====
echo json_encode(['success' => false, 'message' => '❓ Hành động không hợp lệ']);
