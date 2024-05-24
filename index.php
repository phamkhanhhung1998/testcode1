<?php
session_start();

// Kiểm tra nếu người dùng đã đăng nhập, chuyển hướng đến trang chào mừng
// if(isset($_SESSION['username'])){
//     header("Location: welcome.php");
//     exit;
// }
if(isset($_SESSION['username']) && $_SESSION['isadmin'] == 1){
    // Nếu không, chuyển hướng đến trang đăng nhập
    header("Location: ./page/admin_page.php");
    exit;
}
else if(isset($_SESSION['username']) && $_SESSION['isadmin'] == 0){
    // Nếu không, chuyển hướng đến trang đăng nhập
    header("Location: ./page/user_page.php");
    exit;
}



include './connect/conn.php';

// Kết nối đến cơ sở dữ liệu
// $conn = mysqli_connect("localhost", "root", "", "test");

// if(!$conn){
//     die("Kết nối không thành công: " . mysqli_connect_error());
// }
// Giới hạn số lần đăng nhập sai
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if (!isset($_SESSION['last_login_attempt'])) {
    $_SESSION['last_login_attempt'] = 0;
}

// Kiểm tra nếu đã vượt quá số lần đăng nhập sai cho phép và thời gian chờ 5 phút
if ($_SESSION['login_attempts'] >= 3) {
    $current_time = time();
    $time_difference = $current_time - $_SESSION['last_login_attempt'];
    
    if ($time_difference < 20) { // 300 giây = 5 phút
        $time_left = 300 - $time_difference;
        echo "Bạn đã đăng nhập sai quá 3 lần. Vui lòng thử lại sau " . ceil($time_left / 60) . " phút.";
        exit;
    } else {
        // Reset số lần đăng nhập sai sau 5 phút
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_login_attempt'] = 0;
    }
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin đăng nhập từ form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Xác thực thông tin đăng nhập
    // $sql = "SELECT * FROM user WHERE user_name = '$username' AND password = '$password'";
    // $result = mysqli_query($conn, $sql);
    // $count = mysqli_num_rows($result);
    
    $stmt = $conn->prepare("SELECT * FROM user WHERE user_name = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

     // Fetch the result and count the rows
     $result = $stmt->get_result();
     $count = $result->num_rows;

    if($count ==1) {
        
        $sql = "SELECT id,isadmin FROM user WHERE user_name = '$username'";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $isadmin = $row['isadmin'];
        $user_id = $row['id']; // Giả sử 'id' là tên cột chứa id user trong bảng 'user'

        // Lưu thông tin đăng nhập vào session
        $_SESSION['username'] = $username; // ['username'] phụ thuộc vào cách mày chọn tên biến thôi nha
        $_SESSION['isadmin'] = $isadmin;
        $_SESSION['user_id'] = $user_id; // Lưu id user vào session

        $s=$_SESSION['username'];
        echo "$s đã đăng nhập thành công";
    
        // Kiểm tra nếu là admin chuyển hướng đến trang admin, ngược lại chuyển hướng đến trang người dùng
        if($isadmin == 1) {
            header("Location: ./page/admin_page.php");
           // header("Location: ./all_product.php");
        } else {
            header("Location: ./page/user_page.php");
            //header("Location: ./all_product.php");
        }
    } else {
        // Đăng nhập không thành công, chuyển hướng lại đến trang đăng nhập với thông báo lỗi
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng";
        
       // header("Location: login.php");
        //echo "Tên đăng nhập hoặc mật khẩu không đúng";
}
// Nếu đăng nhập không thành công
$_SESSION['login_attempts'] += 1;
$_SESSION['last_login_attempt'] = time();
$_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng";
//echo "Tên đăng nhập hoặc mật khẩu không đúng";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <style>
        form {
    width: 300px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
input[type="text"], input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0 10px 0;
    border: none;
    background: #f1f1f1;
}
input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    margin: 8px 0;
    border: none;
    cursor: pointer;
    width: 100%;
}

input[type="submit"]:hover {
    opacity: 0.8;
}
button.register-btn {
    background-color: #008CBA; /* Màu nền */
    color: white; /* Màu chữ */
    padding: 10px 20px; /* Padding */
    margin: 8px 0; /* Margin */
    border: none; /* Không viền */
    cursor: pointer; /* Con trỏ chuột */
    width: 150%; /* Chiều rộng */
}

button.register-btn:hover {
    opacity: 0.8; /* Độ mờ khi rê chuột lên nút */
}
.center {
    display: flex;
    justify-content: center;
}
        </style>
</head>
<body>
    <h2 style="text-align: center;">Đăng nhập</h2>
    <form method="post">
        <label>Tên đăng nhập:</label><br>
        <input type="text" name="username"><br>
        <label>Mật khẩu:</label><br>
        <input type="password" name="password"><br>
        <input type="submit" value="Đăng nhập">
    </form>
    <div class="center">
        <a href="register.php"><button type="button" class="register-btn">Đăng ký</button></a>
    </div>
    <?php
    if(isset($error)) {
        echo '<p>'.$error.'</p>';
    }
    ?>
</body>
</html>
