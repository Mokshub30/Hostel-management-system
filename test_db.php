<?php
$host = "127.0.0.1";   
$user = "root";        
$pass = "";        
$db   = "hostel_db";   
$port = 3307;          

$conn = mysqli_connect($host, $user, $pass, $db, $port);

if ($conn) {
    echo "✅ Connection successful to database '$db' on port $port";
} else {
    die("❌ Connection failed: " . mysqli_connect_error());
}
?>
