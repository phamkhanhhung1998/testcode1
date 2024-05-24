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
        
        p {
            text-align: center;
            font-size: 20px;
        }
    </style>
</head>
<body>
<!-- <?php
session_start();
//$conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần phải đăng nhập để xem giỏ hàng.";
    header("Location: ./login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT products.*, cart.quantity AS cart_quantity FROM products INNER JOIN cart ON products.id = cart.product_id WHERE cart.user_id = $user_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "Sản phẩm: " . $row['name'] . " - Số lượng: " . $row['cart_quantity'] . "<br>";
    }
} else {
    echo "<p>Giỏ hàng của bạn đang trống.</p>";
}

mysqli_close($conn);
?> -->
<!-- // comment -->

<?php
//session_start();
$conn = mysqli_connect("localhost", "root", "", "test");

if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần phải đăng nhập để xem giỏ hàng.";
    exit();
}

$user_id = $_SESSION['user_id'];

// Truy vấn SQL để lấy thông tin giỏ hàng, tổng hợp số lượng sản phẩm có cùng ID
$sql = "SELECT products.*, SUM(cart.quantity) AS total_quantity
        FROM products
        INNER JOIN cart ON products.id = cart.product_id
        WHERE cart.user_id = $user_id
        GROUP BY products.id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<p>Xin chào, " . $_SESSION['username'] . "!</p>"; // Hiển thị tên người dùng
    echo "<h2>Giỏ Hàng</h2>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Sản Phẩm</th>";
    echo "<th>Số Lượng</th>";
    echo "<th>Giá</th>";
    echo "<th>Thành Tiền</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['total_quantity'] . "</td>";
        echo "<td>" . $row['price'] . " VND</td>";
        echo "<td>" . ($row['total_quantity'] * $row['price']) . " VND</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";

    // Tính tổng số tiền của giỏ hàng
    $total_price = 0;
    mysqli_data_seek($result, 0); // Đưa con trỏ kết quả về đầu
    while ($row = mysqli_fetch_assoc($result)) {
        $total_price += $row['total_quantity'] * $row['price'];
    }
    echo "<p>Tổng Cộng: " . $total_price . " VND</p>";
} else {
    echo "<p>Giỏ hàng của bạn đang trống.</p>";
}

mysqli_close($conn);
?>
<div style="margin-top: 50px; text-align:center">
<a href="thanhtoan.php"><button>Thanh Toán</button></a>
<a href="product_buy.php"><button>Mua tiếp</button></a>
</div>