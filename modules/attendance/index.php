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
$selected_date = $_GET['date'] ?? $today;
$selected_date = trim($selected_date);
$message = "";

$date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);
if (!$date_obj || $date_obj->format('Y-m-d') !== $selected_date) {
    $selected_date = $today;
}

// --- 1. SAVE ATTENDANCE LOGIC ---
if (isset($_POST['mark_attendance'])) {
    $selected_date = trim($_POST['attendance_date'] ?? $today);
    $date_obj = DateTime::createFromFormat('Y-m-d', $selected_date);

    if (!$date_obj || $date_obj->format('Y-m-d') !== $selected_date) {
        $selected_date = $today;
        $message = "Invalid date selected.";
    } else {
        $att_data = $_POST['status'] ?? [];

        if (!is_array($att_data) || count($att_data) === 0) {
            $message = "No students found to mark attendance.";
        } else {
            try {
                foreach ($att_data as $s_id => $status) {
                    // ON DUPLICATE KEY UPDATE: Agar date par attendance lag chuki ho to update hogi.
                    $sql = "INSERT INTO attendance (student_id, attendance_date, status) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE status = ?";
                    $conn->prepare($sql)->execute([$s_id, $selected_date, $status, $status]);
                }
                $message = "Attendance marked successfully for " . date('d M, Y', strtotime($selected_date));
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    }
}

// --- 2. FETCH STUDENTS & TODAY'S ATTENDANCE STATUS ---
// LEFT JOIN is liye taake jo student aaj absent ya present nahi mark hue, wo bhi nazar aayein
$query = "SELECT s.id, s.name, a.status 
          FROM students s 
          LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date = ?
          ORDER BY s.name ASC";
$stmt = $conn->prepare($query);
$stmt->execute([$selected_date]);
$students = $stmt->fetchAll();

// --- 3. ATTENDANCE HISTORY (DATE-WISE) ---
$history_query = "SELECT attendance_date,
                         SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present_count,
                         SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent_count,
                         COUNT(*) AS total_count
                  FROM attendance
                  GROUP BY attendance_date
                  ORDER BY attendance_date DESC
                  LIMIT 30";
$attendance_history = $conn->query($history_query)->fetchAll();

include '../../includes/header.php';
?>

<div class="module-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #1e293b;">Attendance (Date-wise)</h2>
        <div style="background: #e2e8f0; padding: 8px 15px; border-radius: 8px; font-weight: 600; font-size: 14px; color: #475569;">
            Selected: <?= date('d M, Y', strtotime($selected_date)); ?>
        </div>
    </div>

    <?php if($message): ?>
        <div style="padding: 15px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: 500;">
            ✅ <?= $message; ?>
        </div>
    <?php endif; ?>

    <form method="GET" style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 20px;">
        <div style="display: flex; flex-direction: column; gap: 6px;">
            <label style="font-size: 12px; color: #64748b;">Attendance Date</label>
            <input type="date" name="date" value="<?= htmlspecialchars($selected_date) ?>" required
                   style="padding: 8px 10px; border: 1px solid #e2e8f0; border-radius: 8px;">
        </div>
        <button type="submit" style="background: #334155; color: white; border: none; padding: 10px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">
            Load Date
        </button>
        <?php if ($selected_date !== $today): ?>
            <a href="index.php" style="padding: 10px 12px; border-radius: 8px; background: #e2e8f0; text-decoration: none; color: #334155; font-weight: 600;">Today</a>
        <?php endif; ?>
    </form>

    <form method="POST">
        <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($selected_date) ?>">
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

    <div style="margin-top: 35px;">
        <h3 style="margin-bottom: 15px; color: #0f172a;">Saved Attendance (Date-wise)</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                <thead>
                    <tr style="background: #f8fafc; text-align: left;">
                        <th style="padding: 12px; color: #475569;">Date</th>
                        <th style="padding: 12px; color: #475569;">Present</th>
                        <th style="padding: 12px; color: #475569;">Absent</th>
                        <th style="padding: 12px; color: #475569;">Total</th>
                        <th style="padding: 12px; color: #475569; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attendance_history): ?>
                        <?php foreach ($attendance_history as $row): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px; font-weight: 600; color: #1e293b;">
                                    <?= date('d M, Y', strtotime($row['attendance_date'])); ?>
                                </td>
                                <td style="padding: 12px; color: #16a34a; font-weight: 600;">
                                    <?= (int)$row['present_count']; ?>
                                </td>
                                <td style="padding: 12px; color: #dc2626; font-weight: 600;">
                                    <?= (int)$row['absent_count']; ?>
                                </td>
                                <td style="padding: 12px; color: #0f172a; font-weight: 600;">
                                    <?= (int)$row['total_count']; ?>
                                </td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="index.php?date=<?= htmlspecialchars($row['attendance_date']) ?>"
                                       style="padding: 6px 12px; background: #e2e8f0; border-radius: 6px; text-decoration: none; color: #334155; font-weight: 600;">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="padding: 20px; text-align: center; color: #94a3b8;">
                                No attendance records saved yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>