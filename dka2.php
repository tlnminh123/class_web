<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$xml_file = __DIR__ . "/users/user.xml";
$info_file = __DIR__ . "/users/user_informations.xml";

// Hàm lấy role
function getUserRole(string $username, SimpleXMLElement $xml): string {
    foreach ($xml->user as $user) {
        if ((string)$user->username === $username) {
            return (string)$user->role;
        }
    }
    return 'no';
}

if (!file_exists($xml_file)) {
    die("File user XML không tồn tại");
}

$usersXml = simplexml_load_file($xml_file);

// Chỉ admin cấp 1 hoặc 2 được vào
$userRole = getUserRole($_SESSION['username'], $usersXml);
if ($userRole !== 'yes' && $userRole !== 'role2') {
    header("Location: /chat/index.php");
    exit();
}

// Xử lý form đăng ký
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['register_username'] ?? '');
    $password = trim($_POST['register_password'] ?? '');
    $role = $_POST['register_role'] ?? 'no';

    if ($username === '' || $password === '') {
        $error = "Tên đăng nhập và mật khẩu không được để trống.";
    } else {
        foreach ($usersXml->user as $user) {
            if ((string)$user->username === $username) {
                $error = "Tên đăng nhập đã tồn tại.";
                break;
            }
        }

        if (!isset($error)) {
            // Thêm vào user.xml
            $newUser = $usersXml->addChild('user');
            $newUser->addChild('username', $username);
            $newUser->addChild('password',$password);
            $newUser->addChild('banned', 'no');
            $newUser->addChild('role', $role);
			$newUser->addChild('imformation_updates', 'no');
            $usersXml->asXML($xml_file);

            // Thêm vào user_informations.xml
            if (!file_exists($info_file)) {
                $infoXml = new SimpleXMLElement("<users></users>");
            } else {
                $infoXml = simplexml_load_file($info_file);
            }

            $nextStt = count($infoXml->user) + 1;

            $newInfo = $infoXml->addChild('user');
            $newInfo->addChild('stt', $nextStt);
            $newInfo->addChild('username', $username);
            $newInfo->addChild('name', 'no');
            $newInfo->addChild('date_of_birth', 'no');
            $newInfo->addChild('email', 'no');
            $newInfo->addChild('information_updates', 'no');

            $infoXml->asXML($info_file);

            $success = "Tạo tài khoản '$username' thành công!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản mới</title>
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <link rel="stylesheet" href="assets/css/dka2.css">
</head>
<body>
    <div class="ast-admin-container">
        <div class="ast-admin-register-box">
            <div class="ast-admin-register-header">
                <div class="ast-admin-logo">A+</div>
                <h1 class="ast-admin-title">Tạo tài khoản mới</h1>
                <p class="ast-admin-subtitle">Quản lý hệ thống lớp 10A6</p>
                
                <div class="ast-current-user">
                    <span class="ast-user-info">
                        Đang đăng nhập: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                        <span class="ast-user-role">
                            <?= $userRole === 'yes' ? 'Admin cấp 1' : 'Admin cấp 2' ?>
                        </span>
                    </span>
                </div>
            </div>

            <div class="ast-admin-register-form">
                <?php if (isset($error)): ?>
                    <div class="ast-message error ast-animate-slideInDown">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php elseif (isset($success)): ?>
                    <div class="ast-message success ast-animate-slideInDown">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <div class="ast-warning-box">
                    <div class="ast-warning-content">
                        <div class="ast-warning-icon">⚠️</div>
                        <div class="ast-warning-text">
                            <strong>Chú ý:</strong> Chỉ tạo tài khoản Admin cấp 2 khi thực sự cần thiết. 
                            Quyền hạn cao có thể ảnh hưởng đến bảo mật hệ thống.
                        </div>
                    </div>
                </div>

                <form method="POST" action="">
                    <div class="ast-form-group">
                        <label for="register_username" class="ast-form-label required">Tên đăng nhập</label>
                        <input type="text" id="register_username" name="register_username" 
                               class="ast-form-input" placeholder="Nhập tên đăng nhập" required 
                               value="<?= htmlspecialchars($_POST['register_username'] ?? '') ?>">
                    </div>

                    <div class="ast-form-group">
                        <label for="register_password" class="ast-form-label required">Mật khẩu</label>
                        <input type="password" id="register_password" name="register_password" 
                               class="ast-form-input" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="ast-form-group">
                        <label for="register_role" class="ast-form-label required">Phân quyền</label>
                        <select id="register_role" name="register_role" class="ast-form-input ast-form-select" aria-label="Chọn quyền">
                            <option value="no" selected class="ast-role-option user">
                                👤 User thường
                            </option>
                            <option value="role2" class="ast-role-option admin2">
                                🛡️ Admin cấp 2 (không khuyến khích)
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="ast-submit-btn">
                        🎯 Tạo tài khoản
                    </button>
                </form>
            </div>

            <div class="ast-form-footer">
                <p class="ast-footer-text">
                    Quay lại 
                    <a href="/chat/index.php" class="ast-footer-link">Bảng điều khiển</a>
                </p>
            </div>
        </div>
    </div>

    <button onclick="history.back()" class="ast-back-btn">
        <span class="ast-back-btn-icon">🔙</span>
        Quay lại
    </button>

    <script>
        // Real-time username validation
        document.addEventListener('DOMContentLoaded', function() {
            const usernameInput = document.getElementById('register_username');
            const roleSelect = document.getElementById('register_role');
            
            // Username validation
            usernameInput.addEventListener('input', function() {
                const username = this.value.trim();
                if (username.length < 3) {
                    this.classList.add('ast-is-invalid');
                    this.classList.remove('ast-is-valid');
                } else if (username.length >= 3) {
                    this.classList.add('ast-is-valid');
                    this.classList.remove('ast-is-invalid');
                } else {
                    this.classList.remove('ast-is-valid', 'ast-is-invalid');
                }
            });
            
            // Role selection styling
            roleSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                this.className = 'ast-form-input ast-form-select';
                if (selectedOption.value === 'role2') {
                    this.classList.add('ast-role-admin2');
                } else {
                    this.classList.add('ast-role-user');
                }
            });
            
            // Initialize role styling
            const initialOption = roleSelect.options[roleSelect.selectedIndex];
            if (initialOption.value === 'role2') {
                roleSelect.classList.add('ast-role-admin2');
            } else {
                roleSelect.classList.add('ast-role-user');
            }
        });
    </script>
</body>
</html>