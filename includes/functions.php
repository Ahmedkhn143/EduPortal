<?php
// includes/functions.php

// 1. Data ko hackers se bachane ka function (Sanitization)
function sanitize($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

// 2. Security Check (Bar bar session ka code likhne se bachne ke liye)
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        // Base URL ke zariye redirect
        header("Location: /EduPortal/login.php");
        exit();
    }
}
?>