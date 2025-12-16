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
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role = (int)$_POST['role'];
    
    // Validate
    if (empty($fullname) || empty($username) || empty($email)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
        header('Location: ../users.php');
        exit;
    }
    
    // Check duplicate username (exclude current)
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = 'Tên đăng nhập đã tồn tại!';
        header('Location: ../users.php');
        exit;
    }
    
    // Check duplicate email (exclude current)
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' AND id != $id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = 'Email đã được sử dụng!';
        header('Location: ../users.php');
        exit;
    }
    
    // Update
    $query = "UPDATE users SET fullname = '$fullname', username = '$username', email = '$email', role = $role WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Cập nhật người dùng thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra: ' . mysqli_error($conn);
    }
}

header('Location: ../users.php');
?>
