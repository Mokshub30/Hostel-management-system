<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    header("Location:admin_login.php?error=⚠️ Please login first");
    exit();
}

include "db.php";

// Edit mode check
$edit_mode = false;
$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : null;
$edit_room = [];

if ($edit_id) {
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows === 1) {
        $edit_room = $res->fetch_assoc();
        $edit_mode = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Rooms</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2>🏠 Manage Rooms</h2>

    <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

    <!-- Add or Edit Form -->
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title"><?= $edit_mode ? "Edit Room" : "Add New Room" ?></h5>
        <form method="POST" action="room_action.php">
          <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $edit_room['id'] ?>">
          <?php endif; ?>

          <div class="row mb-2">
            <div class="col-md-4">
              <label>Room Number</label>
              <input type="text" name="room_number" class="form-control" 
                     value="<?= $edit_mode ? htmlspecialchars($edit_room['room_number']) : '' ?>" required>
            </div>
            <div class="col-md-4">
              <label>Room Type</label>
              <input type="text" name="room_type" class="form-control" 
                     value="<?= $edit_mode ? htmlspecialchars($edit_room['room_type']) : '' ?>" required>
            </div>
            <div class="col-md-4">
              <label>Rent</label>
              <input type="number" step="0.01" name="rent" class="form-control" 
                     value="<?= $edit_mode ? htmlspecialchars($edit_room['rent']) : '' ?>" required>
            </div>
          </div>

          <div class="mb-2">
            <label>Description</label>
            <textarea name="description" class="form-control"><?= $edit_mode ? htmlspecialchars($edit_room['description']) : '' ?></textarea>
          </div>

          <div class="row mb-2">
            <div class="col-md-6">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="available" <?= ($edit_mode && $edit_room['status'] == 'available') ? 'selected' : '' ?>>Available</option>
                <option value="booked" <?= ($edit_mode && $edit_room['status'] == 'booked') ? 'selected' : '' ?>>Booked</option>
              </select>
            </div>
            <div class="col-md-6">
              <label>Availability</label>
              <select name="availability" class="form-control">
                <option value="1" <?= ($edit_mode && $edit_room['availability'] == 1) ? 'selected' : '' ?>>Yes</option>
                <option value="0" <?= ($edit_mode && $edit_room['availability'] == 0) ? 'selected' : '' ?>>No</option>
              </select>
            </div>
          </div>

          <button type="submit" name="<?= $edit_mode ? 'update_room' : 'add_room' ?>" 
                  class="btn <?= $edit_mode ? 'btn-warning' : 'btn-success' ?>">
            <?= $edit_mode ? 'Update Room' : 'Add Room' ?>
          </button>
        </form>
      </div>
    </div>

    <!-- Rooms List -->
    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Room No</th>
          <th>Type</th>
          <th>Rent</th>
          <th>Status</th>
          <th>Available?</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = mysqli_query($conn, "SELECT * FROM rooms ORDER BY id ASC");
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <td>{$row['id']}</td>
                  <td>{$row['room_number']}</td>
                  <td>{$row['room_type']}</td>
                  <td>{$row['rent']}</td>
                  <td>{$row['status']}</td>
                  <td>" . ($row['availability'] ? 'Yes' : 'No') . "</td>
                  <td>
                    <a href='manage_rooms.php?edit_id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                    <form method='POST' action='room_action.php' style='display:inline-block' onsubmit='return confirm(\"Delete this room?\")'>
                      <input type='hidden' name='id' value='{$row['id']}'>
                      <button type='submit' name='delete_room' class='btn btn-danger btn-sm'>Delete</button>
                    </form>
                  </td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</body>
</html>
