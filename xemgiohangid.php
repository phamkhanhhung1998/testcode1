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
<?php
session_start();
//$conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Kiểm tra user_id được truyền qua URL
if (!isset($_GET['user_id'])) {
    echo "Không tìm thấy thông tin người dùng.";
    exit();
}


// Lấy user_id từ session
$current_user_id = $_SESSION['user_id'];

// Lấy user_id được truyền qua URL
if (isset($_GET['user_id'])) {
    $requested_user_id = $_GET['user_id'];
    
    // Kiểm tra quyền truy cập
    if ($current_user_id != $requested_user_id) {
        echo "Bạn không có quyền truy cập vào giỏ hàng của người dùng này.";
        exit();
    }
} else {
    echo "Không tìm thấy thông tin người dùng.";
    exit();
}
// Truy vấn SQL để lấy thông tin giỏ hàng dựa trên user_id, tổng hợp số lượng sản phẩm giống nhau
$sql = "SELECT products.*, SUM(cart.quantity) AS total_quantity
        FROM products
        INNER JOIN cart ON products.id = cart.product_id
        WHERE cart.user_id = ?
        GROUP BY products.id";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Lỗi truy vấn SQL: " . mysqli_error($conn);
    exit();
}

mysqli_stmt_bind_param($stmt, "i", $requested_user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
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

    $total_price = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['total_quantity'] . "</td>";
        echo "<td>" . $row['price'] . " VND</td>";
        echo "<td>" . ($row['total_quantity'] * $row['price']) . " VND</td>";
        echo "</tr>";
        $total_price += $row['total_quantity'] * $row['price'];
    }

    echo "</tbody>";
    echo "</table>";
    echo "<p>Tổng Cộng: " . $total_price . " VND</p>";
} else {
    echo "<p>Giỏ hàng của người dùng này đang trống.</p>";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<div style="margin-top: 50px; text-align:center">
<a href="thanhtoan.php"><button>Thanh Toán</button></a>
<a href="product_buy.php"><button>Mua tiếp</button></a>
</div>
</body>
</html>
