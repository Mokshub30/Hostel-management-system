<?php
session_start();
include "db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

$msg = "";

/* ---------- Handle Add / Update ---------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST['action'] === 'add') {
        $sid    = intval($_POST['student_id']);
        $year   = intval($_POST['year']);
        $amount = floatval($_POST['amount']);

        // fetch student's room_id
        $roomRes = $conn->query("SELECT room_id FROM students WHERE id = $sid");
        $roomRow = $roomRes->fetch_assoc();
        $room_id = $roomRow ? intval($roomRow['room_id']) : NULL;

        if ($room_id) {
            $stmt = $conn->prepare("
                INSERT INTO billing (student_id, room_id, year, amount, paid_amount, status)
                VALUES (?, ?, ?, ?, 0, 'unpaid')
                ON DUPLICATE KEY UPDATE amount = VALUES(amount)
            ");
            $stmt->bind_param("iiid", $sid, $room_id, $year, $amount);
            $stmt->execute();
            $stmt->close();
            $msg = "✅ Bill for $year saved.";
        } else {
            $msg = "⚠️ Cannot add bill: student has no assigned room.";
        }
    }

    if ($_POST['action'] === 'update') {
        $bill_id = intval($_POST['bill_id']);
        $paid    = floatval($_POST['paid_amount']);

        // fetch total amount
        $row = $conn->query("SELECT amount FROM billing WHERE id=$bill_id")->fetch_assoc();
        if ($row) {
            $status = 'unpaid';
            if ($paid > 0 && $paid < $row['amount']) $status = 'partial';
            if ($paid >= $row['amount'])             $status = 'paid';

            $stmt = $conn->prepare("UPDATE billing SET paid_amount=?, status=? WHERE id=?");
            $stmt->bind_param("dsi", $paid, $status, $bill_id);
            $stmt->execute();
            $stmt->close();
            $msg = "✅ Updated payment for Bill #$bill_id ($status).";
        }
    }
}

/* ---------- Fetch Students & Bills ---------- */
$sql = "
    SELECT s.id AS sid, s.name, s.email, s.department,
           b.id AS bill_id, b.year, b.amount, b.paid_amount, b.status
    FROM students s
    LEFT JOIN billing b ON s.id = b.student_id
    ORDER BY s.name ASC, b.year DESC
";
$res = $conn->query($sql);

/* ---------- Group by Student ---------- */
$students = [];
while ($row = $res->fetch_assoc()) {
    $sid = $row['sid'];
    if (!isset($students[$sid])) {
        $students[$sid] = [
            'info' => [
                'name'  => $row['name'],
                'email' => $row['email'],
                'dept'  => $row['department']
            ],
            'bills' => []
        ];
    }
    if ($row['bill_id']) {
        $students[$sid]['bills'][] = [
            'id'          => $row['bill_id'],
            'year'        => $row['year'],
            'amount'      => $row['amount'],
            'paid_amount' => $row['paid_amount'],
            'status'      => $row['status']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Annual Billing</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card { border-radius: 12px; }
.status-paid    { color: green; font-weight: bold; }
.status-partial { color: orange; font-weight: bold; }
.status-unpaid  { color: red; font-weight: bold; }
</style>
</head>
<body class="bg-light">
<?php include "navbar.php"; ?>

<div class="container mt-4">
    <h2 class="mb-3 text-center">📑 Manage Annual Billing</h2>
    <?php if($msg): ?>
      <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="row">
    <?php foreach ($students as $sid => $data): ?>
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($data['info']['name']) ?></h5>
                    <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($data['info']['email']) ?></p>
                    <p class="mb-3"><strong>Dept:</strong> <?= htmlspecialchars($data['info']['dept']) ?></p>

                    <?php if (count($data['bills'])): ?>
                        <?php foreach ($data['bills'] as $bill): ?>
                            <div class="p-2 border rounded mb-2">
                                <div><strong>Year:</strong> <?= htmlspecialchars($bill['year']) ?></div>
                                <div><strong>Total Fee:</strong> ₹<?= number_format($bill['amount'], 2) ?></div>
                                <form method="POST" class="mt-2 d-flex align-items-center">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                                    <label class="me-2"><strong>Paid:</strong></label>
                                    <input type="number" step="0.01" name="paid_amount"
                                           value="<?= htmlspecialchars($bill['paid_amount']) ?>"
                                           class="form-control me-2" style="max-width:120px;">
                                    <span class="me-2 status-<?= $bill['status'] ?>"><?= ucfirst($bill['status']) ?></span>
                                    <button type="submit" class="btn btn-sm btn-primary">💾 Save</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No bills yet.</p>
                    <?php endif; ?>

                    <!-- Add new bill -->
                    <form method="POST" class="mt-3 d-flex">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="student_id" value="<?= $sid ?>">
                        <input type="number" name="year" value="<?= date('Y') ?>" class="form-control me-2" style="max-width:120px;" required>
                        <input type="number" step="0.01" name="amount" placeholder="Total Fee" class="form-control me-2" style="max-width:150px;" required>
                        <button class="btn btn-success">➕ Add</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
