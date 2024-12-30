<?php
// Bắt đầu phiên làm việc
session_start();

// Hủy tất cả các biến phiên
session_unset();

// Hủy phiên làm việc
session_destroy();

// Chuyển hướng người dùng đến trang đăng nhập
header('Location: login.php');
exit;
?>
