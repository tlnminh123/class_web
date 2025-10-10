<?php
http_response_code(404); // ép server trả về mã 404
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>404 - Không tìm thấy trang</title>
<meta http-equiv="refresh" content="5;url=/index.php"> 
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    color: #333;
    text-align: center;
    padding: 50px;
  }
  h1 {
    font-size: 60px;
    margin-bottom: 10px;
    color: #e74c3c;
  }
  p {
    font-size: 18px;
  }
  a {
    display: inline-block;
    margin-top: 20px;
    text-decoration: none;
    color: #3498db;
    font-weight: bold;
  }
  a:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
  <h1>404</h1>
  <p>❌ Trang không tồn tại.</p>
  <p>Đang chuyển hướng bạn về <a href="/index.php">trang chủ</a> trong 5 giây...</p>
</body>
</html>
