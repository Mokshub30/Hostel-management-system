<?php
$host = "127.0.0.1";   // use 127.0.0.1 instead of localhost
$user = "root";        // your MySQL user
$pass = "";        // your MySQL password
$db   = "hostel_db";   // your database name
$port = 3307;          // custom MySQL port

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
