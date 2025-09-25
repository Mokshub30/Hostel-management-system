<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php?error=⚠️ Please login first");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("UPDATE complaints SET status = 'Resolved' WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_complaints.php?msg=✅ Complaint resolved successfully");
    } else {
        header("Location: view_complaints.php?error=❌ Error resolving complaint");
    }
    exit();
} else {
    header("Location: view_complaints.php?error=⚠️ Invalid complaint ID");
    exit();
}
?>
