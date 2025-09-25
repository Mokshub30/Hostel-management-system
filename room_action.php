<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php?error=⚠️ Please login first");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Add room
    if (isset($_POST['add_room'])) {
        $room_number = trim($_POST['room_number']);
        $room_type   = trim($_POST['room_type']);
        $description = trim($_POST['description']);
        $rent        = floatval($_POST['rent']);
        $status      = $_POST['status'];
        $availability= intval($_POST['availability']);

        $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type, description, rent, status, availability) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdis", $room_number, $room_type, $description, $rent, $status, $availability);

        if ($stmt->execute()) {
            header("Location: manage_rooms.php?msg=✅ Room added successfully");
        } else {
            header("Location: manage_rooms.php?error=❌ Error adding room: " . $conn->error);
        }
        exit();
    }

    // Update room
    if (isset($_POST['update_room'])) {
        $id          = intval($_POST['id']);
        $room_number = trim($_POST['room_number']);
        $room_type   = trim($_POST['room_type']);
        $description = trim($_POST['description']);
        $rent        = floatval($_POST['rent']);
        $status      = $_POST['status'];
        $availability= intval($_POST['availability']);

        $stmt = $conn->prepare("UPDATE rooms SET room_number=?, room_type=?, description=?, rent=?, status=?, availability=? WHERE id=?");
        $stmt->bind_param("sssdsii", $room_number, $room_type, $description, $rent, $status, $availability, $id);

        if ($stmt->execute()) {
            header("Location: manage_rooms.php?msg=✅ Room updated successfully");
        } else {
            header("Location: manage_rooms.php?error=❌ Error updating room: " . $conn->error);
        }
        exit();
    }

    // Delete room
    if (isset($_POST['delete_room'])) {
        $id = intval($_POST['id']);

        $stmt = $conn->prepare("DELETE FROM rooms WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            header("Location: manage_rooms.php?msg=✅ Room deleted successfully");
        } else {
            header("Location: manage_rooms.php?error=❌ Error deleting room: " . $conn->error);
        }
        exit();
    }
}
?>
