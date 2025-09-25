<?php
session_start();

// Store role before destroying session
$role = $_SESSION['user_type'] ?? null;

// Clear all session data
session_unset();
session_destroy();

// Redirect based on role
if ($role === 'admin') {
    header("Location: login.php?role=admin");
} else {
    header("Location: login.php?role=student");
}
exit();
