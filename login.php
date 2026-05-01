<?php
session_start();
include 'config/db.php';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Logic: Username aur Password check karna
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$user, $pass]);
    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['full_name'];
        header("Location: dashboard.php");
    } else {
        $error = "Ghalat Username ya Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>EduPortal | Login</title>
    <style>
        body { font-family: sans-serif; background: #2c3e50; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 10px; width: 320px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        h2 { text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .error { color: red; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>EduPortal Login</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Sign In</button>
        </form>
    </div>
</body>
</html>