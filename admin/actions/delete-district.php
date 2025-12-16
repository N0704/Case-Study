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
    
    // Check if district has posts
    $check = mysqli_query($conn, "SELECT COUNT(*) as count FROM motels WHERE district_id = $id");
    $count = mysqli_fetch_assoc($check)['count'];
    
    if ($count > 0) {
        $_SESSION['error'] = "Không thể xóa khu vực này vì còn $count tin đăng!";
        header('Location: ../districts.php');
        exit;
    }
    
    // Delete
    if (mysqli_query($conn, "DELETE FROM districts WHERE id = $id")) {
        $_SESSION['success'] = 'Xóa khu vực thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra!';
    }
}

header('Location: ../districts.php');
?>
