<?php
session_start();
session_unset(); // Tamam variables khatam karein
session_destroy(); // Session khatam karein
header("Location: login.php");
exit();
?>