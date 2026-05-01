<?php
session_start();
include '../../config/db.php';

// 1. Security Guard
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); exit();
}

// Variables
$id = 0; $name = ""; $email = ""; $spec = ""; $phone = ""; $update = false;

// --- 2. DELETE LOGIC ---
if (isset($_GET['del'])) {
    $conn->prepare("DELETE FROM teachers WHERE id=?")->execute([$_GET['del']]);
    header("Location: index.php");
}

// --- 3. SAVE & UPDATE LOGIC ---
if (isset($_POST['save'])) {
    $n = $_POST['name'];
    $e = $_POST['email'];
    $s = $_POST['specialization'];
    $p = $_POST['phone'];

    if ($_POST['id'] != 0) {
        $sql = "UPDATE teachers SET name=?, email=?, specialization=?, phone=? WHERE id=?";
        $conn->prepare($sql)->execute([$n, $e, $s, $p, $_POST['id']]);
    } else {
        $sql = "INSERT INTO teachers (name, email, specialization, phone) VALUES (?, ?, ?, ?)";
        $conn->prepare($sql)->execute([$n, $e, $s, $p]);
    }
    header("Location: index.php");
}

// --- 4. EDIT LOGIC ---
if (isset($_GET['edit'])) {
    $update = true;
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $row = $stmt->fetch();
    
    $id = $row['id'];
    $name = $row['name'];
    $email = $row['email'];
    $spec = $row['specialization'];
    $phone = $row['phone'];
}

// --- 5. VIEW LOGIC ---
$teachers = $conn->query("SELECT * FROM teachers ORDER BY id DESC")->fetchAll();

include '../../includes/header.php';
?>

<div class="module-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
    <h2 style="color: #2c3e50; border-bottom: 3px solid #e67e22; display: inline-block; padding-bottom: 5px; margin-bottom: 25px;">
        <?= $update ? "Edit Teacher Info" : "Register New Teacher" ?>
    </h2>

    <!-- Teacher Form -->
    <form method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; background: #fffcf9; padding: 20px; border-radius: 10px; border: 1px solid #ffe8d6; margin-bottom: 40px;">
        <input type="hidden" name="id" value="<?= $id ?>">
        
        <input type="text" name="name" placeholder="Teacher Full Name" value="<?= $name ?>" required style="padding: 11px; border: 1px solid #ddd; border-radius: 8px;">
        <input type="email" name="email" placeholder="Email Address" value="<?= $email ?>" required style="padding: 11px; border: 1px solid #ddd; border-radius: 8px;">
        <input type="text" name="specialization" placeholder="Expertise (e.g. Physics)" value="<?= $spec ?>" required style="padding: 11px; border: 1px solid #ddd; border-radius: 8px;">
        <input type="text" name="phone" placeholder="Phone No." value="<?= $phone ?>" style="padding: 11px; border: 1px solid #ddd; border-radius: 8px;">
        
        <button type="submit" name="save" style="background: #e67e22; color: white; border: none; padding: 11px; border-radius: 8px; cursor: pointer; font-weight: bold;">
            <?= $update ? "Update Teacher" : "Add Teacher" ?>
        </button>
    </form>

    <!-- Teachers Table -->
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left;">
                <th style="padding: 15px;">Teacher Name</th>
                <th style="padding: 15px;">Expertise</th>
                <th style="padding: 15px;">Contact</th>
                <th style="padding: 15px; text-align: center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teachers as $t): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 15px;"><strong><?= htmlspecialchars($t['name']) ?></strong><br><small><?= $t['email'] ?></small></td>
                <td style="padding: 15px;"><span style="background: #fff3e0; color: #e67e22; padding: 3px 10px; border-radius: 15px; font-size: 12px;"><?= $t['specialization'] ?></span></td>
                <td style="padding: 15px; color: #666;"><?= $t['phone'] ?></td>
                <td style="padding: 15px; text-align: center;">
                    <a href="index.php?edit=<?= $t['id'] ?>" style="color: #3498db; text-decoration: none; margin-right: 10px;">Edit</a>
                    <a href="index.php?del=<?= $t['id'] ?>" style="color: #e74c3c; text-decoration: none;" onclick="return confirm('Remove this teacher?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>