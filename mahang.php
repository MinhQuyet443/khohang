<?php
include'connect.php';
if (isset($_POST['add'])) {
    $mavach = $_POST['mavach'];
    $tenhang = $_POST['tenhang'];
    $soluong = $_POST['soluong'];
    $giatien = $_POST['giatien'];
    $conn->query("INSERT INTO mahang (mavach, tenhang, soluong, giatien) VALUES ('$mavach', '$tenhang', $soluong, $giatien)");
}

// Xóa mã hàng
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $conn->query("DELETE FROM mahang WHERE id = $id");
}

// Cập nhật giá tiền
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $giatien = $_POST['giatien'];
    $conn->query("UPDATE mahang SET giatien = $giatien WHERE id = $id");
}

// Lấy danh sách mã hàng
$result = $conn->query("SELECT * FROM mahang");

// Kiểm tra nếu có yêu cầu chỉnh sửa
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Mã Hàng</title>
</head>
<body>
    <h1>Quản lý Mã Hàng</h1>
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

    <!-- Thêm mã hàng -->
    <form method="POST">
        <h3>Thêm mã hàng</h3>
        <input type="text" name="mavach" placeholder="Mã vạch" required>
        <input type="text" name="tenhang" placeholder="Tên hàng" required>
        <input type="number" name="soluong" placeholder="Số lượng" required>
        <input type="number" name="giatien" placeholder="Giá tiền" required>
        <button type="submit" name="add">Thêm</button>
    </form>

    <!-- Danh sách mã hàng -->
    <h3>Danh sách mã hàng</h3>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Mã vạch</th>
            <th>Tên hàng</th>
            <th>Số lượng</th>
            <th>Giá tiền</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['mavach']; ?></td>
            <td><?php echo $row['tenhang']; ?></td>
            <td><?php echo $row['soluong']; ?></td>
            <td><?php echo $row['giatien']; ?></td>
            <td>
                <!-- Nút xóa -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete">Xóa</button>
                </form>
                <!-- Nút chỉnh sửa -->
                <a href="?edit=<?php echo $row['id']; ?>">Chỉnh sửa</a>
            </td>
        </tr>
        <?php if ($edit_id == $row['id']): ?>
        <!-- Hiển thị form chỉnh sửa giá tiền -->
        <tr>
            <td colspan="6">
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    Giá mới: <input type="number" name="giatien" placeholder="Nhập giá mới" required>
                    <button type="submit" name="update">Cập nhật</button>
                </form>
            </td>
        </tr>
        <?php endif; ?>
        <?php endwhile; ?>
    </table>
</body>
</html>