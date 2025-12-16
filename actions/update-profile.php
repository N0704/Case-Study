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
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check email exists (for other users)
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $user_id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = 'Email đã được sử dụng bởi tài khoản khác!';
        header('Location: ../profile.php');
        exit;
    }
    
    // Update
    $query = "UPDATE users SET fullname = '$fullname', email = '$email' WHERE id = $user_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['fullname'] = $fullname;
        $_SESSION['success'] = 'Cập nhật thông tin thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra!';
    }
}

header('Location: ../profile.php');
?>
