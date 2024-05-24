<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <style>
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        p {
            text-align: center;
            font-size: 20px;
        }
    </style>
</head>

<?php
session_start();
//$conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    echo "Bạn cần phải đăng nhập để thanh toán.";
    
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "DELETE FROM cart WHERE user_id = $user_id";
if (mysqli_query($conn, $sql)) {
    echo "<p>Thanh toán thành công. Giỏ hàng đã được xóa.</p>";
} else {
    echo "Lỗi: " . $sql . "<br>" . mysqli_error($conn);
}

mysqli_close($conn);
?>
<body>
    <div style="margin-top: 50px; text-align:center">
        <a href="product_buy.php"><button>Tiếp tục mua sắm</button></a>
        <a href="xemgiohang.php" style="margin-left: 10px;"><button>Xem giỏ hàng</button></a>
    </div>
    