<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Get current password from DB
    $query = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id");
    $user = mysqli_fetch_assoc($query);
    
    // Verify current password
    if ($current_password !== $user['password']) {
        $_SESSION['error'] = 'Mật khẩu hiện tại không đúng!';
        header('Location: ../profile.php');
        exit;
    }
    
    // Validate new password
    if (strlen($new_password) < 6) {
        $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
        header('Location: ../profile.php');
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = 'Mật khẩu xác nhận không khớp!';
        header('Location: ../profile.php');
        exit;
    }
    
    // Update password
    $update = mysqli_query($conn, "UPDATE users SET password = '$new_password' WHERE id = $user_id");
    
    if ($update) {
        $_SESSION['success'] = 'Đổi mật khẩu thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra!';
    }
}

header('Location: ../profile.php');
?>
