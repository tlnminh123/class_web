<?php
session_start();

function load_users(){
    $fn = __DIR__ . '/users/user.xml';
    if(!file_exists($fn)) return [];
    $xml = simplexml_load_file($fn);
    $out = [];
    foreach($xml->user as $u){
        $out[] = [
            'username' => (string)$u->username,
            'password' => (string)$u->password,
            'banned' => (string)$u->banned,
            'role' => (string)$u->role
        ];
    }
    return $out;
}
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    $users = load_users(); // hàm này bạn tự viết, trả về mảng user

    $found = false;
    foreach ($users as $user) {
        if ($user['username'] === $u && $user['password'] === $p) {
            $found = true;

            if ($user['banned'] === 'yes') {
                // Redirect sang trang unban.php và gửi kèm message
                header('Location: unban.php?msg=' . urlencode('Tài khoản của bạn đã bị khóa, vui lòng gỡ ban trước.'));
                exit;
            } else {
                // Đăng nhập bình thường
                $_SESSION['username'] = $u;
                $_SESSION['role'] = $user['role'];
                header('Location: index.php');
                exit;
            }
        }
    }

    if (!$found) {
        $err = 'Sai username hoặc password.';
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="/assets/css/login.css">
	<link rel="icon" href="/assets/Empty.ico">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="/assets/whc.webp" alt="Logo" class="logo">
            <h1>Đăng nhập</h1>
            <p class="login-subtitle">Lớp 10A6</p>
        </div>
        
        <form method="post" class="login-form">
            <?php if($err): ?>
                <div class="error-message">
                    <span class="error-icon">⚠️</span>
                    <span><?php echo htmlspecialchars($err); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Tên đăng nhập</label>
                <div class="input-wrapper">
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-input" 
                           placeholder="Nhập username của bạn" 
                           required
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           autocomplete="username">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="input-wrapper">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-input" 
                           placeholder="Nhập mật khẩu của bạn" 
                           required
                           autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePassword()">👁️</button>
                </div>
            </div>
            
           
            <div class="login-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    Ghi nhớ đăng nhập
                </label>
                <a href="repass_form.php" class="forgot-password">Quên mật khẩu?</a>
            </div>
           
            
            <button type="submit" class="submit-btn">
                <span class="submit-icon">→</span>
                <span>Đăng nhập</span>
            </button>
        </form>
        
        <div class="login-footer">
            <a href="index.php" class="home-link">
                <span class="home-icon">🏠</span>
                <span>Về trang chủ</span>
            </a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }
        
        // Auto-focus username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Form submission loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('.submit-btn');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<span>Đang đăng nhập...</span>';
        });
        
        // Enter key support
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const focused = document.activeElement;
                if (focused && focused.type !== 'textarea') {
                    document.querySelector('form').requestSubmit();
                }
            }
        });
    </script>
</body>
</html>