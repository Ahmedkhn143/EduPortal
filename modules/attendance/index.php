<?php
session_start();
include '../../config/db.php';
include '../../includes/functions.php';

// Security Check (Using your new functions.php logic)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$today = date('Y-m-d');
$message = "";

// --- 1. SAVE ATTENDANCE LOGIC ---
if (isset($_POST['mark_attendance'])) {
    $att_data = $_POST['status']; // Array: [student_id => status]
    
    try {
        foreach ($att_data as $s_id => $status) {
            // ON DUPLICATE KEY UPDATE: Agar aaj ki attendance lag chuki hai to update hogi, warna insert.
            $sql = "INSERT INTO attendance (student_id, attendance_date, status) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE status = ?";
            $conn->prepare($sql)->execute([$s_id, $today, $status, $status]);
        }
        $message = "Attendance marked successfully for " . date('d M, Y');
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// --- 2. FETCH STUDENTS & TODAY'S ATTENDANCE STATUS ---
// LEFT JOIN is liye taake jo student aaj absent ya present nahi mark hue, wo bhi nazar aayein
$query = "SELECT s.id, s.name, a.status 
          FROM students s 
          LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date = '$today'
          ORDER BY s.name ASC";
$students = $conn->query($query)->fetchAll();

include '../../includes/header.php';
?>

<div class="module-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #1e293b;">Daily Attendance</h2>
        <div style="background: #e2e8f0; padding: 8px 15px; border-radius: 8px; font-weight: 600; font-size: 14px; color: #475569;">
            📅 Today: <?= date('d M, Y'); ?>
        </div>
    </div>

    <?php if($message): ?>
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            ✅ <?= $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th style="text-align: center;">Status (Present / Absent)</th>
                </tr>
            </thead>
            <tbody>
                <?php if($students): ?>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td style="font-weight: 500; color: #334155;"><?= htmlspecialchars($s['name']) ?></td>
                        <td style="text-align: center;">
                            <div style="display: inline-flex; gap: 20px;">
                                <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                    <input type="radio" name="status[<?= $s['id'] ?>]" value="Present" 
                                           <?= ($s['status'] == 'Present') ? 'checked' : '' ?> required> 
                                    <span style="color: #16a34a; font-weight: 700;">P</span>
                                </label>
                                <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                    <input type="radio" name="status[<?= $s['id'] ?>]" value="Absent" 
                                           <?= ($s['status'] == 'Absent') ? 'checked' : '' ?> required> 
                                    <span style="color: #dc2626; font-weight: 700;">A</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 40px; color: #94a3b8;">
                            Koi student register nahi hai. Pehle students add karein.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if($students): ?>
        <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
            <button type="submit" name="mark_attendance" 
                    style="background: #3b82f6; color: white; border: none; padding: 12px 35px; border-radius: 8px; cursor: pointer; font-weight: 700; transition: 0.3s;">
                Save Attendance
            </button>
        </div>
        <?php endif; ?>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>