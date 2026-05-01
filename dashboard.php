<?php
session_start();

// 1. Security Check: Agar user login nahi hai, to wapis bhej do
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Database Connection Include
include 'config/db.php';

// 3. Stats Fetching (Logic)
try {
    // Total Students Count
    $total_students = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();

    // Total Courses Count
    $total_courses = $conn->query("SELECT COUNT(*) FROM courses")->fetchColumn();

    // Today's Registrations (Sirf aaj ki date wale)
    $stmt = $conn->query("SELECT COUNT(*) FROM students WHERE DATE(created_at) = CURDATE()");
    $today_regs = $stmt->fetchColumn();
} catch (PDOException $e) {
    $error = "Stats load nahi ho sakay: " . $e->getMessage();
}

// 4. Header Include
include 'includes/header.php'; 
?>

<!-- Dashboard Main UI -->
<div class="dashboard-wrapper">
    
    <!-- Welcome Section -->
    <div class="dashboard-header" style="margin-bottom: 30px;">
        <h1 style="color: #2c3e50;">Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>! 👋</h1>
        <p style="color: #7f8c8d;">EduPortal ke main administration panel mein aapka khush-amdeed.</p>
    </div>

    <!-- Error Display (Agar koi SQL masla ho) -->
    <?php if(isset($error)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <?= $error; ?>
        </div>
    <?php endif; ?>

    <!-- Statistics Grid -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        
        <!-- Total Students Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 6px solid #3498db;">
            <h3 style="font-size: 14px; color: #95a5a6; text-transform: uppercase; margin-bottom: 10px;">Total Students</h3>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 36px; font-weight: bold; color: #2c3e50;"><?= $total_students; ?></span>
                <span style="font-size: 24px;">👥</span>
            </div>
        </div>

        <!-- Active Courses Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 6px solid #9b59b6;">
            <h3 style="font-size: 14px; color: #95a5a6; text-transform: uppercase; margin-bottom: 10px;">Active Courses</h3>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 36px; font-weight: bold; color: #2c3e50;"><?= $total_courses; ?></span>
                <span style="font-size: 24px;">📚</span>
            </div>
        </div>

        <!-- New Registrations Today Card -->
        <div class="stat-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 6px solid #2ecc71;">
            <h3 style="font-size: 14px; color: #95a5a6; text-transform: uppercase; margin-bottom: 10px;">New Today</h3>
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <span style="font-size: 36px; font-weight: bold; color: #2c3e50;"><?= $today_regs; ?></span>
                <span style="font-size: 24px;">⭐</span>
            </div>
        </div>

    </div>

    <!-- System Info Section -->
    <div style="margin-top: 40px; background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #edf2f7;">
        <h3 style="color: #4a5568; margin-bottom: 15px; font-size: 18px;">Quick Actions & Status</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="modules/students/index.php" style="background: #3498db; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: bold;">Manage Students</a>
            <div style="padding: 10px 20px; background: #f8f9fa; border-radius: 6px; font-size: 14px; color: #7f8c8d;">
                <strong>Server Status:</strong> <span style="color: #27ae60;">Online</span>
            </div>
            <div style="padding: 10px 20px; background: #f8f9fa; border-radius: 6px; font-size: 14px; color: #7f8c8d;">
                <strong>Last Sync:</strong> <?= date('d M, Y | h:i A'); ?>
            </div>
        </div>
    </div>

</div>

<?php 
// 5. Footer Include
include 'includes/footer.php'; 
?>