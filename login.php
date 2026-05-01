<?php
session_start();
include 'config/db.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Password verification logic
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | EduPortal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container { max-width: 400px; margin: 100px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 6px; }
        button { width: 100%; padding: 12px; background: #1e293b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body style="background: #f1f5f9;">
    <div class="auth-container">
        <h2 style="text-align: center; margin-bottom: 20px;">EduPortal Login</h2>
        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login to System</button>
        </form>
        <p style="text-align:center; margin-top:15px; font-size:14px;">New here? <a href="register.php">Create Account</a></p>
    </div>
</body>
</html>