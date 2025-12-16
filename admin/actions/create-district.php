<?php
session_start();

// Check admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header('Location: ../../login.php');
    exit;
}

include('../../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    
    // Validate
    if (empty($name)) {
        $_SESSION['error'] = 'Tên khu vực không được để trống!';
        header('Location: ../districts.php');
        exit;
    }
    
    // Check duplicate name
    $check = mysqli_query($conn, "SELECT id FROM districts WHERE name = '$name' LIMIT 1");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['error'] = 'Tên khu vực đã tồn tại!';
        header('Location: ../districts.php');
        exit;
    }
    
    // Insert
    $lat_sql = $latitude !== null ? $latitude : 'NULL';
    $lng_sql = $longitude !== null ? $longitude : 'NULL';
    
    $query = "INSERT INTO districts (name, latitude, longitude) VALUES ('$name', $lat_sql, $lng_sql)";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = 'Thêm khu vực thành công!';
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra: ' . mysqli_error($conn);
    }
}

header('Location: ../districts.php');
?>
