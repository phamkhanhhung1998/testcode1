<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần phải đăng nhập để thêm sản phẩm vào giỏ hàng.";
    exit();
}

// Kiểm tra dữ liệu POST
if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Kiểm tra nếu giỏ hàng chưa tồn tại thì tạo giỏ hàng
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Kiểm tra nếu sản phẩm đã tồn tại trong giỏ hàng thì cập nhật số lượng
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    echo "Sản phẩm đã được thêm vào giỏ hàng.";
   // print_r($_SESSION) ;
} else {
    echo "Dữ liệu không hợp lệ.";
}
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
    <a href="cart.php">Xem giỏ hàng</a>
</body>
</html>
