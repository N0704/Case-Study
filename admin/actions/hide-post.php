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
    
    // Hide post (set approve = 2)
    if (mysqli_query($conn, "UPDATE motels SET approve = 2 WHERE id = $id")) {
        $_SESSION['success'] = 'Ẩn tin thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra!';
    }
}

header('Location: ../posts.php');
?>
