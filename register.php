<?php
include 'config/db.php';

if (isset($_POST['register'])) {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    // Password Hashing: Password ko secure banayein
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
        $conn->prepare($sql)->execute([$name, $email, $pass]);
        header("Location: login.php?msg=Account Created Successfully");
    } catch (PDOException $e) {
        $error = "Registration Failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | EduPortal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 6px; }
        button { width: 100%; padding: 12px; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body style="background: #f1f5f9;">
    <div class="auth-container">
        <h2 style="text-align: center; margin-bottom: 20px;">Create Admin Account</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Register Now</button>
        </form>
        <p style="text-align:center; margin-top:15px; font-size:14px;">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>