<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); exit();
}

$today = date('Y-m-d');
$message = "";

// --- 1. SAVE ATTENDANCE LOGIC ---
if (isset($_POST['mark_attendance'])) {
    $att_data = $_POST['status']; // Array of statuses [student_id => status]
    
    try {
        foreach ($att_data as $s_id => $status) {
            // INSERT or UPDATE logic (Duplicate entry handle karne ke liye)
            $sql = "INSERT INTO attendance (student_id, attendance_date, status) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE status = ?";
            $conn->prepare($sql)->execute([$s_id, $today, $status, $status]);
        }
        $message = "Attendance successfully marked for today!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// --- 2. FETCH STUDENTS & TODAY'S ATTENDANCE ---
$students = $conn->query("SELECT s.id, s.name, a.status 
                          FROM students s 
                          LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date = '$today'")
                  ->fetchAll();

include '../../includes/header.php';
?>

<div class="module-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Daily Attendance</h2>
        <span style="background: #34495e; color: white; padding: 5px 15px; border-radius: 20px; font-size: 14px;">
            Date: <strong><?= date('d M, Y'); ?></strong>
        </span>
    </div>

    <?php if($message): ?>
        <p style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;"><?= $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 15px; text-align: left;">Student Name</th>
                    <th style="padding: 15px; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 500;"><?= htmlspecialchars($s['name']) ?></td>
                    <td style="padding: 15px; text-align: center;">
                        <label style="margin-right: 15px; cursor: pointer;">
                            <input type="radio" name="status[<?= $s['id'] ?>]" value="Present" 
                                   <?= ($s['status'] == 'Present') ? 'checked' : '' ?> required> 
                            <span style="color: #27ae60; font-weight: bold;">P</span>
                        </label>
                        <label style="cursor: pointer;">
                            <input type="radio" name="status[<?= $s['id'] ?>]" value="Absent" 
                                   <?= ($s['status'] == 'Absent') ? 'checked' : '' ?> required> 
                            <span style="color: #e74c3c; font-weight: bold;">A</span>
                        </label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right;">
            <button type="submit" name="mark_attendance" style="background: #2c3e50; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: bold;">
                Submit Attendance
            </button>
        </div>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>