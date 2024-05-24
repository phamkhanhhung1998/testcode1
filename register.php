<?php
session_start();

// Include the connection file
include './connect/conn.php';

// Check the connection
if (!$conn) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    // Get and sanitize input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);

    // Validate password strength
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        die('Password must be at least 8 characters long and include uppercase and lowercase letters, a number, and a special character.');
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    // Check if username already exists
    $sql = "SELECT id FROM user WHERE user_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "Username đã tồn tại. Vui lòng chọn username khác.";
    } else {
        // Insert new user into database
        $sql = "INSERT INTO user (user_name, password, full_name, isadmin) VALUES (?, ?, ?, 0)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $password_hash, $fullname);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p style=\"text-align:center\">Đăng ký thành công! Bạn có thể <a href='login.php'>đăng nhập</a> ngay bây giờ.</p>";
        } else {
            echo "Lỗi: " . mysqli_error($conn);
        }
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        form {
            max-width: 400px;
            margin: auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var errorElement = document.getElementById("error_message");
            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
            if (!regex.test(password)) {
                errorElement.textContent = "Password must be at least 8 characters long and include uppercase and lowercase letters, a number, and a special character.";
                return false;
            }
            errorElement.textContent = "";
            return true;
        }
    </script>
</head>
<body>
    <h2>Đăng Ký</h2>
    <form method="post" action="register.php" onsubmit="return validatePassword()">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label for="username">Username</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="full_name">Full name</label><br>
        <input type="text" id="full_name" name="full_name" required><br>
        <label for="password">Password</label><br>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Đăng Ký">
        <div id="error_message" class="error"></div>
    </form>
</body>
</html>
