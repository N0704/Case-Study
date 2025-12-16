<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Check ownership
    $check = mysqli_query($conn, "SELECT id, is_rented FROM motels WHERE id = $id AND user_id = $user_id");
    
    if (mysqli_num_rows($check) > 0) {
        $post = mysqli_fetch_assoc($check);
        $new_status = $post['is_rented'] == 0 ? 1 : 0;
        
        if (mysqli_query($conn, "UPDATE motels SET is_rented = $new_status WHERE id = $id")) {
            $_SESSION['success'] = $new_status == 1 ? 'Đã đánh dấu tin là "Đã thuê"!' : 'Đã đánh dấu tin là "Chưa thuê"!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra!';
        }
    } else {
        $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này!';
    }
}

// Redirect back
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Location: ../index.php');
}
?>
