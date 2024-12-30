<?php

session_start();


if (!isset($_SESSION['mySession'])) {
    header('Location: login.php'); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Trang chính</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <header>
        <h1>Hệ thống Quản lý Kho</h1>
      
        <a href="logout.php">Đăng xuất</a>
    </header>

    <nav>
        <ul>
            <li><a href="mahang.php">Quản lý mã hàng</a></li>
            <li><a href="nhapkho.php">Nhập kho</a></li>
            <li><a href="xuatkho.php">Xuất kho</a></li>
            <li><a href="chenhlech.php">Chênh lệch</a></li>
            <li><a href="banhang.php">Bán hàng</a></li>
            <li><a href="baocao.php">Báo Cáo</a></li>
        </ul>
    </nav>

    <footer>
        <p>© 2024 - Quản lý kho hàng</p>
    </footer>
</body>
</html>
