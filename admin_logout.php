<?php
session_start();
session_unset();
session_destroy();

// Redirect to login with a message
header("Location:login.php?error=✅ You have been logged out successfully");
exit();
?>
