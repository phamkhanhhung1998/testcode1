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
            background-color: #4CAF50;
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
// if (!isset($_SESSION['user_id']) || $_SESSION['isadmin'] == 0  ){ 
//     echo "Bạn không có quyền thêm sản phẩm";
//    // header("Location: login.php");
//     exit();
// } 
// Bắt đầu session
if( $_SESSION['isadmin'] == 1){
    echo " thêm sản phẩm";
    
} else {
    echo "Bạn không có quyền thêm sản phẩm";
    exit();
}

?>

<h2 style="text-align: center;">Thêm Sản Phẩm Mới</h2>

<form action="process_create_product.php" method="POST" enctype="multipart/form-data">
    <label for="name">Tên Sản Phẩm:</label>
    <input type="text" id="name" name="name" required>

    <label for="price">Giá:</label>
    <input type="number" id="price" name="price" min="0" step="0.01" required>

    <label for="description">Mô Tả:</label>
    <textarea id="description" name="description" required></textarea>

    <label for="image">Ảnh:</label>
    <input type="file" id="image" name="image" required ">
    <!-- <textarea id="description" name="description" required></textarea> -->
    <!-- accept="image/* -->
    <button type="submit">Thêm Sản Phẩm</button>
</form>

</body>
</html>
