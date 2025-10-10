<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"]);
    $dob = trim($_POST["dob"]);
    $username = strtolower(trim($_POST["username"]));
    $old_password_hint = trim($_POST["old_password_hint"]);
    $admin_message = trim($_POST["admin_message"]);

    $user_file = __DIR__ . "/users/user.xml";
    $info_file = __DIR__ . "/users/user_informations.xml";

    if (!file_exists($user_file) || !file_exists($info_file)) {
        $error = "❌ Không tìm thấy dữ liệu người dùng.";
    } else {
        $usersXml = simplexml_load_file($user_file);
        $infosXml = simplexml_load_file($info_file);

        $userMatch = null;
        $infoMatch = null;

        // Tìm tài khoản
        foreach ($usersXml->user as $user) {
            if ((string)$user->username === $username) {
                $userMatch = $user;
                break;
            }
        }

        // Tìm thông tin tài khoản
        foreach ($infosXml->user as $info) {
            if ((string)$info->username === $username) {
                $infoMatch = $info;
                break;
            }
        }

        if (!$userMatch || !$infoMatch) {
            $error = "❗ Không tìm thấy tài khoản hoặc thông tin tương ứng.";
        } else {
            $correctPassword = (
                $old_password_hint === (string)$userMatch->password ||
                $old_password_hint === (string)$userMatch->old_password
            );

            $correctInfo = (
                strtolower($fullname) === strtolower((string)$infoMatch->name) &&
                $dob === (string)$infoMatch->date_of_birth
            );

            if ($correctPassword && $correctInfo) {
                $userMatch->password = 'a';
                $usersXml->asXML($user_file);
                $success = "✅ Chứng minh thành công! Mật khẩu đã được đặt lại về 'a'.";
            } else {
                // Nếu sai → lưu đơn
                $save_dir = __DIR__ . "/form/repass";
                if (!is_dir($save_dir)) mkdir($save_dir, 0777, true);

                $filename = $save_dir . "/repass_" . $username . "_" . time() . ".txt";
                $content  = "Họ và tên: $fullname\n";
                $content .= "Ngày sinh: $dob\n";
                $content .= "Tên tài khoản: $username\n";
                $content .= "Mật khẩu nhớ gần đây: $old_password_hint\n";
                $content .= "Lời nhắn gửi Admin:\n$admin_message\n";

                file_put_contents($filename, $content);
                $success = "📩 Yêu cầu đã được gửi, admin sẽ duyệt sớm!";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="vi" data-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu đổi mật khẩu</title>
    <link rel="stylesheet" href="/assets/css/rp.css">
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
</head>
<body>
 

    <div class="reset-box animate-slideInUp">
        <h2>Yêu cầu đổi mật khẩu</h2>

        <?php if (!empty($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <form method="post">
            <div class="form-group">
                <label for="fullname">Họ và tên:</label>
                <input type="text" id="fullname" name="fullname" placeholder="VD: Nguyễn Văn A" required class="w-full">
            </div>

            <div class="form-group">
                <label for="dob">Ngày tháng năm sinh:</label>
                <input type="date" id="dob" name="dob" required class="w-full">
            </div>

            <div class="form-group">
                <label for="username">Tên tài khoản:</label>
                <input type="text" id="username" name="username" placeholder="VD: admin" required class="w-full">
            </div>

            <div class="form-group">
                <label for="old_password_hint">Mật khẩu nhớ gần đây:</label>
                <input type="text" id="old_password_hint" name="old_password_hint" placeholder="abc123 hoặc đại loại..." required class="w-full">
            </div>

            <div class="form-group">
                <label for="admin_message">Lời nhắn gửi Admin:</label>
                <textarea id="admin_message" name="admin_message" rows="4" placeholder="Viết lời nhắn gửi admin tại đây..." required class="w-full"></textarea>
            </div>

            <div class="notice notice-warning">
                ⚠️ <strong>Admin sẽ sớm duyệt đơn của bạn.</strong><br>
                🔁 Nếu được duyệt, mật khẩu sẽ được đặt lại về mặc định là <strong>'a'</strong>.<br>
                🚫 Nếu không được duyệt, bạn nên <strong>đăng ký tài khoản mới</strong>.
            </div>

            <button type="submit" class="btn btn-primary w-full">Gửi yêu cầu</button>
        </form>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }

        // Auto detect system theme
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>
</body>
</html>