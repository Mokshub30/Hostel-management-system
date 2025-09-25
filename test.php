<?php
$conn = mysqli_connect("127.0.0.1", "root", "", "hostel_db", 3307);

if ($conn) {
    echo "✅ Connected to DB!";
} else {
    echo "❌ Connection failed: " . mysqli_connect_error();
}
?>
