<?php
session_start();
// Path: 2 folders peeche config file maujood hai
include '../../config/db.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Variables for Form
$id = 0; $name = ""; $email = ""; $course = ""; $phone = ""; $update = false;

// --- 2. DELETE LOGIC ---
if (isset($_GET['del'])) {
    $conn->prepare("DELETE FROM students WHERE id=?")->execute([$_GET['del']]);
    header("Location: index.php");
}
// --- 3. SAVE & UPDATE LOGIC (Updated with Duplicate Check) ---
if (isset($_POST['save'])) {
    $n = $_POST['name'];
    $e = $_POST['email'];
    $c = $_POST['course'];
    $p = $_POST['phone'];
    $id = $_POST['id'];

    try {
        if ($id != 0) {
            // Update Query
            $sql = "UPDATE students SET name=?, email=?, course=?, phone=? WHERE id=?";
            $conn->prepare($sql)->execute([$n, $e, $c, $p, $id]);
        } else {
            // Insert Query
            $sql = "INSERT INTO students (name, email, course, phone) VALUES (?, ?, ?, ?)";
            $conn->prepare($sql)->execute([$n, $e, $c, $p]);
        }
        header("Location: index.php");
    } catch (PDOException $ex) {
        // Agar email pehle se maujood ho
        if ($ex->getCode() == 23000) {
            echo "<script>alert('Error: Ye Email pehle se register hai!'); window.location='index.php';</script>";
        } else {
            echo "Error: " . $ex->getMessage();
        }
    }
}

// --- 4. EDIT LOGIC (Form fill karne ke liye) ---
if (isset($_GET['edit'])) {
    $update = true;
    $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $row = $stmt->fetch();
    
    $id = $row['id'];
    $name = $row['name'];
    $email = $row['email'];
    $course = $row['course'];
    $phone = $row['phone'];
}

// --- 5. FETCH ALL COURSES (Dropdown ke liye) ---
$courses_list = $conn->query("SELECT * FROM courses ORDER BY course_name ASC")->fetchAll();

// --- 6. FETCH STUDENTS (Search logic ke sath) ---
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? OR email LIKE ? ORDER BY id DESC");
    $stmt->execute(["%$search%", "%$search%"]);
    $students = $stmt->fetchAll();
} else {
    $students = $conn->query("SELECT * FROM students ORDER BY id DESC")->fetchAll();
}

// UI Start
include '../../includes/header.php';
?>

<div class="module-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="color: #2c3e50; border-left: 5px solid #3498db; padding-left: 15px;">
            <?= $update ? "Edit Student" : "Student Registration" ?>
        </h2>
        
        <!-- Search Bar -->
        <form method="GET" style="display: flex; gap: 5px;">
            <input type="text" name="search" placeholder="Search students..." value="<?= htmlspecialchars($search) ?>" 
                   style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; width: 200px;">
            <button type="submit" style="background: #34495e; color: white; border: none; padding: 10px 15px; border-radius: 8px; cursor: pointer;">🔍</button>
            <?php if($search != ""): ?>
                <a href="index.php" style="background: #eee; padding: 10px; border-radius: 8px; text-decoration: none; color: #333;">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Registration Form -->
    <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 40px;">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <div class="input-group">
            <label style="display: block; font-size: 13px; margin-bottom: 5px; color: #666;">Full Name</label>
            <input type="text" name="name" value="<?= $name ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
        </div>

        <div class="input-group">
            <label style="display: block; font-size: 13px; margin-bottom: 5px; color: #666;">Email Address</label>
            <input type="email" name="email" value="<?= $email ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
        </div>

        <div class="input-group">
            <label style="display: block; font-size: 13px; margin-bottom: 5px; color: #666;">Select Course</label>
            <select name="course" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                <option value="">-- Choose Course --</option>
                <?php foreach ($courses_list as $c): ?>
                    <option value="<?= $c['course_name'] ?>" <?= ($course == $c['course_name']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="input-group">
            <label style="display: block; font-size: 13px; margin-bottom: 5px; color: #666;">Phone No.</label>
            <input type="text" name="phone" value="<?= $phone ?>" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
        </div>

        <div style="display: flex; align-items: flex-end;">
            <button type="submit" name="save" style="width: 100%; padding: 11px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
                <?= $update ? "Update Data" : "Register Student" ?>
            </button>
        </div>
    </form>

    <!-- Students Table -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="background: #f1f3f5; text-align: left;">
                    <th style="padding: 15px; color: #495057;">Student Name</th>
                    <th style="padding: 15px; color: #495057;">Email</th>
                    <th style="padding: 15px; color: #495057;">Enrolled Course</th>
                    <th style="padding: 15px; color: #495057; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if($students): ?>
                    <?php foreach ($students as $s): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px; font-weight: 500;"><?= htmlspecialchars($s['name']) ?></td>
                        <td style="padding: 15px; color: #666;"><?= htmlspecialchars($s['email']) ?></td>
                        <td style="padding: 15px;"><span style="background: #e3f2fd; color: #1976d2; padding: 4px 10px; border-radius: 20px; font-size: 12px;"><?= htmlspecialchars($s['course']) ?></span></td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="index.php?edit=<?= $s['id'] ?>" style="color: #3498db; text-decoration: none; font-weight: 600; margin-right: 15px;">Edit</a>
                            <a href="index.php?del=<?= $s['id'] ?>" style="color: #e74c3c; text-decoration: none; font-weight: 600;" onclick="return confirm('Kya aap is student ko remove karna chahte hain?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="padding: 30px; text-align: center; color: #999;">Koi student nahi mila.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>