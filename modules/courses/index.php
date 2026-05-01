<?php
session_start();
// Path: Do folders peeche ja kar config aur includes milenge
include '../../config/db.php';

// Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Variables
$id = 0; $course_name = ""; $duration = ""; $update = false;

// --- 1. SAVE & UPDATE LOGIC ---
if (isset($_POST['save'])) {
    $cn = $_POST['course_name'];
    $dr = $_POST['duration'];

    if ($_POST['id'] != 0) {
        // Update Query
        $sql = "UPDATE courses SET course_name=?, duration=? WHERE id=?";
        $conn->prepare($sql)->execute([$cn, $dr, $_POST['id']]);
    } else {
        // Insert Query
        $sql = "INSERT INTO courses (course_name, duration) VALUES (?, ?)";
        $conn->prepare($sql)->execute([$cn, $dr]);
    }
    header("Location: index.php");
}

// --- 2. DELETE LOGIC ---
if (isset($_GET['del'])) {
    $conn->prepare("DELETE FROM courses WHERE id=?")->execute([$_GET['del']]);
    header("Location: index.php");
}

// --- 3. EDIT LOGIC (Fetch for form) ---
if (isset($_GET['edit'])) {
    $update = true;
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $row = $stmt->fetch();
    
    $id = $row['id'];
    $course_name = $row['course_name'];
    $duration = $row['duration'];
}

// --- 4. VIEW LOGIC ---
$courses = $conn->query("SELECT * FROM courses ORDER BY id DESC")->fetchAll();

include '../../includes/header.php';
?>

<div class="module-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <h2 style="margin-bottom: 20px; color: #2c3e50; border-bottom: 2px solid #9b59b6; display: inline-block; padding-bottom: 5px;">
        <?= $update ? "Edit Course Info" : "Add New Course" ?>
    </h2>

    <!-- Course Form -->
    <form method="POST" style="margin-bottom: 40px; display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="text" name="course_name" placeholder="Course Name (e.g. Web Development)" value="<?= $course_name ?>" required style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; flex: 2;">
        <input type="text" name="duration" placeholder="Duration (e.g. 6 Months)" value="<?= $duration ?>" required style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
        
        <button type="submit" name="save" style="background: <?= $update ? '#9b59b6' : '#8e44ad' ?>; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold;">
            <?= $update ? "Update Course" : "Save Course" ?>
        </button>
        <?php if($update): ?>
            <a href="index.php" style="padding: 12px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 8px; font-weight: bold;">Cancel</a>
        <?php endif; ?>
    </form>

    <!-- Courses Table -->
    <h3 style="margin-bottom: 15px; color: #7f8c8d;">Available Courses</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8f9fa;">
            <tr>
                <th style="text-align: left; padding: 15px; border-bottom: 2px solid #dee2e6;">Course Name</th>
                <th style="text-align: left; padding: 15px; border-bottom: 2px solid #dee2e6;">Duration</th>
                <th style="text-align: left; padding: 15px; border-bottom: 2px solid #dee2e6;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if($courses): ?>
                <?php foreach ($courses as $c): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px; font-weight: 500;"><?= htmlspecialchars($c['course_name']) ?></td>
                    <td style="padding: 15px;"><?= htmlspecialchars($c['duration']) ?></td>
                    <td style="padding: 15px;">
                        <a href="index.php?edit=<?= $c['id'] ?>" style="color: #9b59b6; text-decoration: none; font-weight: bold; margin-right: 15px;">Edit</a>
                        <a href="index.php?del=<?= $c['id'] ?>" style="color: #e74c3c; text-decoration: none; font-weight: bold;" onclick="return confirm('Is course ko delete kar dein?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" style="padding: 20px; text-align: center; color: #999;">Abhi tak koi course add nahi kiya gaya.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>