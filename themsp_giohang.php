<?php
session_start();
// Kết nối đến cơ sở dữ liệu
//$conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần phải đăng nhập để thêm sản phẩm vào giỏ hàng.";
    exit();
}

if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, $quantity)";
    if (mysqli_query($conn, $sql)) {
        echo "Sản phẩm đã được thêm vào giỏ hàng.";
    } else {
        echo "Lỗi: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    echo "Dữ liệu không hợp lệ.";

}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm vào giỏ hàng</title>
</head>
<body>
    <a href="product_buy.php">Tiếp tục mua sắm</a>
    <a href="xemgiohang.php">Xem giỏ hàng</a>
</body>
</html>