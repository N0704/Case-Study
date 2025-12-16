<?php
session_start();

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../../login.php');
    exit;
}

include('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $new_password = $_POST['new_password'];
    
    if (strlen($new_password) < 6) {
        $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        header('Location: ../users.php');
        exit;
    }
    
    // Update password
    if (mysqli_query($conn, "UPDATE users SET password = '$new_password' WHERE id = $id")) {
        $_SESSION['success'] = 'Đổi mật khẩu thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra!';
    }
}

header('Location: ../users.php');
?>
