<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"]);
    $dob = trim($_POST["dob"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $message = trim($_POST["message"]);

    if ($password !== $confirm_password) {
        $error = "❌ Mật khẩu và xác nhận mật khẩu không khớp!";
    } else {
        $save_dir = __DIR__ . '/form/dk';
        if (!is_dir($save_dir)) {
            mkdir($save_dir, 0777, true);
        }

        $filename = $save_dir . "/dk_" . time() . ".txt";
        $content  = "Họ và tên: $fullname\n";
        $content .= "Ngày sinh: $dob\n";
        $content .= "Email: $email\n";
        $content .= "Mật khẩu đăng ký: $password\n";
        $content .= "Lời nhắn gửi Admin:\n$message\n";

        file_put_contents($filename, $content);
        $success = "📩 Đơn đăng ký đã được gửi! Admin sẽ sớm duyệt.";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <link rel="stylesheet" href="assets/css/dangki.css">
    </head>
<body>
    <div class="ast-container">
        <div class="ast-register-box ast-animate-bounceIn">
            <div class="ast-register-header">
                <div class="ast-register-logo">A</div>
                <h1 class="ast-register-title">Đăng ký tài khoản</h1>
                <p class="ast-register-subtitle">Tham gia hệ thống lớp 10A6</p>
            </div>

            <div class="ast-register-form">
                <?php if (!empty($error)): ?>
                    <div class="ast-message error ast-animate-slideInDown">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="ast-message success ast-animate-slideInDown">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="ast-form-group">
                        <label for="fullname" class="ast-form-label required">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname" class="ast-form-input" 
                               placeholder="Nguyễn Văn A" required value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
                    </div>

                    <div class="ast-form-group">
                        <label for="dob" class="ast-form-label required">Ngày tháng năm sinh</label>
                        <input type="date" id="dob" name="dob" class="ast-form-input" required 
                               value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
                    </div>

                    <div class="ast-form-group">
                        <label for="email" class="ast-form-label required">Email liên lạc</label>
                        <input type="email" id="email" name="email" class="ast-form-input" 
                               placeholder="email@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>

                    <div class="ast-form-group">
                        <label for="password" class="ast-form-label required">Mật khẩu</label>
                        <div class="ast-password-group">
                            <input type="password" id="password" name="password" class="ast-form-input" required>
                            <button type="button" id="togglePass1" class="ast-toggle-btn" 
                                    onclick="togglePassword('password', 'togglePass1')">👁️</button>
                        </div>
                    </div>

                    <div class="ast-form-group">
                        <label for="confirm_password" class="ast-form-label required">Nhập lại mật khẩu</label>
                        <div class="ast-password-group">
                            <input type="password" id="confirm_password" name="confirm_password" class="ast-form-input" required>
                            <button type="button" id="togglePass2" class="ast-toggle-btn" 
                                    onclick="togglePassword('confirm_password', 'togglePass2')">👁️</button>
                        </div>
                    </div>

                    <div class="ast-form-group">
                        <label for="message" class="ast-form-label required">Lời nhắn gửi Admin</label>
                        <textarea id="message" name="message" class="ast-form-input ast-form-textarea" 
                                  rows="4" placeholder="Tôi mong được tham gia hệ thống..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    </div>

                    <div class="ast-notice-box">
                        <div class="ast-notice-content">
                            <div class="ast-notice-icon">🕓</div>
                            <div class="ast-notice-text">
                                <strong>Admin sẽ sớm duyệt đơn của bạn.</strong><br>
                                Thời gian xử lý thường từ 1-2 ngày làm việc.
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="ast-submit-btn">Gửi đăng ký</button>
                </form>
            </div>

            <div class="ast-form-footer">
                <p class="ast-footer-text">
                    Đã có tài khoản? 
                    <a href="/login.php" class="ast-footer-link">Đăng nhập ngay</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id, btnId) {
            var input = document.getElementById(id);
            var btn = document.getElementById(btnId);
            if (input.type === "password") {
                input.type = "text";
                btn.innerHTML = "🙈";
                btn.setAttribute('aria-label', 'Ẩn mật khẩu');
            } else {
                input.type = "password";
                btn.innerHTML = "👁️";
                btn.setAttribute('aria-label', 'Hiện mật khẩu');
            }
        }

        // Real-time password validation
        document.addEventListener('DOMContentLoaded', function() {
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            
            function validatePasswords() {
                if (password.value && confirmPassword.value) {
                    if (password.value === confirmPassword.value) {
                        confirmPassword.classList.add('ast-is-valid');
                        confirmPassword.classList.remove('ast-is-invalid');
                    } else {
                        confirmPassword.classList.add('ast-is-invalid');
                        confirmPassword.classList.remove('ast-is-valid');
                    }
                }
            }
            
            password.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
        });
    </script>
</body>
</html>