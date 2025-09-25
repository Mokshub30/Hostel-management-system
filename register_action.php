<?php
include "db.php"; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data safely
    $name       = mysqli_real_escape_string($conn, $_POST['name']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $phone      = mysqli_real_escape_string($conn, $_POST['phone']);
    $address    = mysqli_real_escape_string($conn, $_POST['address']);
    $dob        = mysqli_real_escape_string($conn, $_POST['dob']);
    $course     = mysqli_real_escape_string($conn, $_POST['department']); // map "department" field to course
    $year       = intval($_POST['year']); // convert to int
    $room_id    = intval($_POST['room_id']); // selected room
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];

    // 1. Check password match
    if ($password !== $confirm) {
        echo "<script>alert('❌ Passwords do not match!'); window.location.href='register.php';</script>";
        exit();
    }

    // 2. Check if email already exists
    $check = "SELECT * FROM students WHERE email='$email'";
    $result = mysqli_query($conn, $check);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('⚠️ Email already registered! Please login.'); window.location.href='login.php';</script>";
        exit();
    }

    // 3. Hash password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert new student (with selected room_id)
    $sql = "INSERT INTO students 
            (name, email, phone, address, dob, course, year, room_id, password) 
            VALUES 
            ('$name', '$email', '$phone', '$address', '$dob', '$course', $year, $room_id, '$hashed_password')";

    if (mysqli_query($conn, $sql)) {
        // 5. Update the chosen room → mark as booked
        $update = "UPDATE rooms SET status='booked', availability=0 WHERE id=$room_id";
        mysqli_query($conn, $update);

        echo "<script>
                alert('✅ Registration successful! Please login.');
                window.location.href='login.php';
              </script>";
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
}
?>
