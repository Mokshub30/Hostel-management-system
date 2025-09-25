<?php
session_start();
include "db.php"; 

$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['student_id']) || isset($_SESSION['admin_id']);
$role = $_SESSION['role'] ?? (
  isset($_SESSION['admin_id']) ? 'admin' :
  (isset($_SESSION['student_id']) ? 'student' : null)
);
$userId = $_SESSION['user_id'] ?? ($_SESSION['student_id'] ?? ($_SESSION['admin_id'] ?? null));
$dashboardUrl = ($role === 'admin') ? 'admin_dashboard.php' : 'student_dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MTS Hostel | Mount Carmel College</title>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Header & Navigation -->
<header class="header">
  <nav class="navbar">
    <div class="logo"><a href="index.php">🏫 MTS Hostel</a></div>

    <ul class="links">
      <li><a href="index.php">Home</a></li>
      <?php if (!$isLoggedIn): ?>
        <li><a href="#rooms">Rooms</a></li>
      <?php endif; ?>
      <li><a href="mess.php">Mess</a></li>
      <li><a href="notices.php">Notices</a></li>
      <li><a href="complaints.php">Complaints</a></li>
    </ul>

    <div class="buttons">
      <?php if ($isLoggedIn): ?>
        <a href="<?php echo htmlspecialchars($dashboardUrl); ?>">Dashboard</a>
        <a href="logout.php" class="btn-danger">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php" class="btn-success">Register</a>
      <?php endif; ?>
    </div>
  </nav>
</header>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-content">
      <h1>Welcome to MTS Hostel</h1>
      <p>A smart and secure platform to manage all your hostel needs – from room allocation and mess schedules to announcements and support.</p>
      <div class="buttons">
        <?php if (!$isLoggedIn): ?>
          <a href="register.php">Apply for Room</a>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features">
    <h2>✨ System Features</h2>
    <div class="feature-cards">
      <div class="card fade-in">
        <h3>🏠 Room Management</h3>
        <p>Check availability, book rooms, or view room details with ease.</p>
      </div>
      <div class="card fade-in">
        <h3>🍽️ Mess Schedule</h3>
        <p>Stay updated with weekly menus and manage your mess preferences.</p>
      </div>
      <div class="card fade-in">
        <h3>📢 Announcements</h3>
        <p>Get real-time updates about hostel rules, events, and more.</p>
      </div>
      <div class="card fade-in">
        <h3>🛠️ Complaint Box</h3>
        <p>Report issues directly to hostel admin and track resolution status.</p>
      </div>
    </div>
  </section>

  <!-- Available Rooms Section -->
  <?php if (!$isLoggedIn): ?>
  <section class="rooms" id="rooms">
    <h2>🏡 Available Rooms</h2>
    <div class="room-grid">
      <?php
      $rooms = json_decode(file_get_contents(__DIR__ . "/rooms.json"), true);
      if ($rooms && count($rooms) > 0):
          foreach ($rooms as $room): ?>
            <div class="room-card fade-in">
              <img src="<?php echo htmlspecialchars($room['image']); ?>" 
                   alt="<?php echo htmlspecialchars($room['name']); ?>">
              <div class="card-body mt-2">
                <h2><?php echo htmlspecialchars($room['name']); ?></h2>
                <p><?php echo htmlspecialchars($room['description']); ?></p>
                <p class="rent">₹<?php echo number_format($room['price'], 2); ?> / year</p>
                <a href="register.php" class="book-button">Register to Book</a>
              </div>
            </div>
      <?php endforeach; else: echo "<p>No rooms available right now.</p>"; endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <!-- Footer -->
  <footer class="footer">
    <p>&copy; 2025 Mount Carmel College | Hostel Management System</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Scroll Animation Script -->
  <script>
    const faders = document.querySelectorAll('.fade-in');
    const appearOnScroll = () => {
      faders.forEach(fader => {
        const rect = fader.getBoundingClientRect();
        if(rect.top < window.innerHeight - 50) {
          fader.classList.add('show');
        }
      });
    };
    window.addEventListener('scroll', appearOnScroll);
    window.addEventListener('load', appearOnScroll);
  </script>

</body>
</html>
