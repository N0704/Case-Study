<?php
session_start();
include('../../config/db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = (int)$_POST['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (int)$_POST['price'];
    $area = (float)$_POST['area'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $district_id = (int)$_POST['district_id'];
    $category_id = (int)$_POST['category_id'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $utilities = mysqli_real_escape_string($conn, $_POST['utilities']);
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Handle Image Upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../assets/uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = "assets/uploads/" . $new_filename;
        }
    }

    $query = "INSERT INTO motels (user_id, category_id, district_id, title, description, price, area, address, latitude, longitude, image, utilities, phone, approve, created_at) 
              VALUES ($user_id, $category_id, $district_id, '$title', '$description', $price, $area, '$address', '$latitude', '$longitude', '$image_path', '$utilities', '$phone', 1, NOW())";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Đăng tin thành công!';
    } else {
        $_SESSION['error'] = 'Lỗi: ' . mysqli_error($conn);
    }
    
    header('Location: ../posts.php');
    exit;
}
?>
