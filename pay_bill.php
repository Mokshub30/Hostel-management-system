<?php
session_start();
include "db.php";

// ✅ Only logged-in students
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

// Get billing id from query
if (!isset($_GET['billing_id']) || !is_numeric($_GET['billing_id'])) {
    die("Invalid request.");
}
$billing_id = (int)$_GET['billing_id'];

/* ---------- Fetch billing row ---------- */
$stmt = $conn->prepare("
    SELECT id, student_id, year, amount, paid_amount, status
    FROM billing
    WHERE id = ? AND student_id = ?
");
$stmt->bind_param("ii", $billing_id, $student_id);
$stmt->execute();
$bill = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$bill) {
    die("Bill not found.");
}

$remaining = $bill['amount'] - $bill['paid_amount'];

/* ---------- Handle Payment Submission ---------- */
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pay_amount = floatval($_POST['pay_amount'] ?? 0);

    if ($pay_amount <= 0) {
        $message = "<div class='alert alert-danger'>Enter a valid amount.</div>";
    } elseif ($pay_amount > $remaining) {
        $message = "<div class='alert alert-danger'>Payment cannot exceed ₹" . number_format($remaining,2) . ".</div>";
    } else {
        // Update DB
        $new_paid = $bill['paid_amount'] + $pay_amount;
        $new_status = ($new_paid >= $bill['amount']) ? "paid" : "partial";

        $update_stmt = $conn->prepare("UPDATE billing SET paid_amount=?, status=? WHERE id=?");
        $update_stmt->bind_param("dsi", $new_paid, $new_status, $billing_id);
        $update_stmt->execute();
        $update_stmt->close();

        header("Location: student_dashboard.php?success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pay Bill | MCC Hostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include "navbar.php"; ?>

<div class="container mt-4">
    <h2 class="mb-4">💳 Pay Hostel Fee (<?= htmlspecialchars($bill['year']) ?>)</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Total:</strong> ₹<?= number_format($bill['amount'], 2) ?></p>
            <p><strong>Paid:</strong> ₹<?= number_format($bill['paid_amount'], 2) ?></p>
            <p><strong>Remaining:</strong> ₹<?= number_format($remaining, 2) ?></p>
            <p><strong>Status:</strong> 
                <span class="<?=
                    $bill['status']==='unpaid' ? 'text-danger fw-bold' :
                    ($bill['status']==='partial' ? 'text-warning fw-bold' : 'text-success fw-bold')
                ?>">
                    <?= ucfirst($bill['status']) ?>
                </span>
            </p>

            <?php if ($bill['status'] !== 'paid'): ?>
                <?= $message ?>
                <form method="POST" class="mt-3">
                    <div class="mb-3">
                        <label class="form-label">Enter Amount to Pay</label>
                        <input type="number" step="0.01" min="1" max="<?= $remaining ?>" name="pay_amount" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Pay Now</button>
                    <a href="student_dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            <?php else: ?>
                <div class="alert alert-success">✅ Bill already cleared.</div>
                <a href="student_dashboard.php" class="btn btn-secondary">Back</a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
