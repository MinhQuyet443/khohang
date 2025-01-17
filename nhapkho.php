<?php
include 'connect.php';

$tenhang = "";

if (isset($_POST['check_mavach'])) {
    $mavach = $_POST['mavach'];
    $result = $conn->query("SELECT tenhang FROM mahang WHERE mavach = '$mavach'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tenhang = $row['tenhang']; 
    } else {
        $tenhang = "Không tìm thấy";
    }
}

if (isset($_POST['nhapkho'])) {
    $mavach = $_POST['mavach'];
    $soluong = $_POST['soluong'];
    $ngaynhap = $_POST['ngaynhap'];
    
   
    $result = $conn->query("SELECT soluong FROM mahang WHERE mavach = '$mavach'");
    if ($result->num_rows > 0) {
   
        $row = $result->fetch_assoc();
        $soluong_kho = $row['soluong'];
        $soluong_kho += $soluong;

       
        $sql_update = "UPDATE mahang SET soluong = $soluong_kho WHERE mavach = '$mavach'";
        $conn->query($sql_update);

        $sql_nhap = "INSERT INTO nhapkho (mavach, tenhang, soluong, ngaynhap) VALUES ('$mavach', '$tenhang', $soluong, '$ngaynhap')";
        $conn->query($sql_nhap);

        echo "Nhập kho thành công! Số lượng đã được cập nhật.";
    } else {
        echo "Lỗi: Mã vạch không tồn tại trong kho.";
    }
}

$nhapkho_list = $conn->query("
    SELECT nhapkho.id, nhapkho.mavach, nhapkho.tenhang, nhapkho.soluong, nhapkho.ngaynhap
    FROM nhapkho
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Nhập Kho</title>
</head>
<body>
<header>
      
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

    <h1>Quản lý Nhập Kho</h1>

    <form method="POST">
        <h3>Kiểm tra Mã Vạch</h3>
        <label>Mã vạch:</label>
        <input type="text" name="mavach" value="<?php echo isset($_POST['mavach']) ? $_POST['mavach'] : ''; ?>" required>
        <button type="submit" name="check_mavach">Kiểm tra</button>
    </form>
    <br>
    <label>Tên hàng:</label>
    <input type="text" value="<?php echo $tenhang; ?>" readonly>
    <br><br>

    <form method="POST">
        <h3>Nhập Kho</h3>
        <input type="hidden" name="mavach" value="<?php echo isset($_POST['mavach']) ? $_POST['mavach'] : ''; ?>">
        
        <label>Số lượng:</label>
        <input type="number" name="soluong" required>
        <br>
        <label>Ngày nhập:</label>
        <input type="date" name="ngaynhap" required>
        <br><br>

        <button type="submit" name="nhapkho">Nhập kho</button>
    </form>

    <h3>Danh sách Nhập Kho</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Mã vạch</th>
            <th>Tên hàng</th>
            <th>Số lượng</th>
            <th>Ngày nhập</th>
        </tr>
        <?php while ($row = $nhapkho_list->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['mavach']; ?></td>
                <td><?php echo $row['tenhang']; ?></td>
                <td><?php echo $row['soluong']; ?></td>
                <td><?php echo $row['ngaynhap']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
