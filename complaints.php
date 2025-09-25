<?php
session_start();
include "db.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_id = $_SESSION['user_id'];
    $subject    = mysqli_real_escape_string($conn, $_POST['subject']);
    $description= mysqli_real_escape_string($conn, $_POST['description']);
    $complaint_text = $subject . " - " . $description;

    $sql = "INSERT INTO complaints (student_id, complaint_text) VALUES ('$student_id', '$complaint_text')";
    $msg = mysqli_query($conn, $sql) ? "✅ Complaint submitted successfully!" 
                                     : "❌ Error: " . mysqli_error($conn);
}

// Fetch user's complaints
$student_id = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM complaints WHERE student_id='$student_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complaints | MCC Hostel</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <!-- Same fixed header -->
  <header class="header">
    <nav class="navbar">
      <div class="logo">
        <a href="index.php">🏫 MCC Hostel</a>
      </div>
      <ul class="links">
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php#rooms">Rooms</a></li>
        <li><a href="mess.php">Mess</a></li>
        <li><a href="complaints.php" class="active">Complaints</a></li>
        <li><a href="#">Notices</a></li>
      </ul>
      <div class="buttons">
        <a href="student_dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
      </div>
    </nav>
  </header>

  <!-- Complaint Form -->
  <section class="features">
    <h2>Submit a Complaint</h2>
    <?php if (isset($msg)) echo "<p>$msg</p>"; ?>

    <form method="POST" style="max-width:600px;margin:auto;text-align:left;">
      <label>Subject:</label><br>
      <input type="text" name="subject" required style="width:100%;padding:10px;margin-bottom:10px;"><br>

      <label>Description:</label><br>
      <textarea name="description" rows="5" required style="width:100%;padding:10px;margin-bottom:10px;"></textarea><br>

      <button type="submit" class="book-button">Submit Complaint</button>
    </form>
  </section>

  <!-- Complaint List -->
  <section class="features" style="margin-top:50px;">
    <h2>My Complaints</h2>
    <div style="max-width:800px;margin:auto;text-align:left;">
      <?php
      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<div class='card' style='margin-bottom:15px;'>
                  <p>" . htmlspecialchars($row['complaint_text']) . "</p>
                  <p><b>Status:</b> " . htmlspecialchars($row['status']) . "</p>
                  <small>Submitted on: " . htmlspecialchars($row['created_at']) . "</small>
                </div>";
        }
      } else {
        echo "<p>No complaints submitted yet.</p>";
      }
      ?>
    </div>
  </section>

  <footer class="footer">
    <p>&copy; 2025 Mount Carmel College | Hostel Management System</p>
  </footer>
</body>
</html>
