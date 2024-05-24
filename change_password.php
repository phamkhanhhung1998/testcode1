<?php
session_start();
include './connect/conn.php';

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

// Tạo và lưu mã CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Tạo mã CSRF token ngẫu nhiên
}

// Kiểm tra mã CSRF token và xử lý yêu cầu thay đổi mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        echo "CSRF token không hợp lệ.";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $old_password = mysqli_real_escape_string($conn, $_POST['old_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Kiểm tra mật khẩu mới và mật khẩu xác nhận có khớp nhau không
    if ($new_password !== $confirm_password) {
        echo "Mật khẩu xác nhận không khớp.";
        exit();
    }

    // Kiểm tra mật khẩu mới có đủ mạnh không
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
        echo "Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.";
        exit();
    }

    // Lấy mật khẩu hiện tại từ cơ sở dữ liệu
    $sql = "SELECT password FROM user WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_password_hash = $row['password'];

        // Kiểm tra mật khẩu cũ có đúng không
        if (password_verify($old_password, $current_password_hash)) {
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT);

            // Cập nhật mật khẩu mới vào cơ sở dữ liệu
            $sql = "UPDATE user SET password='$new_password_hash' WHERE id='$user_id'";

            if (mysqli_query($conn, $sql)) {
                echo "Mật khẩu đã được thay đổi thành công.";
            } else {
                echo "Lỗi: " . mysqli_error($conn);
            }
        } else {
            echo "Mật khẩu cũ không đúng.";
        }
    } else {
        echo "Người dùng không tồn tại.";
    }
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thay Đổi Mật Khẩu</title>
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
        input[type="password"] {
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
            var newPassword = document.getElementById("new_password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            var errorElement = document.getElementById("error_message");
            var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

            if (newPassword !== confirmPassword) {
                errorElement.textContent = "Mật khẩu xác nhận không khớp.";
                return false;
            }

            if (!regex.test(newPassword)) {
                errorElement.textContent = "Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.";
                return false;
            }

            errorElement.textContent = "";
            return true;
        }
    </script>
</head>
<body>
    <h2>Thay Đổi Mật Khẩu</h2>
    <form method="post" action="change_password.php" onsubmit="return validatePassword()">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <label for="old_password">Mật khẩu cũ:</label><br>
        <input type="password" id="old_password" name="old_password" required><br>
        <label for="new_password">Mật khẩu mới:</label><br>
        <input type="password" id="new_password" name="new_password" required><br>
        <label for="confirm_password">Xác nhận mật khẩu mới:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br>
        <input type="submit" value="Thay Đổi Mật Khẩu">
        <div id="error_message" class="error"></div>
    </form>
</body>
</html>

