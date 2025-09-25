<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php?error=⚠️ Please login first");
    exit();
}
include "db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Notices</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2>📢 Manage Notices</h2>
    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

    <!-- Notice Form -->
    <form method="POST" action="notice_action.php" class="mb-3">
      <input type="text" name="title" placeholder="Title" class="form-control mb-2" required>
      <textarea name="message" placeholder="Message" class="form-control mb-2" required></textarea>
      <button type="submit" name="post_notice" class="btn btn-primary">Post Notice</button>
    </form>

    <!-- Notices List -->
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Message</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM notices ORDER BY created_at DESC");
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['title']}</td>
                  <td>{$row['message']}</td>
                  <td>{$row['created_at']}</td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
