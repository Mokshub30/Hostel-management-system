<?php
session_start();
include "db.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notices | MCC Hostel</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include "navbar.php"; ?> <!-- reuse your navbar -->

  <div class="container mt-5">
    <h2 class="text-center">📢 Hostel Notices</h2>
    <p class="text-center">Stay updated with hostel rules and announcements</p>

    <div class="list-group mt-4">
      <a href="#" class="list-group-item list-group-item-action">
        🔔 Hostel gates will be closed at 10 PM sharp.
      </a>
      <a href="#" class="list-group-item list-group-item-action">
        📆 Mess will remain closed on upcoming festival (Jan 15th).
      </a>
      <a href="#" class="list-group-item list-group-item-action">
        🛠️ Water maintenance scheduled on Sept 10th, 9 AM - 1 PM.
      </a>
      <a href="#" class="list-group-item list-group-item-action">
        🎉 Cultural fest registrations open till Sept 20th.
      </a>
    </div>
  </div>

  <footer class="footer text-center mt-5">
    <p>&copy; 2025 Mount Carmel College | Hostel Management System</p>
  </footer>
</body>
</html>
