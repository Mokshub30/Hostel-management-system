<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php?error=⚠️ Please login first");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id']);

    if ($action === "resolve") {
        $stmt = $conn->prepare("UPDATE complaints SET status = 'resolved' WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: manage_complaints.php?msg=✅ Complaint resolved successfully");
        } else {
            header("Location: manage_complaints.php?error=❌ Failed to resolve complaint");
        }
        exit();
    }

    if ($action === "delete") {
        $stmt = $conn->prepare("DELETE FROM complaints WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: manage_complaints.php?msg=✅ Complaint deleted successfully");
        } else {
            header("Location: manage_complaints.php?error=❌ Failed to delete complaint");
        }
        exit();
    }
}

header("Location: manage_complaints.php?error=⚠️ Invalid request");
exit();
?>
