<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"]);
    $dob = trim($_POST["dob"]);
    $username = strtolower(trim($_POST["username"]));
    $regret = trim($_POST["regret"]);

    $user_file = __DIR__ . "/users/user.xml";
    $info_file = __DIR__ . "/users/user_informations.xml";

    if (!file_exists($user_file) || !file_exists($info_file)) {
        $error = "❌ Không tìm thấy dữ liệu hệ thống.";
    } else {
        $users_data = simplexml_load_file($user_file);
        $info_data = simplexml_load_file($info_file);

        $userNode = null;
        $infoNode = null;

        // Tìm user
        foreach ($users_data->user as $u) {
            if ((string)$u->username === $username) {
                $userNode = $u;
                break;
            }
        }

        // Tìm thông tin cá nhân
        foreach ($info_data->user as $info) {
            if ((string)$info->username === $username) {
                $infoNode = $info;
                break;
            }
        }

        if (!$userNode || !$infoNode) {
            $error = "❗ Không tìm thấy tài khoản hoặc thông tin cá nhân.";
        } elseif ((string)$userNode->banned !== "yes") {
            $error = "✅ Tài khoản này không bị ban.";
        } else {
            // So sánh thông tin
            $matchInfo = (
                strtolower($fullname) === strtolower((string)$infoNode->name) &&
                $dob === (string)$infoNode->date_of_birth
            );

            if (!$matchInfo) {
                $error = "⚠️ Thông tin cá nhân không đúng. Bạn đang cố tình giả mạo?";
            } else {
                // Kiểm tra độ thành khẩn
                $regretLower = strtolower($regret);
                $honestWords = ['xin lỗi', 'hứa', 'không tái phạm', 'nhận lỗi', 'rút kinh nghiệm'];
                $isHonest = false;
                foreach ($honestWords as $word) {
                    if (strpos($regretLower, $word) !== false) {
                        $isHonest = true;
                        break;
                    }
                }

                if (!$isHonest) {
                    $error = "⚠️ Lời hối cải chưa đủ chân thành. Không đủ điều kiện gỡ ban!";
                } else {
                    $userNode->banned = "no";
                    $users_data->asXML($user_file);
                    $success = "✅ Bạn đã được gỡ ban nhờ lời hối cải chân thành!";
                }
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
    <title>Đơn xin gỡ ban</title>
    <link rel="stylesheet" href="/assets/css/ub.css">
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
</head>
<body>
       <div class="unban-box animate-slideInUp">
        <h2>Đơn xin gỡ ban</h2>

        <?php if (!empty($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
        <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <form method="post" id="unbanForm">
            <div class="form-group">
                <label for="fullname">Họ và tên:</label>
                <input type="text" id="fullname" name="fullname" placeholder="Nguyễn Văn A" required class="w-full">
            </div>

            <div class="form-group">
                <label for="dob">Ngày tháng năm sinh:</label>
                <input type="date" id="dob" name="dob" required class="w-full">
            </div>

            <div class="form-group">
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" placeholder="vd: admin" required class="w-full">
            </div>

            <div class="form-group">
                <label for="regret">Lời hối cải:</label>
                <div class="relative">
                    <textarea id="regret" name="regret" rows="5" placeholder="Viết lời hối cải chân thành tại đây..." required class="w-full" oninput="updateRegretCounter(this)"></textarea>
                    <div id="regretCounter" class="regret-counter">0 từ khóa thành khẩn</div>
                </div>
                <div class="honesty-meter">
                    <div id="honestyLevel" class="honesty-level" style="width: 0%"></div>
                </div>
            </div>

            <div class="notice notice-purple">
                ⚖️️ <strong>Admin sẽ không cần mật khẩu tài khoản của bạn.</strong><br>
                ❤️ Nếu hối cải tốt, bạn sẽ sớm được gỡ ban!<br>
                💡 Gợi ý: Sử dụng các từ như "xin lỗi", "hứa", "không tái phạm", "nhận lỗi", "rút kinh nghiệm"
            </div>

            <button type="submit" class="btn btn-purple w-full btn-lg">
                <span class="icon">🙏</span>
                Gửi đơn xin gỡ ban
            </button>
        </form>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update toggle icon
            const toggleIcon = document.querySelector('.theme-toggle .icon');
            toggleIcon.textContent = newTheme === 'dark' ? '🌙' : '☀️';
        }

        // Auto detect system theme
        if (localStorage.getItem('theme') === 'dark' || 
            (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.querySelector('.theme-toggle .icon').textContent = '🌙';
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            document.querySelector('.theme-toggle .icon').textContent = '☀️';
        }

        function updateRegretCounter(textarea) {
            const text = textarea.value.toLowerCase();
            const honestWords = ['xin lỗi', 'hứa', 'không tái phạm', 'nhận lỗi', 'rút kinh nghiệm', 'thành khẩn', 'sửa chữa', 'cam kết'];
            let foundCount = 0;
            
            honestWords.forEach(word => {
                if (text.includes(word)) {
                    foundCount++;
                }
            });
            
            const counter = document.getElementById('regretCounter');
            const honestyLevel = document.getElementById('honestyLevel');
            const percentage = Math.min((foundCount / honestWords.length) * 100, 100);
            
            counter.textContent = `${foundCount} từ khóa thành khẩn`;
            honestyLevel.style.width = `${percentage}%`;
            
            // Update counter style based on count
            counter.className = 'regret-counter';
            if (foundCount >= 3) {
                counter.classList.add('success');
            } else if (foundCount >= 1) {
                counter.classList.add('warning');
            }
        }

        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
        });
    </script>
</body>
</html>