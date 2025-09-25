<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== "admin") {
    header("Location: login.php?error=⚠️ Please login first");
    exit();
}

include "db.php";

// 🔹 Quick stats
$total_students = $conn->query("SELECT COUNT(*) AS c FROM students")->fetch_assoc()['c'] ?? 0;
$total_rooms    = $conn->query("SELECT COUNT(*) AS c FROM rooms")->fetch_assoc()['c'] ?? 0;
$total_bills    = $conn->query("SELECT COUNT(*) AS c FROM billing")->fetch_assoc()['c'] ?? 0;
$pending_bills  = $conn->query("SELECT COUNT(*) AS c FROM billing WHERE status IN ('unpaid','partial')")->fetch_assoc()['c'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | Hostel Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">

  <style>
    .stat-card {
      transition: transform 0.2s;
    }
    .stat-card:hover {
      transform: scale(1.02);
    }
  </style>
</head>
<body class="bg-light">

<?php include "navbar.php"; ?>

<div class="container mt-4">
  <h2 class="mb-4 text-center">👨‍💼 Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>

  <!-- Quick Stats -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm stat-card text-center">
        <div class="card-body">
          <h5 class="text-muted">Students</h5>
          <h2 class="fw-bold"><?= $total_students ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm stat-card text-center">
        <div class="card-body">
          <h5 class="text-muted">Rooms</h5>
          <h2 class="fw-bold"><?= $total_rooms ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm stat-card text-center">
        <div class="card-body">
          <h5 class="text-muted">Bills</h5>
          <h2 class="fw-bold"><?= $total_bills ?></h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm stat-card text-center">
        <div class="card-body">
          <h5 class="text-muted">Pending Bills</h5>
          <h2 class="text-danger fw-bold"><?= $pending_bills ?></h2>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Actions -->
  <div class="row g-4">
    <div class="col-md-3">
      <div class="card shadow-lg border-0 h-100 text-center">
        <div class="card-body">
          <h5 class="card-title">🏠 Manage Rooms</h5>
          <p class="card-text small text-muted">Add, update or delete hostel rooms.</p>
          <a href="manage_rooms.php" class="btn btn-primary btn-sm">Open</a>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-lg border-0 h-100 text-center">
        <div class="card-body">
          <h5 class="card-title">📢 Manage Notices</h5>
          <p class="card-text small text-muted">Post announcements for students.</p>
          <a href="manage_notices.php" class="btn btn-warning btn-sm">Open</a>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-lg border-0 h-100 text-center">
        <div class="card-body">
          <h5 class="card-title">🛠️ View Complaints</h5>
          <p class="card-text small text-muted">Check and resolve student complaints.</p>
          <a href="manage_complaints.php" class="btn btn-danger btn-sm">Open</a>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card shadow-lg border-0 h-100 text-center">
        <div class="card-body">
          <h5 class="card-title">💰 Manage Billing</h5>
          <p class="card-text small text-muted">Generate & update student bills.</p>
          <a href="manage_billing.php" class="btn btn-success btn-sm">Open</a>
        </div>
      </div>
    </div>

    <!-- Students Report -->
    <div class="col-md-3">
      <div class="card shadow-lg border-0 h-100 text-center">
        <div class="card-body">
          <h5 class="card-title">📋 View Students Report</h5>
          <p class="card-text small text-muted">See all registered students at a glance.</p>
          <a href="view_students.php" class="btn btn-info btn-sm">Open</a>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center mt-4">
    <a href="logout.php" class="btn btn-dark">🚪 Logout</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
