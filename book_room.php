<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

if (!isset($_GET['room_id'])) {
    die("Invalid room selection.");
}

$room_id = intval($_GET['room_id']);

// Check if student already has an active booking
$stmt = $conn->prepare("SELECT id, room_id FROM bookings WHERE student_id = ? AND status='active' LIMIT 1");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($booking_id, $current_room_id);
$stmt->fetch();
$stmt->close();

$today = date('Y-m-d');
$year_month = date('Y-m'); // For billing, e.g., 2025-09

if ($booking_id) {
    // Student wants to change room
    if ($current_room_id == $room_id) {
        die("You are already in this room.");
    }

    // Update current booking to new room
    $update = $conn->prepare("UPDATE bookings SET room_id = ?, start_date = ? WHERE id = ?");
    $update->bind_param("isi", $room_id, $today, $booking_id);
    if ($update->execute()) {
        // Update room availability
        $conn->query("UPDATE rooms SET availability=1 WHERE id=$current_room_id"); // previous room now available
        $conn->query("UPDATE rooms SET availability=0 WHERE id=$room_id"); // new room booked

        // Generate monthly bill for new room
        $rent = $conn->query("SELECT rent FROM rooms WHERE id=$room_id")->fetch_assoc()['rent'];
        $conn->query("INSERT INTO billing (student_id, room_id, month, amount) VALUES ($student_id, $room_id, '$year_month', $rent)");

        echo "✅ Room changed successfully. <a href='student_dashboard.php'>Go Back</a>";
    } else {
        echo "❌ Error updating room: " . $update->error;
    }
    $update->close();

} else {
    // No active booking, insert new booking
    $insert = $conn->prepare("INSERT INTO bookings (student_id, room_id, start_date, status) VALUES (?, ?, ?, 'active')");
    $insert->bind_param("iis", $student_id, $room_id, $today);

    if ($insert->execute()) {
        $booking_id = $conn->insert_id;
        $conn->query("UPDATE rooms SET availability=0 WHERE id=$room_id"); // mark room as unavailable

        // Generate first month bill
        $rent = $conn->query("SELECT rent FROM rooms WHERE id=$room_id")->fetch_assoc()['rent'];
        $conn->query("INSERT INTO billing (student_id, room_id, month, amount) VALUES ($student_id, $room_id, '$year_month', $rent)");

        echo "✅ Room booked successfully. <a href='student_dashboard.php'>Go Back</a>";
    } else {
        echo "❌ Error booking room: " . $insert->error;
    }
    $insert->close();
}
?>
