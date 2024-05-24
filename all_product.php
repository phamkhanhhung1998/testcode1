<!-- <?php
// Bắt đầu session



session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if(isset($_SESSION['user_id'])) {
    // Người dùng đã đăng nhập, lấy user_id từ session
    $user_id = $_SESSION['user_id'];
} else {
    // Người dùng chưa đăng nhập, gán user_id mặc định là 0 hoặc làm gì đó khác tùy thuộc vào yêu cầu của bạn
    header("Location: login.php");
}
// echo $_SESSION['isadmin'];
print_r($_SESSION);
?> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm</title>
    <style>
        /* Style cho sản phẩm */
        .product {
    border: 1px solid #ccc;
    padding: 20px; /* Tăng padding để tạo không gian xung quanh nội dung */
    margin: 20px; /* Tăng margin để tạo không gian giữa các sản phẩm */
    width: 200px;
    height: 300px; /* Đặt chiều cao tự động để nội dung không bị cắt */
    float: left;
    text-align: center;
    box-shadow: 0 0 10px rgba(0,0,0,0.1); /* Thêm bóng để tạo độ sâu */
    transition: box-shadow 0.3s ease; /* Thêm hiệu ứng khi di chuột qua sản phẩm */
    display: flex; /* Chuyển sang flexbox */
    flex-direction: column; /* Sắp xếp các mục theo cột */
    justify-content: space-between; /* Phân bố không gian giữa các mục */
}

.product:hover {
    box-shadow: 0 0 10px rgba(0,0,0,0.3); /* Tăng độ mờ của bóng khi di chuột qua */
}

.product img {
    max-width: 150px;
    height: auto; /* Đặt chiều cao tự động để ảnh không bị méo */
    margin-bottom: 10px; /* Tạo không gian giữa ảnh và tiêu đề */
}

.product h3 {
    margin-bottom: 10px; /* Tạo không gian giữa tiêu đề và liên kết chi tiết */
}
        .product img {
            max-width: 150px;
            height: 150px;
        }
        a {
            margin-right: 20px; /* Tạo khoảng cách giữa các thẻ a */
            color:#f00 ; /* Màu chữ */
            text-decoration: none; /* Loại bỏ gạch chân */
            transition: color 0.3s ease; /* Hiệu ứng màu chữ khi di chuột qua */
            
        }

        a:hover {
            color: #f00; /* Màu chữ khi di chuột qua */
        }
        .product a {
    align-self: flex-end; /* Đẩy thẻ a xuống dưới cùng */
    
}
a {
    margin-right: 20px; /* Tạo khoảng cách giữa các thẻ a */
    color:#f00 ; /* Màu chữ */
    text-decoration: none; /* Loại bỏ gạch chân */
    transition: color 0.3s ease; /* Hiệu ứng màu chữ khi di chuột qua */
    text-align: center; /* Căn giữa chữ */
}
.product a {
    margin-right: 20px; /* Tạo khoảng cách giữa các thẻ a */
    color:#f00 ; /* Màu chữ */
    text-decoration: none; /* Loại bỏ gạch chân */
    transition: color 0.3s ease; /* Hiệu ứng màu chữ khi di chuột qua */
    text-align: center; /* Căn giữa chữ */
    display: flex; /* Chuyển sang flexbox */
    justify-content: center; /* Căn giữa nội dung theo chiều ngang */
    align-items: center; /* Căn giữa nội dung theo chiều dọc */
}
body {
    font-family: Arial, sans-serif; /* Sử dụng font chữ dễ đọc */
    padding: 20px; /* Tạo không gian xung quanh nội dung */
    background-color: #f0f0f0; /* Màu nền nhẹ */
}

p {
    font-size: 18px; /* Kích thước chữ lớn hơn */
}

a {
    color: #007BFF; /* Màu chữ cho liên kết */
    text-decoration: none; /* Loại bỏ gạch chân */
}

a:hover {
    color: #0056b3; /* Màu chữ khi di chuột qua */
}

h2 {
    color: #333; /* Màu chữ cho tiêu đề */
    margin-top: 20px; /* Tạo không gian trên tiêu đề */
}
    </style>
</head>
<body>
<p>Xin chào, <?php echo $_SESSION['username']; ?>!</p>

    <!-- Liên kết hoặc nút để đăng xuất -->
    <a href="logout.php">Đăng xuất</a>
    <a href="create_product.php">Thêm sản phẩm</a>
    <a href="product_buy.php">Trang mua hàng</a>
<h2>Danh Sách Sản Phẩm</h2>

<?php

// Kết nối đến cơ sở dữ liệu
$conn = mysqli_connect("localhost", "root", "", "test");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Truy vấn lấy toàn bộ sản phẩm
$sql = "SELECT id, name, image_url FROM products";
$result = mysqli_query($conn, $sql);

// Kiểm tra số lượng bản ghi trả về
if (mysqli_num_rows($result) > 0) {
    // Lặp qua từng sản phẩm và hiển thị
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='product'>";
        echo "<img src='{$row['image_url']}' alt='Ảnh Sản Phẩm'>";
        echo "<h3>{$row['name']}</h3>";
        echo "<a href='product.php?product_id={$row['id']}'>Chi Tiết</a>";
        echo "</div>";
    }
} else {
    echo "Không có sản phẩm nào.";
}

// Đóng kết nối
mysqli_close($conn);
?>

</body>
</html>