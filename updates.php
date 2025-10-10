<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["username"])) {
    die("⛔ Bạn chưa đăng nhập.");
}

$username = strtolower($_SESSION["username"]);
$user_file = __DIR__ . "/users/user.xml";
$info_file = __DIR__ . "/users/user_informations.xml";

// Kiểm tra tồn tại file
if (!file_exists($user_file)) die("❌ Không tìm thấy user.xml.");
if (!file_exists($info_file)) file_put_contents($info_file, "<users></users>");

$users_data = simplexml_load_file($user_file);
$info_data = simplexml_load_file($info_file);
$target_user = null;

// Kiểm tra trạng thái cập nhật trong user.xml
foreach ($users_data->user as $user) {
    if ((string)$user->username === $username) {
        if ((string)$user->imformation_updates === "yes") {
            header("Location: index.php");
            exit();
        }
        $target_user = $user;
        break;
    }
}

if (!$target_user) die("❌ Không tìm thấy thông tin người dùng.");

// Xử lý khi submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $dob = trim($_POST["dob"]);
    $email = trim($_POST["email"]);

    // Tìm user trong user_informations.xml
    $found = false;
    foreach ($info_data->user as $info_user) {
        if ((string)$info_user->username === $username) {
            $info_user->name = $name;
            $info_user->date_of_birth = $dob;
            $info_user->email = $email;
            $found = true;
            break;
        }
    }

    // Nếu không tìm thấy thì thêm mới (dự phòng)
    if (!$found) {
        $new_info = $info_data->addChild("user");
        $new_info->addChild("username", $username);
        $new_info->addChild("name", $name);
        $new_info->addChild("date_of_birth", $dob);
        $new_info->addChild("email", $email);
    }

    // Lưu lại file
    $info_data->asXML($info_file);

    // Cập nhật trạng thái trong user.xml
    $target_user->imformation_updates = "yes";
    $users_data->asXML($user_file);

    // Chuyển hướng
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật thông tin</title>
    <link rel="icon" type="image/x-icon" href="/assets/Empty.ico">
    <link rel="stylesheet" href="assets/css/updates.css">
	<script src="assets/js/update.js"></script>
</head>
<body>
    <div class="container">
        <div class="background-animation">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
        
        <div class="form-container">
            <div class="form-header">
                <div class="icon-container">
                    <svg class="form-icon" viewBox="0 0 24 24" width="48" height="48">
                        <path fill="currentColor" d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" />
                    </svg>
                </div>
                <h1>Cập nhật thông tin</h1>
                <p class="subtitle">Vui lòng cung cấp thông tin cá nhân của bạn</p>
            </div>
            
            <form method="post" class="update-form" id="updateForm">
                <div class="form-group">
                    <label for="name" class="form-label">
                        <span class="label-text">Họ và tên</span>
                        <span class="required">*</span>
                    </label>
                    <div class="input-container">
                        <input type="text" name="name" id="name" class="form-input" required>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="error-message" id="nameError"></div>
                </div>
                
                <div class="form-group">
                    <label for="dob" class="form-label">
                        <span class="label-text">Ngày sinh</span>
                        <span class="required">*</span>
                    </label>
                    <div class="input-container">
                        <input type="date" name="dob" id="dob" class="form-input" required>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M9,10H7V12H9V10M13,10H11V12H13V10M17,10H15V12H17V10M19,3H18V1H16V3H8V1H6V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M19,19H5V8H19V19Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="error-message" id="dobError"></div>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <span class="label-text">Email</span>
                        <span class="required">*</span>
                    </label>
                    <div class="input-container">
                        <input type="email" name="email" id="email" class="form-input" required>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" />
                            </svg>
                        </div>
                    </div>
                    <div class="error-message" id="emailError"></div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <span class="btn-text">Cập nhật</span>
                        <div class="btn-loader">
                            <div class="loader-spinner"></div>
                        </div>
                    </button>
                </div>
            </form>
            
            <div class="form-footer">
                <div class="info-note">
                    <svg class="info-icon" viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M13,9H11V7H13M13,17H11V11H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z" />
                    </svg>
                    <span>Thông tin này sẽ được gửi cho admin để xác minh tài khoản và đăng ký chỗ ngồi.</span>
                </div>
            </div>
        </div>
        
        <div class="notification" id="notification">
            <div class="notification-content">
                <div class="notification-icon"></div>
                <div class="notification-message"></div>
            </div>
        </div>
    </div>
    
    
</body>
</html>
