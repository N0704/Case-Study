<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $id = (int)$_POST['id'];
    
    // Check ownership
    $check = mysqli_query($conn, "SELECT user_id, image FROM motels WHERE id = $id");
    if (!$check || mysqli_num_rows($check) == 0) {
        $_SESSION['error'] = 'Tin không tồn tại!';
        header('Location: ../my-posts.php');
        exit;
    }
    
    $current = mysqli_fetch_assoc($check);
    if ($current['user_id'] != $user_id) {
        $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa tin này!';
        header('Location: ../my-posts.php');
        exit;
    }
    
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (int)$_POST['price'];
    $area = (float)$_POST['area'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $district_id = (int)$_POST['district_id'];
    $category_id = (int)$_POST['category_id'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $utilities = mysqli_real_escape_string($conn, $_POST['utilities'] ?? '');
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    
    // Handle image upload (optional)
    $image_path = $current['image']; // Keep current image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validate
        if (!in_array($file_ext, $allowed)) {
            $_SESSION['error'] = 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF!';
            header('Location: ../edit-post.php?id=' . $id);
            exit;
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            $_SESSION['error'] = 'Kích thước file không được vượt quá 5MB!';
            header('Location: ../edit-post.php?id=' . $id);
            exit;
        }
        
        // Create upload directory
        $year = date('Y');
        $month = date('m');
        $upload_dir = "../uploads/$year/$month/";
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $new_filename = uniqid() . '_' . time() . '.' . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Delete old image
            if (!empty($current['image']) && file_exists('../' . $current['image'])) {
                unlink('../' . $current['image']);
            }
            
            $image_path = "uploads/$year/$month/$new_filename";
        } else {
            $_SESSION['error'] = 'Có lỗi khi upload ảnh!';
            header('Location: ../edit-post.php?id=' . $id);
            exit;
        }
    }
    
    // Update database
    $lat_sql = $latitude !== null ? $latitude : 'NULL';
    $lng_sql = $longitude !== null ? $longitude : 'NULL';
    
    $query = "UPDATE motels SET 
              title = '$title',
              description = '$description',
              price = $price,
              area = $area,
              address = '$address',
              district_id = $district_id,
              category_id = $category_id,
              phone = '$phone',
              utilities = '$utilities',
              latitude = $lat_sql,
              longitude = $lng_sql,
              image = '$image_path'
              WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Cập nhật tin thành công!';
        header('Location: ../my-posts.php');
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra: ' . mysqli_error($conn);
        header('Location: ../edit-post.php?id=' . $id);
    }
} else {
    header('Location: ../my-posts.php');
}
?>
