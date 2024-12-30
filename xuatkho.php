<?php
include 'connect.php';

$tenhang = ""; 
$loaixuat = ""; 
$cuahang = ""; 

if (isset($_POST['checkmavach'])) {
    $mavach = $_POST['mavach'];
    $result = $conn->query("SELECT tenhang FROM mahang WHERE mavach = '$mavach'");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $tenhang = $row['tenhang'];
    } else {
        $tenhang = "Không tìm thấy"; 
    }
}


if (isset($_POST['xuatkho'])) {
    $mavach = $_POST['mavach'];
    $soluong = $_POST['soluong'];
    $ngayxuat = $_POST['ngayxuat'];
    $loaixuat = $_POST['loaixuat'];
    if ($loaixuat == 'chuyenhang') {
        $cuahang = $_POST['cuahang']; 
    }
    
  
    $result = $conn->query("SELECT soluong FROM mahang WHERE mavach = '$mavach'");
    $row = $result->fetch_assoc();
    $soluongkho = $row['soluong'];

    if ($soluongkho >= $soluong) {
        if ($loaixuat == 'huyhang') {
         
            $sql = "INSERT INTO xuatkho (mavach, soluong, ngayxuat, loaixuat) VALUES ('$mavach', $soluong, '$ngayxuat', 'Hủy')";
            $conn->query($sql);
            $conn->query("UPDATE mahang SET soluong = soluong - $soluong WHERE mavach = '$mavach'");
            echo "Hủy hàng thành công!";
        } elseif ($loaixuat == 'chuyenhang') {
         
            $sql = "INSERT INTO xuatkho (mavach, soluong, ngayxuat, loaixuat, cuahang) VALUES ('$mavach', $soluong, '$ngayxuat', 'Chuyển', '$cuahang')";
            $conn->query($sql);
            $conn->query("UPDATE mahang SET soluong = soluong - $soluong WHERE mavach = '$mavach'");
            echo "Chuyển hàng tới cửa hàng $cuahang thành công!";
        }
    } else {
        echo "Lỗi: Số lượng trong kho không đủ!";
    }
}


$xuatkholist = $conn->query("
    SELECT xuatkho.id, mahang.tenhang, xuatkho.mavach, xuatkho.soluong, xuatkho.ngayxuat, xuatkho.loaixuat, xuatkho.cuahang
    FROM xuatkho
    JOIN mahang ON xuatkho.mavach = mahang.mavach
");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Xuất Kho</title>
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
    <h1>Quản lý Xuất Kho</h1>


                <form method="POST">
        <h3>Kiểm tra Mã Vạch</h3>
        <label>Mã vạch:</label>
        <input type="text" name="mavach" value="<?php echo isset($_POST['mavach']) ? $_POST['mavach'] : ''; ?>" required>
        <button type="submit" name="checkmavach">Kiểm tra</button>
    </form>
    <br>
    <label>Tên hàng:</label><input type="text" value="<?php echo $tenhang; ?>" readonly>
    <br><br>

    <form method="POST">
    <h3>Xuất Kho</h3>
    <input type="hidden" name="mavach" value="<?php echo isset($_POST['mavach']) ? $_POST['mavach'] : ''; ?>">
    
    <label>Số lượng:</label>
    <input type="number" name="soluong" value="<?php echo isset($_POST['soluong']) ? $_POST['soluong'] : ''; ?>" required>
    <br>
    <label>Ngày xuất:</label>
    <input type="date" name="ngayxuat" value="<?php echo isset($_POST['ngayxuat']) ? $_POST['ngayxuat'] : ''; ?>" required>
    <br>
    
    <label>Loại xuất kho:</label>
    <select name="loaixuat" required onchange="this.form.submit()">
        <option value="">Chọn loại xuất kho</option>
        <option value="huyhang" <?php echo (isset($_POST['loaixuat']) && $_POST['loaixuat'] == 'huyhang') ? 'selected' : ''; ?>>Hủy hàng</option>
        <option value="chuyenhang" <?php echo (isset($_POST['loaixuat']) && $_POST['loaixuat'] == 'chuyenhang') ? 'selected' : ''; ?>>Chuyển đi cửa hàng khác</option>
    </select>
    <br><br>

    <?php if (isset($_POST['loaixuat']) && $_POST['loaixuat'] == 'chuyenhang'): ?>
        <label for="cuahang">Chọn cửa hàng chuyển tới:</label>
        <select name="cuahang" required>
            <option value="">Chọn cửa hàng</option>
            <option value="A" <?php echo (isset($_POST['cuahang']) && $_POST['cuahang'] == 'A') ? 'selected' : ''; ?>>Cửa hàng A</option>
            <option value="B" <?php echo (isset($_POST['cuahang']) && $_POST['cuahang'] == 'B') ? 'selected' : ''; ?>>Cửa hàng B</option>
            <option value="C" <?php echo (isset($_POST['cuahang']) && $_POST['cuahang'] == 'C') ? 'selected' : ''; ?>>Cửa hàng C</option>
            <option value="D" <?php echo (isset($_POST['cuahang']) && $_POST['cuahang'] == 'D') ? 'selected' : ''; ?>>Cửa hàng D</option>
        </select>
    <?php endif; ?>

    <?php if (isset($_POST['loaixuat']) && $_POST['loaixuat'] == 'huyhang'): ?>
        <br>
        <label for="lydohuy">Lý do hủy:</label>
        <input type="text" name="lydohuy" value="<?php echo isset($_POST['lydohuy']) ? $_POST['lydohuy'] : ''; ?>" required>
    <?php endif; ?>

    <br><br>
    <button type="submit" name="xuatkho">Xuất kho</button>
</form>

    <h3>Danh sách Xuất Kho</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Tên hàng</th>
            <th>Mã vạch</th>
            <th>Số lượng</th>
            <th>Ngày xuất</th>
            <th>Loại xuất kho</th>
            <th>Cửa hàng </th>
        </tr>
        <?php while ($row = $xuatkholist->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['tenhang']; ?></td>
                <td><?php echo $row['mavach']; ?></td>
                <td><?php echo $row['soluong']; ?></td>
                <td><?php echo $row['ngayxuat']; ?></td>
                <td><?php echo $row['loaixuat']; ?></td>
                <td><?php echo isset($row['cuahang']) ? $row['cuahang'] : ''; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
