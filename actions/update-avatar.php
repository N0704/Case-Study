<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include('../config/db.php');

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['avatar'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    // Validate
    if (!in_array($file_ext, $allowed)) {
        $_SESSION['error'] = 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF!';
        header('Location: ../profile.php');
        exit;
    }
    
    if ($file['size'] > 2 * 1024 * 1024) { // 2MB
        $_SESSION['error'] = 'Kích thước file không được vượt quá 2MB!';
        header('Location: ../profile.php');
        exit;
    }
    
    // Create upload directory
    $upload_dir = "../uploads/avatars/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        // Get old avatar
        $old_query = mysqli_query($conn, "SELECT avatar FROM users WHERE id = $user_id");
        $old_user = mysqli_fetch_assoc($old_query);
        
        // Delete old avatar if it's not from UI Avatars
        if (!empty($old_user['avatar']) && strpos($old_user['avatar'], 'ui-avatars.com') === false) {
            if (file_exists('../' . $old_user['avatar'])) {
                unlink('../' . $old_user['avatar']);
            }
        }
        
        $avatar_path = "uploads/avatars/$new_filename";
        
        // Update database
        if (mysqli_query($conn, "UPDATE users SET avatar = '$avatar_path' WHERE id = $user_id")) {
            $_SESSION['avatar'] = $avatar_path;
            $_SESSION['success'] = 'Cập nhật avatar thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi khi cập nhật avatar!';
        }
    } else {
        $_SESSION['error'] = 'Có lỗi khi upload ảnh!';
    }
} else {
    $_SESSION['error'] = 'Vui lòng chọn ảnh!';
}

header('Location: ../profile.php');
?>
