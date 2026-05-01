<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php"); exit();
}

// 1. FETCH ALL STUDENTS (Dropdown ke liye)
$students_list = $conn->query("SELECT id, name FROM students ORDER BY name ASC")->fetchAll();

// 2. SAVE FEE LOGIC
if (isset($_POST['pay_fee'])) {
    $s_id = $_POST['student_id'];
    $amt = $_POST['amount'];
    $month = $_POST['fee_month'];
    $p_date = date('Y-m-d');

    $sql = "INSERT INTO fees (student_id, amount, fee_month, payment_date) VALUES (?, ?, ?, ?)";
    $conn->prepare($sql)->execute([$s_id, $amt, $month, $p_date]);
    header("Location: index.php?success=1");
}

// 3. FETCH RECENT PAYMENTS
$payments = $conn->query("SELECT f.*, s.name FROM fees f JOIN students s ON f.student_id = s.id ORDER BY f.id DESC LIMIT 10")->fetchAll();

include '../../includes/header.php';
?>

<div class="module-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
    <h2>Fees Management</h2>
    
    <!-- Fee Payment Form -->
    <form method="POST" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        <div>
            <label>Select Student</label>
            <select name="student_id" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                <option value="">-- Choose Student --</option>
                <?php foreach($students_list as $sl): ?>
                    <option value="<?= $sl['id'] ?>"><?= $sl['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Amount (PKR)</label>
            <input type="number" name="amount" placeholder="e.g. 5000" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
        </div>
        <div>
            <label>Fee Month</label>
            <select name="fee_month" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
            </select>
        </div>
        <div style="display: flex; align-items: flex-end;">
            <button type="submit" name="pay_fee" style="background: #27ae60; color: white; border: none; padding: 11px; width: 100%; border-radius: 5px; cursor: pointer; font-weight: bold;">Submit Payment</button>
        </div>
    </form>

    <!-- Recent Payments Table -->
    <h3>Recent Transactions</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead>
            <tr style="background: #34495e; color: white; text-align: left;">
                <th style="padding: 12px;">Student</th>
                <th style="padding: 12px;">Amount</th>
                <th style="padding: 12px;">Month</th>
                <th style="padding: 12px;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($payments as $p): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px;"><?= $p['name'] ?></td>
                <td style="padding: 12px;"><strong>PKR <?= $p['amount'] ?></strong></td>
                <td style="padding: 12px;"><?= $p['fee_month'] ?></td>
                <td style="padding: 12px; font-size: 13px; color: #666;"><?= $p['payment_date'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>