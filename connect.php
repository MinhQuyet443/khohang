<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kho_hang";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}


?>


