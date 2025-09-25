<?php
include "db.php";

$sql = "SELECT * FROM admins WHERE username='admin'";
$result = mysqli_query($conn, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    echo "Username in DB: " . $row['username'] . "<br>";
    echo "Password in DB: " . $row['password'] . "<br>";
} else {
    echo "No admin found!";
}
?>
