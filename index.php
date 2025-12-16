<?php if (!isset($_SESSION)) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/output.css">
</head>
<body>
    <?php include('config/db.php'); ?>
    <?php include('components/home/header.php'); ?>
    <main class="bg-gray-100 h-full pb-8">
    <?php include('components/home/hero.php'); ?>
    <?php include('components/home/latest.php'); ?>
    <?php include('components/home/most_viewed.php'); ?>
    <?php include('components/home/near_vu.php'); ?>
    </main>
    <?php include('components/footer.php'); ?>
</body>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/lucide@latest"></script>
  <script>
    lucide.createIcons();
  </script>
</html>