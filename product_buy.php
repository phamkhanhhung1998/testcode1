<?php
session_start();
include './connect/conn.php';
// Kết nối đến cơ sở dữ liệu
//$conn = mysqli_connect("localhost", "root","", "test");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Truy vấn lấy danh sách sản phẩm
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

// Đóng kết nối
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        .product {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .product img {
            max-width: 100px;
            margin-right: 20px;
        }
        .product-details {
            flex-grow: 1;
        }
        .product-details h3 {
            margin: 0;
        }
        .product-actions {
            text-align: right;
        }
    </style>
</head>
<body>

<h2>Danh Sách Sản Phẩm</h2>
<div style="text-align: center;">
    <a href="cart.php">Xem giỏ hàng</a>
    <a href="xemgiohang.php" style="margin-left: 10px ;">Xem giỏ hàng khác</a>
    <a href="xemgiohangid.php?user_id=<?php echo $_SESSION['user_id']; ?>"; style="margin-left: 10px;">Xem Giỏ Hàng 2</a>

</div>
<?php
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        echo "<div class='product'>";
       // echo "<img src='" . $row['image'] . "' alt='" . $row['name'] . "'>";
       echo "<img src='{$row['image_url']}' alt='Ảnh Sản Phẩm' class='product-image'>";
        echo "<div class='product-details'>";
        echo "<h3>" . $row['name'] . "</h3>";
        echo "<p>Giá: " . $row['price'] . " VND</p>";
        echo "</div>";
        echo "<div class='product-actions'>";
        //echo "<form action='add_to_cart.php' method='POST'>";
        echo "<form action='themsp_giohang.php' method='POST'>";
        echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
        echo "<input type='number' name='quantity' min='1' value='1' required>";
        echo "<button type='submit'>Thêm vào giỏ hàng</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "Không có sản phẩm nào.";
}
?>

</body>
</html>
