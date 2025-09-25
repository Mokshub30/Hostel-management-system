<?php
include "db.php";

$hash = password_hash("admin@123", PASSWORD_DEFAULT);
$sql = "INSERT INTO admins (username, password) VALUES ('admin', 'admin@123')";

if (mysqli_query($conn, $sql)) {
    echo "✅ Admin user created successfully!";
} else {
    echo "❌ Error: " . mysqli_error($conn);
}
?>
