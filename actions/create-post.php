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
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validate
        if (!in_array($file_ext, $allowed)) {
            $_SESSION['error'] = 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF!';
            header('Location: ../post.php');
            exit;
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            $_SESSION['error'] = 'Kích thước file không được vượt quá 5MB!';
            header('Location: ../post.php');
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
            $image_path = "uploads/$year/$month/$new_filename";
        } else {
            $_SESSION['error'] = 'Có lỗi khi upload ảnh!';
            header('Location: ../post.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Vui lòng chọn ảnh!';
        header('Location: ../post.php');
        exit;
    }
    
    // Insert to database
    $lat_sql = $latitude !== null ? $latitude : 'NULL';
    $lng_sql = $longitude !== null ? $longitude : 'NULL';
    
    $query = "INSERT INTO motels (user_id, title, description, price, area, address, district_id, category_id, phone, utilities, latitude, longitude, image, approve, count_view, created_at) 
              VALUES ($user_id, '$title', '$description', $price, $area, '$address', $district_id, $category_id, '$phone', '$utilities', $lat_sql, $lng_sql, '$image_path', 0, 0, NOW())";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Đăng tin thành công! Tin của bạn đang chờ duyệt.';
        header('Location: ../my-posts.php');
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra: ' . mysqli_error($conn);
        header('Location: ../post.php');
    }
} else {
    header('Location: ../post.php');
}
?>
