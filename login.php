<?php

include 'connect.php'; 
session_start();

if (isset($_SESSION['mySession'])) {
    header('Location: index.php');
    exit;
}
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM tai_khoan WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) == 1) {

        $_SESSION['mySession'] = $username;
        
        header('Location: index.php');
        exit;
    } else {

        $error_message = "Tài khoản hoặc mật khẩu sai!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
</head>
<body>
    <h2>Đăng nhập vào hệ thống Quản lý Kho</h2>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="username">Tên tài khoản:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit" name="login">Đăng nhập</button>
    </form>
</body>
</html>
