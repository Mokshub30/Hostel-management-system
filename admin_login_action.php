<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // ✅ Use prepared statements (SQL injection safe)
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ✅ Verify hashed password
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true); // prevent session fixation

            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_username'] = $row['username'];

            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: admin_login.php?error=❌ Wrong password");
            exit();
        }
    } else {
        header("Location: admin_login.php?error=❌ Admin not found");
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>
