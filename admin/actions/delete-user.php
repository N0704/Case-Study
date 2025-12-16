<?php
session_start();

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../../login.php');
    exit;
}

include('../../config/db.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Cannot delete yourself
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = 'Không thể xóa tài khoản của chính mình!';
        header('Location: ../users.php');
        exit;
    }
    
    // Check if user has posts
    $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM motels WHERE user_id = $id");
    $count = mysqli_fetch_assoc($check)['count'];
    
    if ($count > 0) {
        $_SESSION['error'] = "Không thể xóa người dùng này vì còn $count tin đăng!";
        header('Location: ../users.php');
        exit;
    }
    
    // Delete
    if (mysqli_query($conn, "DELETE FROM users WHERE id = $id")) {
        $_SESSION['success'] = 'Xóa người dùng thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra!';
    }
}

header('Location: ../users.php');
?>
