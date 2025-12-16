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
    
    // Get post image
    $query = mysqli_query($conn, "SELECT image FROM motels WHERE id = $id");
    if ($query && mysqli_num_rows($query) > 0) {
        $post = mysqli_fetch_assoc($query);
        
        // Delete image file
        if (!empty($post['image']) && file_exists('../../' . $post['image'])) {
            unlink('../../' . $post['image']);
        }
        
        // Delete from database
        if (mysqli_query($conn, "DELETE FROM motels WHERE id = $id")) {
            $_SESSION['success'] = 'Xóa tin thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi khi xóa tin!';
        }
    } else {
        $_SESSION['error'] = 'Tin không tồn tại!';
    }
}

header('Location: ../posts.php');
?>
