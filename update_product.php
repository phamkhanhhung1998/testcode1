<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới</title>
    <style>
         body {
            font-family: Arial, sans-serif;
        }
        form {
            width: 50%;
            margin: 0 auto;
            background-color: #f2f2f2;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px #000;
        }
        label, input, textarea {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #367517;


            color: white;
            padding: 10px 20px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['isadmin']== 0) {
    echo "Bạn không có quyền thêm sản phẩm";
   // header("Location: login.php");
    exit();
}
// Kết nối đến cơ sở dữ liệu
//$conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Kiểm tra xem có dữ liệu sản phẩm được gửi từ form hay không
if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Kiểm tra xem người dùng đã chọn tập tin hình ảnh mới hay không
    if ($_FILES['image']['size'] > 0) {
        // Đường dẫn tạm thời của tệp tải lên
        $temp_file = $_FILES['image']['tmp_name'];

        // Thư mục lưu trữ hình ảnh tải lên
        $uploads_dir = 'uploads/';

        // Tạo tên tệp hình ảnh mới
        $new_file_name = uniqid() . '_' . $_FILES['image']['name'];

        // Di chuyển tập tin tải lên vào thư mục lưu trữ
        move_uploaded_file($temp_file, $uploads_dir . $new_file_name);

        // Tạo đường dẫn hoàn chỉnh của hình ảnh mới
        $image_url = $uploads_dir . $new_file_name;

        // Cập nhật thông tin sản phẩm trong cơ sở dữ liệu
        $sql = "UPDATE products SET name='$name', description='$description', price='$price', image_url='$image_url' WHERE id=$product_id";
    } else {
        // Nếu người dùng không tải lên hình ảnh mới, chỉ cập nhật các trường khác
        $sql = "UPDATE products SET name='$name', description='$description', price='$price' WHERE id=$product_id";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<h4 style=\"text-align:center;\">Cập nhật sản phẩm thành công!</h2>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
}

// Lấy thông tin sản phẩm từ cơ sở dữ liệu dựa trên ID sản phẩm được chọn
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $sql = "SELECT * FROM products WHERE id=$product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title >Chỉnh Sửa Sản Phẩm</title>
</head>
<body>
    <h2 style="text-align: center;">Chỉnh Sửa Sản Phẩm</h2>
    <?php if (isset($product)): ?>
    <form method="post" action="" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <label for="name">Tên Sản Phẩm:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $product['name']; ?>"><br>
        <label for="description">Mô Tả:</label><br>
        <textarea id="description" name="description"><?php echo $product['description']; ?></textarea><br>
        <label for="price">Giá:</label><br>
        <input type="text" id="price" name="price" value="<?php echo $product['price']; ?>"><br>
        <label for="image">Hình Ảnh:</label><br>
        <input type="file" id="image" name="image"><br><br>
        <img src='<?php echo $product['image_url'] ?>' alt='Ảnh Sản Phẩm' style="width: auto; height: 100px;"><br><br>
        <input type="submit" name="submit" value="Lưu Thay Đổi" style="background-color: #4CAF50; color: white; padding: 14px 20px; margin: 8px 0; border: none; cursor: pointer; width: 100%;">
    </form>
    <?php else: ?>
    <p>Không tìm thấy sản phẩm.</p>
    <?php endif; ?>
</body>
</html>

<?php
// Đóng kết nối
mysqli_close($conn);
?>
