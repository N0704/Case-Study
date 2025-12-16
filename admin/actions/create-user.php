<?php
session_start();

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../../login.php');
    exit;
}

include('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = mysqli_real_escape_string($conn, trim($_POST['fullname']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $role = (int)$_POST['role'];
    
    // Validate
    if (empty($fullname) || empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
        header('Location: ../users.php');
        exit;
    }
    
    if (strlen($password) < 6) {
        $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        header('Location: ../users.php');
        exit;
    }
    
    // Check duplicate username
    $check = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = 'Tên đăng nhập đã tồn tại!';
        header('Location: ../users.php');
        exit;
    }
    
    // Check duplicate email
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = 'Email đã được sử dụng!';
        header('Location: ../users.php');
        exit;
    }
    
    // Generate avatar
    $avatar = "https://ui-avatars.com/api/?name=" . urlencode($fullname) . "&background=random";
    
    // Insert
    $query = "INSERT INTO users (fullname, username, email, password, avatar, role) 
              VALUES ('$fullname', '$username', '$email', '$password', '$avatar', $role)";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Thêm người dùng thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra: ' . mysqli_error($conn);
    }
}

header('Location: ../users.php');
?>
