<?php
session_start();
include "db.php";

// ✅ Security check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$bill_id    = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;

if ($bill_id <= 0) {
    die("❌ Invalid bill.");
}

// 🔹 Get the annual bill
$stmt = $conn->prepare("
    SELECT id, year, total_amount, amount_paid, status
    FROM annual_billing
    WHERE id = ? AND student_id = ?
");
$stmt->bind_param("ii", $bill_id, $student_id);
$stmt->execute();
$bill = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$bill) {
    die("❌ Bill not found.");
}

// ✅ Handle Form Submit
$success = $error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pay_amount = floatval($_POST['amount']);

    // Basic validation
    if ($pay_amount <= 0) {
        $error = "Enter a valid amount.";
    } elseif ($bill['amount_paid'] + $pay_amount > $bill['total_amount']) {
        $error = "Cannot pay more than the total (₹{$bill['total_amount']}).";
    } else {
        // Insert installment
        $ins_stmt = $conn->prepare("
            INSERT INTO installments (annual_billing_id, amount, paid_on)
            VALUES (?, ?, NOW())
        ");
        $ins_stmt->bind_param("id", $bill['id'], $pay_amount);
        $ins_stmt->execute();
        $ins_stmt->close();

        // Update main bill
        $new_paid = $bill['amount_paid'] + $pay_amount;
        $new_status = ($new_paid >= $bill['total_amount']) ? 'paid' : 'unpaid';

        $upd = $conn->prepare("
            UPDATE annual_billing
            SET amount_paid = ?, status = ?
            WHERE id = ?
        ");
        $upd->bind_param("dsi", $new_paid, $new_status, $bill['id']);
        $upd->execute();
        $upd->close();

        $success = "✅ ₹" . number_format($pay_amount, 2) . " paid successfully.";
        // Refresh bill info for display
        $bill['amount_paid'] = $new_paid;
        $bill['status']      = $new_status;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pay Annual Fee | MCC Hostel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <div class="card shadow p-4">
    <h3 class="mb-3">💳 Pay Annual Hostel Fee (<?= htmlspecialchars($bill['year']) ?>)</h3>

    <?php if($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php elseif($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <p><b>Total Fee:</b> ₹<?= number_format($bill['total_amount'], 2) ?></p>
    <p><b>Paid So Far:</b> ₹<?= number_format($bill['amount_paid'], 2) ?></p>
    <p><b>Status:</b> 
      <span class="<?= $bill['status'] === 'paid' ? 'text-success fw-bold' : 'text-danger fw-bold' ?>">
        <?= ucfirst($bill['status']) ?>
      </span>
    </p>

    <?php if($bill['status'] !== 'paid'): ?>
    <form method="POST" class="mt-3">
      <div class="mb-3">
        <label for="amount" class="form-label">Installment Amount (₹)</label>
        <input type="number" step="0.01" class="form-control" name="amount" id="amount" required>
      </div>
      <button type="submit" class="btn btn-success">Pay Now</button>
      <a href="student_dashboard.php" class="btn btn-secondary">Back</a>
    </form>
    <?php else: ?>
      <a href="student_dashboard.php" class="btn btn-primary">Return to Dashboard</a>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
