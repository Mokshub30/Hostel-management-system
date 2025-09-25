<?php
// hash_admin_passwords.php
include "db.php";

// Fetch all admins
$result = mysqli_query($conn, "SELECT id, password FROM admins");

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    $plainPassword = $row['password'];

    // Skip if already hashed (bcrypt hashes always start with $2y$ or $2a$)
    if (preg_match('/^\$2y\$/', $plainPassword)) {
        echo "Admin ID $id already hashed. Skipping...<br>";
        continue;
    }

    // Hash the plain-text password
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    // Update database with hashed password
    $update = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
    $update->bind_param("si", $hashedPassword, $id);

    if ($update->execute()) {
        echo "✅ Admin ID $id password hashed successfully.<br>";
    } else {
        echo "❌ Error updating Admin ID $id: " . $conn->error . "<br>";
    }
}

echo "<br>All done! Now you can safely use password_verify() in your login.";
?>
