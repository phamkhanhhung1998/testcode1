<?php
session_start();

// Kiểm tra nếu người dùng chưa đăng nhập, chuyển hướng đến trang đăng nhập
// if(!isset($_SESSION['username'])){
//     header("Location: login.php");
//     exit;
// }
if(!isset($_SESSION['username']) || $_SESSION['isadmin'] != 0){
    // Nếu không, chuyển hướng đến trang đăng nhập
    header("Location: ../login.php");
    exit;
}


// Hiển thị chào mừng và thông tin của người dùng
$username = $_SESSION['username'];

echo "<h2>Chào mừng, $username!</h2>";
echo "<p>Thông tin của bạn:</p>";

// Kết nối đến cơ sở dữ liệu và lấy thông tin của người dùng
//$conn = mysqli_connect("localhost", "root", "", "test");
include './connect/conn.php';
if(!$conn){
    die("Kết nối không thành công: " . mysqli_connect_error());
}

$sql = "SELECT * FROM user WHERE user_name = '$username'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo "Tên đăng nhập: " . $row['user_name'] . "<br>";
echo "Tên đầy đủ: " . $row['full_name'] . "<br>";

mysqli_close($conn);
?>
 <a href="logout.php">Đăng xuất</a>