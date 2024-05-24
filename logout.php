<?php
session_start();

// Hủy bỏ tất cả các biến session
$_SESSION = array();

// Nếu có session cookie, hủy bỏ nó
if(isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Hủy bỏ session
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: login.php");
exit;
?>