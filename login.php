<?php
session_start();
include "db.php"; // Database connection

// Initialize error message
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);            
    $password = $_POST['password'];
    $role     = $_POST['role']; // student or admin

    if ($role === "student") {
        // Student login (using email)
        $stmt = $conn->prepare("SELECT id, name, email, password FROM students WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Store session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_type'] = "student";

                header("Location: student_dashboard.php");
                exit;
            } else {
                $error = "❌ Invalid email or password!";
            }
        } else {
            $error = "❌ No student account found with this email!";
        }
        $stmt->close();

    } elseif ($role === "admin") {
        // Admin login (using username)
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                // Store session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['username'];
                $_SESSION['user_type'] = "admin";

                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error = "❌ Invalid admin credentials!";
            }
        } else {
            $error = "❌ Admin not found!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | MCC Hostel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">

</head>
<body>

  <?php include "navbar.php"; ?>

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg p-4">
          <h3 class="text-center mb-4">🔑 Login</h3>

          <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php } ?>

          <form method="POST">
            <!-- Email / Username -->
            <div class="mb-3">
              <label class="form-label">Email / Username</label>
              <input type="text" name="email" class="form-control" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Role -->
            <div class="mb-3">
              <label class="form-label">Login as</label>
              <select name="role" class="form-control" required>
                <option value="student">Student</option>
                <option value="admin">Admin</option>
              </select>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>

</body>
</html>