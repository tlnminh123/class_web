<?php
http_response_code(403); // ép server trả về mã 403
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>403 - Truy cập bị từ chối</title>
<meta http-equiv="refresh" content="5;url=/index.php"> <!-- Tự động về trang chủ sau 5s -->
<style>
  body {
    font-family: Arial, sans-serif;
    background: #fff0f0;
    color: #333;
    text-align: center;
    padding: 50px;
  }
  h1 {
    font-size: 60px;
    margin-bottom: 10px;
    color: #c0392b;
  }
  p {
    font-size: 18px;
  }
  a {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #2980b9;
    font-weight: bold;
  }
  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <h1>403</h1>
  <p>🚫 Bạn không có quyền truy cập vào trang này.</p>
  <p>Đang chuyển hướng bạn về <a href="/index.php">trang chủ</a> trong 5 giây...</p>
</body>
</html>
