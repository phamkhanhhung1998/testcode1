<?php
// Bắt đầu session
session_start();
include './connect/conn.php';
// Kiểm tra xem người dùng đã đăng nhập với quyền admin chưa
if(!isset($_SESSION['user_id']) || $_SESSION['isadmin'] != 1) {
    // Nếu không phải admin, có thể chuyển hướng người dùng hoặc hiển thị thông báo lỗi
    echo "Bạn không có quyền truy cập trang này.";
    echo "{$_SESSION['user_id']} và {$_SESSION['isadmin']}";
    exit();
}

// Xử lý việc tạo sản phẩm mới khi người dùng gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra xem các trường cần thiết đã được gửi hay không
    if (isset($_POST['name']) && isset($_POST['price']) && isset($_POST['description']) && isset($_FILES['image'])) {
        // Lấy dữ liệu từ form
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        
        // Xử lý ảnh được tải lên
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["image"])) {
            $file = $_FILES["image"];
            $fileName = $file["name"];
            $targetDir = "uploads/";
            $imageFileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $imageFileType;
            $targetPath = $targetDir . $newFileName; // Đường dẫn tệp tới thư mục lưu trữ ảnh
        
            // Kiểm tra tên tệp để đảm bảo không có ký tự đặc biệt hoặc phần mở rộng phụ
            if (!preg_match('/^[a-zA-Z0-9-_]+\.(jpg|jpeg|png|gif)$/', $fileName)) {
                echo "Tên tệp không hợp lệ.";
                exit();
            }
        
            // Kiểm tra kích thước tệp ảnh
            if ($file["size"] > 500000) {
                echo "Sorry, your file is too large.";
                exit();
            }
        
            // Kiểm tra MIME type của tệp
            // $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            // $fileMimeType = mime_content_type($file["tmp_name"]);
        
            // if (!in_array($fileMimeType, $allowedMimeTypes)) {
            //     echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed based on MIME type.";
            //     exit();
            // }
        
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMimeType = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);
            
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                exit();
            }
            // Sử dụng getimagesize để kiểm tra định dạng ảnh
            $imageInfo = getimagesize($file["tmp_name"]);
            if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedMimeTypes)) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed based on image content.";
                exit();
            }
        
            // Kiểm tra phần mở rộng tệp
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed based on file extension.";
                exit();
            }
        
            // Di chuyển tệp đã xác thực đến thư mục đích
            if (move_uploaded_file($file["tmp_name"], $targetPath)) {
             
            if (!$conn) {
                die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
            }

            // Chuẩn bị truy vấn SQL để thêm sản phẩm mới vào cơ sở dữ liệu
            $sql = "INSERT INTO products (name, price, description, image_url) VALUES ('$name', '$price', '$description', '$target_path')";

            // Thực thi truy vấn
            if (mysqli_query($conn, $sql)) {
                // Sản phẩm đã được thêm thành công, có thể thực hiện các hành động khác tùy thuộc vào yêu cầu của bạn
                echo "Sản phẩm đã được thêm thành công.";
                header("Location:all_product.php");
            } else {
                echo "Lỗi: " . $sql . "<br>" . mysqli_error($conn);
            }

            // Đóng kết nối
            mysqli_close($conn);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "Vui lòng nhập đầy đủ thông tin sản phẩm.";
    }
    }
}
?>