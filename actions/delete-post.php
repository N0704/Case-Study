<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include('../config/db.php');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Check ownership
    $check = mysqli_query($conn, "SELECT user_id, image FROM motels WHERE id = $id");
    if ($check && mysqli_num_rows($check) > 0) {
        $post = mysqli_fetch_assoc($check);
        
        if ($post['user_id'] == $user_id) {
            // Delete image file
            if (!empty($post['image']) && file_exists('../' . $post['image'])) {
                unlink('../' . $post['image']);
            }
            
            // Delete from database
            if (mysqli_query($conn, "DELETE FROM motels WHERE id = $id")) {
                $_SESSION['success'] = 'Xóa tin thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi khi xóa tin!';
            }
        } else {
            $_SESSION['error'] = 'Bạn không có quyền xóa tin này!';
        }
    } else {
        $_SESSION['error'] = 'Tin không tồn tại!';
    }
}

header('Location: ../my-posts.php');
?>
