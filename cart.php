<?php
session_start();

// Kết nối đến cơ sở dữ liệu
$conn = mysqli_connect("localhost", "root", "", "test");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Khởi tạo biến tổng giá trị giỏ hàng
$total = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Lấy thông tin sản phẩm từ giỏ hàng
    $product_ids = implode(',', array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE id IN ($product_ids)";
    $result = mysqli_query($conn, $sql);
    
    $cart_items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $cart_items[] = $row;
        $total += $row['quantity'] * $row['price'];
    }
} else {
    echo "Giỏ hàng của bạn đang trống.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
        background-color: #f2f2f2;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    tr:hover {
        background-color: #ddd;
    }
    button {
        background-color: #4CAF50;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
    }
    a {
        /* display: inline-block; Để nút và link nằm trên cùng một dòng */
        padding: 15px 32px; /* Đồng bộ padding với nút */
        text-align: center; /* Căn giữa chữ */
        text-decoration: none; /* Loại bỏ gạch chân */
        color: #4CAF50; /* Màu chữ */
        font-size: 16px; /* Kích thước chữ */
        transition: color 0.3s ease; /* Hiệu ứng màu chữ khi di chuột qua */
    }
    </style>
</head>
<body>

<h2>Giỏ Hàng</h2>

<?php if (!empty($cart_items)): ?>
<table>
    <thead>
        <tr>
            <th>Sản Phẩm</th>
            <th>Số Lượng</th>
            <th>Giá</th>
            <th>Thành Tiền</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cart_items as $item): ?>
        <tr>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo $item['price']; ?> VND</td>
            <td><?php echo $item['quantity'] * $item['price']; ?> VND</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Tổng Cộng</td>
            <td><?php echo $total; ?> VND</td>
        </tr>
    </tfoot>
</table>
<form action="checkout.php" method="POST">
    <button type="submit">Thanh Toán</button>
</form>
<?php endif; ?>
<a href="product_buy.php" style="display: inline-block;">Tiếp tục mua sắm</a>
</body>
</html>

<?php
// Đóng kết nối
mysqli_close($conn);
?>
