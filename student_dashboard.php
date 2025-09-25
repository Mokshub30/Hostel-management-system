<?php
session_start();
include "db.php";

// ✅ Only logged-in students
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$student_id  = $_SESSION['user_id'];
$student_name = $_SESSION['user_name'] ?? "Guest";

/* ---------- Fetch profile ---------- */
$profile_stmt = $conn->prepare("
    SELECT name, email, phone, department, course, year, address
    FROM students
    WHERE id = ?
");
$profile_stmt->bind_param("i", $student_id);
$profile_stmt->execute();
$profile = $profile_stmt->get_result()->fetch_assoc();
$profile_stmt->close();

/* ---------- Fetch annual billing ---------- */
$billing_stmt = $conn->prepare("
    SELECT id, year, amount, paid_amount, status
    FROM billing
    WHERE student_id = ?
    ORDER BY year DESC
    LIMIT 1
");
$billing_stmt->bind_param("i", $student_id);
$billing_stmt->execute();
$billing = $billing_stmt->get_result()->fetch_assoc();
$billing_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | MCC Hostel</title>
    <link rel="stylesheet" href="style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include "navbar.php"; ?>
<?php if (isset($_GET['msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_GET['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>


<div class="container mt-4">
    <h2 class="mb-4">🎓 Welcome, <?= htmlspecialchars($student_name) ?></h2>

    <!-- Profile -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">Profile Information</div>
        <div class="card-body">
            <p><b>Name:</b> <?= htmlspecialchars($profile['name']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($profile['email']) ?></p>
            <p><b>Phone:</b> <?= htmlspecialchars($profile['phone']) ?></p>
            <p><b>Department:</b> <?= htmlspecialchars($profile['department']) ?></p>
            <p><b>Course:</b> <?= htmlspecialchars($profile['course']) ?></p>
            <p><b>Year:</b> <?= htmlspecialchars($profile['year']) ?></p>
            <p><b>Address:</b> <?= htmlspecialchars($profile['address']) ?></p>
        </div>
    </div>

    <!-- Annual Hostel Fee -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">Annual Hostel Fee</div>
        <div class="card-body">
            <?php if ($billing): 
                $remaining = $billing['amount'] - $billing['paid_amount'];
            ?>
                <div class="p-3 border rounded shadow-sm bg-white">
                    <h5 class="mb-3 text-success">
                        Annual Hostel Fee – Year <?= htmlspecialchars($billing['year']) ?>
                    </h5>
                    
                    <p><strong>Total Fee:</strong> ₹<?= number_format($billing['amount'], 2) ?></p>
                    <p><strong>Remaining:</strong> ₹<?= number_format($remaining, 2) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="<?=
                            $billing['status']==='unpaid'  ? 'text-danger fw-bold' :
                            ($billing['status']==='partial' ? 'text-warning fw-bold' : 'text-success fw-bold')
                        ?>">
                            <?= ucfirst($billing['status']) ?>
                        </span>
                    </p>

                    <?php if ($billing['status'] !== 'paid'): ?>
                        <a href="pay_bill.php?billing_id=<?= $billing['id'] ?>" class="btn btn-primary">Pay / Installment</a>
                    <?php else: ?>
                        <span class="badge bg-success">Cleared</span>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p>No annual bill generated yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-end mb-4">
        <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
    </div>
</div>

</body>
</html>
