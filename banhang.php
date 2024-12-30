<?php
session_start();
include 'connect.php';  // Kết nối cơ sở dữ liệu

// Kiểm tra giỏ hàng đã tồn tại trong session chưa
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];  // Khởi tạo giỏ hàng nếu chưa tồn tại
}

$tongtien = 0;  // Tổng tiền giỏ hàng

// Kiểm tra mã vạch sản phẩm
if (isset($_POST['check_item']) && isset($_POST['mavach']) && !empty($_POST['mavach'])) {
    $mavach = $_POST['mavach'];
    $result = $conn->query("SELECT id, tenhang, giatien, soluong FROM mahang WHERE mavach = '$mavach'");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $tenhang = $row['tenhang'];
        $giatien = $row['giatien'];
        $soluonghethong = $row['soluong'];
    } else {
        echo "Mã vạch không tồn tại trong kho!<br>";
        $tenhang = $giatien = $soluonghethong = "";  
    }
}

// Thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart']) && isset($_POST['mavach']) && isset($_POST['soluongban'])) {
    $mavach = $_POST['mavach'];
    $soluongban = $_POST['soluongban'];

    $result = $conn->query("SELECT id, tenhang, giatien, soluong FROM mahang WHERE mavach = '$mavach'");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id = $row['id'];
        $tenhang = $row['tenhang'];
        $giatien = $row['giatien'];
        $soluonghethong = $row['soluong'];

        // Kiểm tra số lượng bán
        if ($soluongban <= $soluonghethong) {
            $tongtienmotsanpham = $soluongban * $giatien;

            $product = [
                'id' => $id,
                'mavach' => $mavach,
                'tenhang' => $tenhang,
                'giatien' => $giatien,
                'soluongban' => $soluongban,
                'tongtien' => $tongtienmotsanpham
            ];

            // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['mavach'] === $mavach) {
                    $item['soluongban'] += $soluongban;
                    $item['tongtien'] = $item['soluongban'] * $item['giatien'];
                    $found = true;
                    break;
                }
            }

            // Nếu chưa có, thêm mới vào giỏ hàng
            if (!$found) {
                $_SESSION['cart'][] = $product;
            }

            // Tính tổng tiền giỏ hàng
            $tongtien = 0;
            foreach ($_SESSION['cart'] as $item) {
                if (isset($item['tongtien'])) {
                    $tongtien += $item['tongtien'];
                }
            }
        } else {
            echo "Số lượng bán không thể lớn hơn số lượng trong kho!<br>";
        }
    } else {
        echo "Mã vạch không tồn tại trong kho!<br>";
    }
}

// Xử lý bán hàng khi người dùng gửi thông tin
if (isset($_POST['ban_hang'])) {
    $isvalid = true;
    foreach ($_SESSION['cart'] as $item) {
        $mavach = $item['mavach'];
        $result = $conn->query("SELECT mavach FROM mahang WHERE mavach = '$mavach'");
        if ($result->num_rows === 0) {
            echo "Mã vạch {$mavach} không tồn tại trong kho!<br>";
            $isvalid = false;
            break;
        }
    }

    if ($isvalid) {
        // Thực hiện thêm giao dịch vào bảng `ban_hang`
        foreach ($_SESSION['cart'] as $item) {
            $mavach = $item['mavach'];
            $tenhang = $item['tenhang'];
            $soluongban = $item['soluongban'];
            $giatien = $item['giatien'];
            $tongtien = $item['tongtien'];
            $phuongthuc = $_POST['phuongthuc'];  // Phương thức thanh toán

            // Thêm giao dịch vào bảng `ban_hang`
            $sqlbanhang = "INSERT INTO ban_hang (mavach, tenhang, soluongban, giatien, tongtien, phuongthucthanhtoan)
                           VALUES ('$mavach', '$tenhang', $soluongban, $giatien, $tongtien, '$phuongthuc')";
            if ($conn->query($sqlbanhang) === TRUE) {
                // Cập nhật lại số lượng kho
                $sqlupdatesoluong = "UPDATE mahang SET soluong = soluong - $soluongban WHERE mavach = '$mavach'";
                $conn->query($sqlupdatesoluong);
            } else {
                echo "Lỗi khi thêm giao dịch bán hàng: " . $conn->error . "<br>";
            }
        }

        echo "Giao dịch bán hàng đã được xử lý thành công!";

        // Xóa giỏ hàng sau khi bán
        $_SESSION['cart'] = [];
    }
}

// Xử lý phương thức thanh toán
if (isset($_POST['thanh_toan'])) {
    $phuongthuc = mysqli_real_escape_string($conn, $_POST['phuongthuc']);  // Escape để tránh lỗi SQL
    $tongtien = $_POST['tongtien'];  // Tổng tiền giao dịch

    if ($tongtien > 0) {
        // Thêm thông tin thanh toán vào cơ sở dữ liệu
        $sqlthanhtoan = "INSERT INTO thanh_toan (phuongthuc, tongtien) 
                           VALUES ('$phuongthuc', $tongtien)";

        if ($conn->query($sqlthanhtoan) === TRUE) {
            echo "Thanh toán thành công qua $phuongthuc!<br>";
        } else {
            echo "Lỗi khi thanh toán: " . $conn->error . "<br>";
        }
    } else {
        echo "Tổng tiền không hợp lệ!<br>";
    }
}

// Xử lý nút làm mới giỏ hàng
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];  // Xóa giỏ hàng
    $tongtien = 0;  // Đặt lại tổng tiền
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Bán Hàng</title>
</head>
<body>
<header>
    <a href="logout.php">Đăng xuất</a>
</header>

<h1>Quản lý Bán Hàng</h1>

<!-- Form nhập mã vạch và kiểm tra -->
<form method="POST">
    <h3>Nhập mã vạch và kiểm tra</h3>
    <label>Mã vạch:</label>
    <input type="text" name="mavach" required>
    <button type="submit" name="check_item">Kiểm tra</button>
</form>

<br>

<?php if (!empty($tenhang)): ?>
    <h4>Thông tin mặt hàng</h4>
    <p>Tên hàng: <?php echo $tenhang; ?></p>
    <p>Giá tiền: <?php echo $giatien; ?></p>
    <p>Số lượng trong kho: <?php echo $soluonghethong; ?></p>
    <form method="POST">
        <label>Số lượng bán:</label>
        <input type="number" name="soluongban" max="<?php echo $soluonghethong; ?>" required>
        <button type="submit" name="add_to_cart">Thêm vào giỏ</button>
    </form>
<?php endif; ?>

<br>

<!-- Hiển thị giỏ hàng -->
<h3>Giỏ hàng</h3>
<table>
    <tr>
        <th>Mã vạch</th>
        <th>Tên hàng</th>
        <th>Số lượng</th>
        <th>Giá tiền</th>
        <th>Tổng tiền</th>
    </tr>
    <?php
    $tongtien = 0;
    foreach ($_SESSION['cart'] as $item):
        $tongtien += $item['tongtien'];
    ?>
        <tr>
            <td><?php echo $item['mavach']; ?></td>
            <td><?php echo $item['tenhang']; ?></td>
            <td><?php echo $item['soluongban']; ?></td>
            <td><?php echo $item['giatien']; ?></td>
            <td><?php echo $item['tongtien']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<p>Tổng tiền: <?php echo $tongtien; ?></p>

<!-- Thêm các nút xử lý -->
<form method="POST">
    <button type="submit" name="ban_hang">Bán hàng</button>
    <button type="submit" name="clear_cart">Làm mới giỏ hàng</button>
</form>

</body>
</html>
