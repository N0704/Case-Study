<?php
session_start();

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../../login.php');
    exit;
}

include('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    
    // Validate
    if (empty($name)) {
        $_SESSION['error'] = 'Tên khu vực không được để trống!';
        header('Location: ../districts.php');
        exit;
    }
    
    // Check duplicate name (exclude current)
    $check = mysqli_query($conn, "SELECT id FROM districts WHERE name = '$name' AND id != $id LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = 'Tên khu vực đã tồn tại!';
        header('Location: ../districts.php');
        exit;
    }
    
    // Update
    $lat_sql = $latitude !== null ? $latitude : 'NULL';
    $lng_sql = $longitude !== null ? $longitude : 'NULL';
    
    $query = "UPDATE districts SET name = '$name', latitude = $lat_sql, longitude = $lng_sql WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Cập nhật khu vực thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra: ' . mysqli_error($conn);
    }
}

header('Location: ../districts.php');
?>
