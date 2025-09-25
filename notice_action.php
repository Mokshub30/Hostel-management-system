<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php?error=⚠️ Please login first");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['post_notice'])) {
        $title = trim($_POST['title']);
        $message = trim($_POST['message']);

        $stmt = $conn->prepare("INSERT INTO notices (title, message, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $title, $message);

        if ($stmt->execute()) {
            header("Location: manage_notices.php?msg=✅ Notice posted successfully");
        } else {
            header("Location: manage_notices.php?error=❌ Error posting notice");
        }
        exit();
    }
}
?>
