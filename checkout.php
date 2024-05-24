<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần phải đăng nhập để thực hiện thanh toán.";
    exit();
}

// Kiểm tra xem giỏ hàng có sản phẩm hay không
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "Giỏ hàng của bạn đang trống.";
    exit();
}

// Kết nối đến cơ sở dữ liệu
// $conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Lấy thông tin sản phẩm trong giỏ hàng
$cart = $_SESSION['cart'];
$product_ids = implode(",", array_keys($cart));
$sql_products = "SELECT * FROM products WHERE id IN ($product_ids)";
$result = mysqli_query($conn, $sql_products);

// Tính tổng tiền của đơn hàng
$total = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $product_id = $row['id'];
    $quantity = $cart[$product_id];
    $price = $row['price'];
    $total += $quantity * $price;
}

// Tạo đơn hàng mới
$user_id = $_SESSION['user_id'];
$sql_order = "INSERT INTO orders (user_id, order_date, total) VALUES ($user_id, NOW(), $total)";
mysqli_query($conn, $sql_order);
$order_id = mysqli_insert_id($conn);

// Thêm chi tiết đơn hàng
foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $sql_order_detail = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $quantity, (SELECT price FROM products WHERE id = $product_id))";
    mysqli_query($conn, $sql_order_detail);
}

// Xóa giỏ hàng sau khi đã thanh toán
unset($_SESSION['cart']);

// Đóng kết nối
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Đơn Hàng</title>
</head>
<body>
    <h1>Thanh Toán Đơn Hàng</h1>
    <p>Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn đã được ghi nhận.</p>
    <p>Tổng số tiền cần thanh toán: <?php echo number_format($total, 2); ?> VND</p>
</body>
</html>
