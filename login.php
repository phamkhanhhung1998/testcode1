<?php
session_start();

// Kiểm tra nếu người dùng đã đăng nhập, chuyển hướng đến trang chào mừng tương ứng
if(isset($_SESSION['username']) && $_SESSION['isadmin'] == 1){
    header("Location: ./page/admin_page.php");
    exit;
}
else if(isset($_SESSION['username']) && $_SESSION['isadmin'] == 0){
    header("Location: ./page/user_page.php");
    exit;
}

include './connect/conn.php';

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
    
    if ($time_difference < 300) { // 300 giây = 5 phút
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
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    // Xác thực thông tin đăng nhập
    $stmt = $conn->prepare("SELECT id, password, isadmin FROM user WHERE user_name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            // Lưu thông tin đăng nhập vào session
            $_SESSION['username'] = $username;
            $_SESSION['isadmin'] = $row['isadmin'];
            $_SESSION['user_id'] = $row['id'];

            // Reset số lần đăng nhập sai sau khi đăng nhập thành công
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_login_attempt'] = 0;

            // Chuyển hướng người dùng sau khi đăng nhập thành công
            if ($row['isadmin'] == 1) {
                header("Location: ./page/admin_page.php");
                exit;
            } else {
                header("Location: ./page/user_page.php");
                exit;
            }
        } else {
            // Mật khẩu không đúng
            $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng";
        }
    } else {
        // Tên đăng nhập không tồn tại
        $_SESSION['error'] = "Tên đăng nhập hoặc mật khẩu không đúng";
    }

    $stmt->close();
    $conn->close();
    
    // Tăng số lần đăng nhập sai và ghi lại thời gian
    $_SESSION['login_attempts'] += 1;
    $_SESSION['last_login_attempt'] = time();
}

// Nếu đăng nhập không thành công, hiển thị lỗi
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
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
            background-color: #008CBA;
            color: white;
            padding: 10px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 150%;
        }
        button.register-btn:hover {
            opacity: 0.8;
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
        <input type="text" name="username" required><br>
        <label>Mật khẩu:</label><br>
        <input type="password" name="password" required><br>
        <input type="submit" value="Đăng nhập">
    </form>
    <div class="center">
        <a href="register.php"><button type="button" class="register-btn">Đăng ký</button></a>
    </div>
    <?php
    if(isset($error)) {
        echo '<p style="color:red; text-align: center;">'.$error.'</p>';
    }
    ?>
</body>
</html>
