<?php
session_start();
include "db.php"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Mess | MCC Hostel</title>

  <!-- Bootstrap first -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Your theme last so it overrides Bootstrap -->
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include "navbar.php"; ?> <!-- same navbar across all pages -->

  <div class="container mt-5">
    <h2 class="text-center text-pink mb-2">🍽️ Mess Menu</h2>
    <p class="text-center mb-4">Weekly mess schedule for students</p>

    <div class="table-responsive">
      <table class="table table-hover align-middle text-center shadow-sm pink-table">
        <thead>
          <tr>
            <th>Day</th>
            <th>Breakfast</th>
            <th>Lunch</th>
            <th>Dinner</th>
          </tr>
        </thead>
        <tbody>
          <tr><td>Monday</td><td>Idli & Sambar</td><td>Rice, Sambar, Veg Curry</td><td>Chapati & Curry</td></tr>
          <tr><td>Tuesday</td><td>Poori & Aloo</td><td>Curd Rice & Fryums</td><td>Rice & Dal</td></tr>
          <tr><td>Wednesday</td><td>Dosa</td><td>Puliyodarai</td><td>Roti & Paneer</td></tr>
          <tr><td>Thursday</td><td>Upma</td><td>Veg Biriyani</td><td>Chapati & Veg Curry</td></tr>
          <tr><td>Friday</td><td>Paratha</td><td>Lemon Rice</td><td>Rice & Sambar</td></tr>
          <tr><td>Saturday</td><td>Pongal</td><td>Veg Fried Rice</td><td>Dosa & Curry</td></tr>
          <tr><td>Sunday</td><td>Bread Omelette</td><td>Special Meals</td><td>Rice & Chicken Curry</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <footer class="footer text-center mt-5">
    <p>&copy; 2025 Mount Carmel College | Hostel Management System</p>
  </footer>
</body>
</html>
