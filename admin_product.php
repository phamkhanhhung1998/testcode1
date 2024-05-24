<?php
// Bắt đầu session
session_start();

// Kiểm tra xem người dùng đã đăng nhập với quyền admin chưa
if(!isset($_SESSION['user_id']) || $_SESSION['isadmin'] != 1) {
    echo "Bạn không có quyền truy cập trang này.";
    exit();
}

// Kết nối đến cơ sở dữ liệu
$conn = mysqli_connect("localhost", "root", "", "test");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Truy vấn lấy danh sách sản phẩm
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

echo "<h2>Danh Sách Sản Phẩm</h2>";

if (mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<li>";
        echo "<a href='edit_product.php?product_id=" . $row['id'] . "'>" . $row['name'] . "</a>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "Không có sản phẩm nào.";
}

// Đóng kết nối
mysqli_close($conn);
?>
