<?php
session_start();
include 'connect.php';  
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];  
}

$tongtien = 0;  

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

            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['mavach'] === $mavach) {
                    $item['soluongban'] += $soluongban;
                    $item['tongtien'] = $item['soluongban'] * $item['giatien'];
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $_SESSION['cart'][] = $product;
            }

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
        foreach ($_SESSION['cart'] as $item) {
            $mavach = $item['mavach'];
            $tenhang = $item['tenhang'];
            $soluongban = $item['soluongban'];
            $giatien = $item['giatien'];
            $tongtien = $item['tongtien'];
            $phuongthuc = $_POST['phuongthuc'];  

            $sqlbanhang = "INSERT INTO ban_hang (mavach, tenhang, soluongban, giatien, tongtien, phuongthucthanhtoan)
                           VALUES ('$mavach', '$tenhang', $soluongban, $giatien, $tongtien, '$phuongthuc')";
            if ($conn->query($sqlbanhang) === TRUE) {
          
                $sqlupdatesoluong = "UPDATE mahang SET soluong = soluong - $soluongban WHERE mavach = '$mavach'";
                $conn->query($sqlupdatesoluong);
            } else {
                echo "Lỗi khi thêm giao dịch bán hàng: " . $conn->error . "<br>";
            }
        }

        echo "Giao dịch bán hàng đã được xử lý thành công!";

        $_SESSION['cart'] = [];
    }
}

if (isset($_POST['thanh_toan'])) {
    $phuongthuc = mysqli_real_escape_string($conn, $_POST['phuongthuc']);  
    $tongtien = $_POST['tongtien'];  

    if ($tongtien > 0) {
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

if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];  
    $tongtien = 0; 
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

<form method="POST">
    <button type="submit" name="ban_hang">Bán hàng</button>
    <button type="submit" name="clear_cart">Làm mới giỏ hàng</button>
</form>

</body>
</html>
