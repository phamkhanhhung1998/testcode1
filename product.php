<?php
// Bắt đầu session
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if(isset($_SESSION['user_id'])) {
    // Người dùng đã đăng nhập, lấy user_id từ session
    $user_id = $_SESSION['user_id'];
    //echo $_SESSION['user_id'];
} else {
    // Người dùng chưa đăng nhập, gán user_id mặc định là 0 hoặc làm gì đó khác tùy thuộc vào yêu cầu của bạn
   echo "Bạn chưa đăng nhập";
  
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <style>
        /* Style cho ảnh sản phẩm */
       
        .product {
    border: 1px solid #ccc;
    padding: 30px; /* Tăng padding để tạo không gian xung quanh nội dung */
    margin: 30px; /* Tăng margin để tạo không gian giữa các sản phẩm */
    width: 250px;
    height: 350px; /* Đặt chiều cao tự động để nội dung không bị cắt */
    float: left;
    text-align: center;
    box-shadow: 0 0 15px rgba(0,0,0,0.2); /* Thêm bóng để tạo độ sâu */
    transition: box-shadow 0.3s ease; /* Thêm hiệu ứng khi di chuột qua sản phẩm */
    display: flex; /* Chuyển sang flexbox */
    flex-direction: column; /* Sắp xếp các mục theo cột */
    justify-content: space-between; /* Phân bố không gian giữa các mục */
    background-color: #fff; /* Thêm màu nền cho sản phẩm */
}

.product:hover {
    box-shadow: 0 0 15px rgba(0,0,0,0.5); /* Tăng độ mờ của bóng khi di chuột qua */
}



.product h3 {
    margin-bottom: 15px; /* Tạo không gian giữa tiêu đề và liên kết chi tiết */
    color: #333; /* Thay đổi màu chữ của tiêu đề */
}

.product a {
    align-self: flex-end; /* Đẩy thẻ a xuống dưới cùng */
    color: #007BFF; /* Thay đổi màu chữ của liên kết */
}

.product a:hover {
    color: #0056b3; /* Thay đổi màu chữ của liên kết khi di chuột qua */
}
h2 {
    color: #333; /* Màu chữ cho tiêu đề */
    margin-bottom: 20px; /* Tạo không gian dưới tiêu đề */
}

p {
    font-size: 16px; /* Kích thước chữ */
    color: #666; /* Màu chữ */
    margin-bottom: 10px; /* Tạo không gian dưới mỗi đoạn văn */
}

p strong {
    color: #333; /* Màu chữ cho phần in đậm */
}

.product-image {
    max-width: 100%; /* Đảm bảo ảnh không vượt quá chiều rộng của container */
    height: auto; /* Đặt chiều cao tự động để ảnh không bị méo */
    margin-bottom: 20px; /* Tạo không gian dưới ảnh */
    width: 150px;
    height: auto;
}
    </style>
</head>
<body>

<?php

// Kết nối đến cơ sở dữ liệu
//$conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Lấy thông tin sản phẩm từ cơ sở dữ liệu (sử dụng product_id làm điều kiện)
$product_id = $_GET['product_id'];
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);

// Kiểm tra số lượng bản ghi trả về
if (mysqli_num_rows($result) > 0) {
    // Lặp qua từng hàng kết quả và hiển thị thông tin sản phẩm
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<h2>{$row['name']}</h2>";
        echo "<p><strong>Giá:</strong> {$row['price']}</p>";
        echo "<img src='{$row['image_url']}' alt='Ảnh Sản Phẩm' class='product-image'>";
        echo "<p><strong>Mô Tả:</strong> {$row['description']}</p>";
        echo "<p><strong>Số lượng:</strong> {$row['stock']}</p>";
    }
} else {
    echo "Không tìm thấy sản phẩm.";
}

// Hiển thị các bình luận về sản phẩm (nếu có)
echo "<h3>Bình Luận</h3>";
$sql_comments = "SELECT * FROM comments WHERE product_id = $product_id";
$result_comments = mysqli_query($conn, $sql_comments);
if (mysqli_num_rows($result_comments) > 0) {
    while ($row_comment = mysqli_fetch_assoc($result_comments)) {
        //$user= $row_comment['user_id'];
        $user_id_sp= $row_comment['user_id'];
        $sql_comments_user = "SELECT * FROM user WHERE id = $user_id_sp";
        $result_comments_user = mysqli_query($conn, $sql_comments_user);
        $row_comment_user = mysqli_fetch_assoc($result_comments_user);

        //echo "<p><strong>{$row_comment_user['user_name']}:</strong> {$row_comment['comment']}</p>";
        echo "<p><strong>" . htmlspecialchars($row_comment_user['user_name']) . ":</strong> " . htmlspecialchars($row_comment['comment']) . "</p>";
    }
} else {
    echo "Chưa có bình luận nào cho sản phẩm này.";
}

// Form nhập bình luận
echo "<div class='comment-form'>";
echo "<h3>Thêm Bình Luận</h3>";
echo "<form action='process_comment.php' method='POST'>";
echo "<input type='hidden' name='product_id' value='$product_id'>";
echo "<textarea name='comment' placeholder='Nhập bình luận của bạn'></textarea><br>";
//thêm trường input hidden để lưu user_id
echo "<input type='hidden' name='user_id' value='$user_id'>";
echo "<button type='submit'>Gửi Bình Luận</button>";
echo "</form>";
echo "</div>";


// Đóng kết nối
mysqli_close($conn);
?>

</body>
</html>
