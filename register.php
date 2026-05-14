<?php
session_start();
include 'config/db.php';

$full_name = '';
$email = '';

if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $email === '' || $pass === '' || $confirm === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($pass !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $existing = $stmt->fetch();

        if ($existing) {
            $error = 'Email is already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
            $stmt->execute([$full_name, $email, $hash]);
            $success = 'Account created successfully. You can log in now.';
            $full_name = '';
            $email = '';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | EduPortal</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container { max-width: 420px; margin: 90px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 12px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 6px; }
        button { width: 100%; padding: 12px; background: #1e293b; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .helper { font-size: 12px; margin: 6px 0 12px; color: #475569; }
        .error { color: #b91c1c; margin-bottom: 12px; }
        .success { color: #15803d; margin-bottom: 12px; }
    </style>
</head>
<body style="background: #f1f5f9;">
    <div class="auth-container">
        <h2 style="text-align: center; margin-bottom: 20px;">Create Account</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="POST">
            <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($full_name) ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div id="passwordStrength" class="helper"></div>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            <button type="submit" name="register">Create Account</button>
        </form>
        <p style="text-align:center; margin-top:15px; font-size:14px;">Already have an account? <a href="login.php">Login</a></p>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const strengthEl = document.getElementById('passwordStrength');

        function updateStrength(value) {
            if (!value) {
                strengthEl.textContent = '';
                return;
            }

            let score = 0;
            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[a-z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;

            if (score <= 2) {
                strengthEl.textContent = 'Password strength: Weak (use 8+ chars, upper, lower, number, symbol)';
                strengthEl.style.color = '#b91c1c';
            } else if (score <= 4) {
                strengthEl.textContent = 'Password strength: Medium (add more variety for strong password)';
                strengthEl.style.color = '#b45309';
            } else {
                strengthEl.textContent = 'Password strength: Strong';
                strengthEl.style.color = '#15803d';
            }
        }

        passwordInput.addEventListener('input', (e) => updateStrength(e.target.value));
    </script>
</body>
</html>
