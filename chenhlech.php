<?php
include 'connect.php';
$tenhang = ""; // Tên hàng mặc định
$giatien = 0;  // Giá bán mặc định
$soluonghethong = 0; // Số lượng hệ thống mặc định

// Kiểm tra nếu mã vạch được nhập và gửi qua biểu mẫu
if (isset($_POST['checkmavach'])) {
    $mavach = $_POST['mavach'];
    $result = $conn->query("SELECT tenhang, giatien, soluong FROM mahang WHERE mavach = '$mavach'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tenhang = $row['tenhang']; // Lấy tên hàng từ cơ sở dữ liệu
        $giatien = $row['giatien']; // Lấy giá bán từ cơ sở dữ liệu
        $soluonghethong = $row['soluong']; // Lấy số lượng hệ thống
    } else {
        $tenhang = "Không tìm thấy"; // Thông báo nếu mã vạch không tồn tại
    }
}

// Cập nhật hàng âm và tiền âm
if (isset($_POST['capnhatchenhlech'])) {
    $mavach = $_POST['mavach'];
    $soluongthucte = $_POST['soluongthucte']; // Số lượng thực tế nhập từ người dùng

    // Lấy dữ liệu từ cơ sở dữ liệu về số lượng hệ thống và giá tiền
    $result = $conn->query("SELECT tenhang, giatien, soluong FROM mahang WHERE mavach = '$mavach'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tenhang = $row['tenhang']; // Lấy tên hàng
        $giatien = $row['giatien']; // Lấy giá tiền
        $soluonghethong = $row['soluong']; // Lấy số lượng hệ thống
    } else {
        $tenhang = "Không tìm thấy";
        echo "Mã vạch không tồn tại trong cơ sở dữ liệu. <br>";
        exit; // Dừng chương trình nếu không tìm thấy mã vạch
    }

    // Tính toán hàng âm và tiền âm
    $hangam = $soluongthucte - $soluonghethong; // Hàng âm
    $tienam = $hangam * $giatien; // Tiền âm

    // Chèn vào bảng chenh_lech
    $sqlchenhlech = "INSERT INTO chenh_lech (mavach, soluongthucte, soluonghethong, hangam, tienam) 
                       VALUES ('$mavach', $soluongthucte, $soluonghethong, $hangam, $tienam)";
    if ($conn->query($sqlchenhlech) === TRUE) {
        echo "Cập nhật hàng âm và tiền âm thành công!<br>";
    } else {
        echo "Lỗi: " . $conn->error . "<br>";
    }
}

// Lấy danh sách hàng âm và tiền âm
$chenhlechlist = $conn->query("
    SELECT chenh_lech.id, mahang.tenhang, chenh_lech.mavach, chenh_lech.soluongthucte, chenh_lech.soluonghethong, chenh_lech.hangam, chenh_lech.tienam, mahang.giatien
    FROM chenh_lech
    JOIN mahang ON chenh_lech.mavach = mahang.mavach
");

// Tính tổng tiền âm
$totaltienamresult = $conn->query("SELECT SUM(tienam) AS totaltienam FROM chenh_lech");
$totaltienamrow = $totaltienamresult->fetch_assoc();
$totaltienam = $totaltienamrow['totaltienam'] ? $totaltienamrow['totaltienam'] : 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Hàng Âm và Tiền Âm</title>
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

    <h1>Quản lý Hàng Âm và Tiền Âm</h1>

    <!-- Form nhập mã vạch -->
    <form method="POST">
        <h3>Kiểm tra Mã Vạch</h3>
        <label>Mã vạch:</label>
        <input type="text" name="mavach" value="<?php echo isset($_POST['mavach']) ? $_POST['mavach'] : ''; ?>" required>
        <button type="submit" name="checkmavach">Kiểm tra</button>
    </form>
    <br>
    <label>Tên hàng:</label><input type="text" value="<?php echo $tenhang; ?>" readonly>
    <br><br>
    <label>Số lượng hệ thống:</label><input type="text" value="<?php echo isset($soluonghethong) ? $soluonghethong : ''; ?>" readonly>
    <br><br>

    <!-- Form nhập hàng âm và tiền âm -->
    <form method="POST">
    <h3>Cập Nhật Hàng Âm và Tiền Âm</h3>
        <input type="hidden" name="mavach" value="<?php echo isset($_POST['mavach']) ? $_POST['mavach'] : ''; ?>">
        
        <label>Số lượng thực tế:</label>
        <input type="number" name="soluongthucte" required>
        <br><br>
        <button type="submit" name="capnhatchenhlech">Cập nhật hàng âm và tiền âm</button>
    </form>

    <h3>Danh sách Hàng Âm và Tiền Âm</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Tên hàng</th>
            <th>Mã vạch</th>
            <th>Số lượng thực tế</th>
            <th>Số lượng hệ thống</th>
            <th>Giá tiền</th>
            <th>Hàng âm</th>
            <th>Tiền âm</th>
        </tr>
        <?php while ($row = $chenhlechlist->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['tenhang']; ?></td>
                <td><?php echo $row['mavach']; ?></td>
                <td><?php echo $row['soluongthucte']; ?></td>
                <td><?php echo $row['soluonghethong']; ?></td>
                <td><?php echo number_format($row['giatien'], 2); ?> VNĐ</td>
                <td><?php echo $row['hangam']; ?></td>
                <td><?php echo number_format($row['tienam'], 2); ?> VNĐ</td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Hiển thị tổng tiền âm -->
    <h3>Tổng Tiền Âm: <?php echo number_format($totaltienam, 2); ?> VNĐ</h3>
</body>
</html>
<?php