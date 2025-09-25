<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="style.css">
  <meta charset="UTF-8">
  <title>Register | MCC Hostel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include "navbar.php"; ?> <!-- Navbar -->
<?php include "db.php"; ?>     <!-- Database connection -->

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-lg p-4">
        <h3 class="text-center mb-4">📝 Student Registration</h3>
        
        <form action="register_action.php" method="POST">
          
          <!-- Full Name -->
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>

          <!-- Phone -->
          <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" pattern="[0-9]{10}" placeholder="10-digit number" required>
          </div>

          <!-- Address -->
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3" required></textarea>
          </div>

          <!-- Course -->
          <div class="mb-3">
            <label class="form-label">Course</label>
            <select name="department" class="form-control" required>
              <option value="">-- Select Course --</option>
              <option>Computer Science</option>
              <option>Electronics</option>
              <option>Physics</option>
              <option>Chemistry</option>
              <option>Biology</option>
              <option>Commerce</option>
              <option>Arts</option>
            </select>
          </div>

          <!-- Year of Study -->
          <div class="mb-3">
            <label class="form-label">Year of Study</label>
            <select name="year" class="form-control" required>
              <option value="">-- Select Year --</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
            </select>
          </div>

          <!-- Date of Birth -->
          <div class="mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
          </div>

          <!-- Room Selection -->
          <div class="mb-3">
            <label class="form-label">Select Room</label>
            <select name="room_id" class="form-control" required>
              <option value="">-- Select Available Room --</option>
              <?php
              $rooms = mysqli_query(
                $conn,
                "SELECT id, room_number, room_type, rent 
                 FROM rooms 
                 WHERE status = 'available' AND availability = 1"
              );
              while ($room = mysqli_fetch_assoc($rooms)) {
                echo "<option value='{$room['id']}'>
                        Room {$room['room_number']} ({$room['room_type']}) - ₹{$room['rent']}
                      </option>";
              }
              ?>
            </select>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>

          <!-- Submit -->
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Register</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
