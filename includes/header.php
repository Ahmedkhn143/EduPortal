<?php
// Root folder ka path set karein (Agar aapke folder ka naam kuch aur hai to yahan badal len)
$base_url = "/EduPortal"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPortal | System</title>
    <!-- CSS File Link -->
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>

<div class="wrapper">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h2 style="color: white; margin: 0;">EduPortal</h2>
        </div>
        <ul class="nav-links">
            <li><a href="<?= $base_url ?>/dashboard.php">📊 Dashboard</a></li>
            <li><a href="<?= $base_url ?>/modules/students/index.php">🎓 Students</a></li>
            <li><a href="<?= $base_url ?>/modules/courses/index.php">📚 Courses</a></li>
            <li><a href="<?= $base_url ?>/modules/attendance/index.php">📅 Attendance</a></li>
            <li style="margin-top: 50px;"><a href="<?= $base_url ?>/logout.php" class="logout-link" style="color: #e74c3c;">🚪 Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <header class="top-nav">
            <div class="user-info">
                <span>Welcome, <strong><?= isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin' ?></strong></span>
            </div>
        </header>
        <div class="content-body">