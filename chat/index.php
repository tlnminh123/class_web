<?php
session_start();
date_default_timezone_set("Asia/Ho_Chi_Minh");

if (!isset($_SESSION["username"])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: /login.php");
    exit();
}

$username = $_SESSION['username'];

// ==== Load role từ user.xml ====
$userXmlPath = __DIR__ . "/../users/user.xml";
if (!file_exists($userXmlPath)) die("⚠️ Không tìm thấy user.xml!");
$userXml = simplexml_load_file($userXmlPath);

// Lấy quyền
function getUserRole($username, $xml) {
    foreach ($xml->user as $user) {
        if ((string)$user->username === $username) {
            return (string)$user->role;
        }
    }
    return 'no';
}
$role = getUserRole($username, $userXml);
$isAdmin1 = ($role === 'yes');
$isAdmin2 = ($role === 'role2');

// ==== Load danh sách theme ====
$themes = [];
foreach (glob(__DIR__ . "/assets/css/*.css") as $path) {
    $themes[] = basename($path, '.css');
}

// ==== Map tên theme đẹp ====
$themeNames = [
    'style'  => '🎨 Giao diện Mặc định',
    'style1' => '🌞 Giao diện Sáng',
    'style2' => '🌙 Giao diện Tối',
    'style3' => '🧓 Giao diện Cũ',
    'style4' => '💬 Giao diện LINE',
    'style5' => '🎧 Giao diện Discord',
    'style6' => '📸 Giao diện Instagram',
    'style7' => '🤖 Giao diện Copilot',
    'style8' => '🧠 Giao diện HAGO',
    'style9' => '🎨 Giao diện 9',
];

// ==== Load/ghi user_settings.xml ====
$themeXmlPath = __DIR__ . "/user_settings.xml";
if (!file_exists($themeXmlPath)) {
    file_put_contents($themeXmlPath, "<?xml version='1.0' encoding='UTF-8'?><settings></settings>");
}
$themeXml = simplexml_load_file($themeXmlPath);

// Lấy theme hiện tại
$selectedTheme = "style";
foreach ($themeXml->user as $u) {
    if ((string)$u['name'] === $username) {
        $selectedTheme = (string)$u->theme ?: "style";
    }
}

// Nếu đổi theme
if (isset($_GET['theme'])) {
    $newTheme = $_GET['theme'];
    if (in_array($newTheme, $themes)) {
        $found = false;
        foreach ($themeXml->user as $u) {
            if ((string)$u['name'] === $username) {
                $u->theme = $newTheme;
                $themeXml->asXML($themeXmlPath);
                $selectedTheme = $newTheme;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $newUser = $themeXml->addChild("user");
            $newUser->addAttribute("name", $username);
            $newUser->addChild("theme", $newTheme);
            $themeXml->asXML($themeXmlPath);
            $selectedTheme = $newTheme;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Phòng chat</title>
  <link rel="stylesheet" href="assets/css/<?= htmlspecialchars($selectedTheme) ?>.css">
  <link rel="icon" href="assets/Empty.ico">
  <script src="assets/jquery-3.6.0.min.js"></script>
  <script src="assets/script.js"></script>
  <style>
    .code-block {
      position: relative;
      margin-bottom: 10px;
    }
    .copy-btn {
      position: absolute;
      top: 5px;
      right: 5px;
      padding: 4px 8px;
      font-size: 12px;
      cursor: pointer;
      background-color: #eee;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .copy-btn:hover {
      background-color: #ddd;
    }

    /* Header fix layout */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 20px;
    }
    .header-right {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .user-dropdown {
      position: relative;
      cursor: pointer;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      top: 100%;
      background-color: white;
      border: 1px solid #ccc;
      z-index: 999;
      min-width: 150px;
    }
    .dropdown-content a {
      display: block;
      padding: 8px 12px;
      text-decoration: none;
      color: #333;
    }
    .dropdown-content a:hover {
      background-color: #f0f0f0;
    }
  </style>
</head>
<body>
<div class="header">
  <div class="header-left"></div>
  <div class="header-right">
    <form method="GET" style="margin: 0;">
      <select name="theme" onchange="this.form.submit()" style="padding: 5px;">
        <?php foreach ($themes as $theme): ?>
          <option value="<?= htmlspecialchars($theme) ?>" <?= $selectedTheme === $theme ? 'selected' : '' ?>>
            <?= $themeNames[$theme] ?? ucfirst($theme) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php if ($isAdmin1): ?>
      <a href="/admin.php" class="btn">🔧 Admin Panel</a>
    <?php elseif ($isAdmin2): ?>
      <a href="/dka2.php" class="btn">🔧 Đăng ký tài khoản</a>
    <?php endif; ?>

    <div class="user-dropdown" onclick="toggleDropdown()">
      <span class="username"><?= htmlspecialchars($username) ?> ▼</span>
      <div class="dropdown-content" id="userDropdown">
        <a href="/repass.php">🔒 Đổi mật khẩu</a>
        <a href="/logout.php">🚪 Đăng xuất</a>
        <a href="/game/index.php">🎮 Game</a>
        <a href="/test/index.php">⏳ Gửi file</a>
        <a href="/profile.php">📄 Hồ sơ</a>
      </div>
    </div>
  </div>
</div>

<div class="messages" id="messages">
  <!-- Tin nhắn sẽ được load bằng JS -->
</div>

<form class="message-form">
  <textarea name="message" rows="3" placeholder="Nhập tin nhắn..." required></textarea>
  <button type="submit">👉 Gửi</button>
</form>

<script>
function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  dropdown.style.display = (dropdown.style.display === "block") ? "none" : "block";
}
window.addEventListener("click", function(event) {
  const trigger = document.querySelector(".user-dropdown");
  const dropdown = document.getElementById("userDropdown");
  if (!trigger.contains(event.target)) {
    dropdown.style.display = "none";
  }
});
</script>
</body>
</html>
