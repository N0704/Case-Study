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
    
    // Approve post (set approve = 1)
    if (mysqli_query($conn, "UPDATE motels SET approve = 1 WHERE id = $id")) {
        $_SESSION['success'] = 'Duyệt tin thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra!';
    }
}

header('Location: ../posts.php');
?>
