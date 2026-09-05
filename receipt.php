<?php
session_start();

require "db.php";

if (
    !isset($_SESSION["patient_id"]) ||
    !isset($_SESSION["payment_id"])
) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION["patient_id"];
$payment_id = $_SESSION["payment_id"];

$stmt = $conn->prepare(
    "SELECT * FROM patients WHERE id = ?"
);

$stmt->bind_param("i", $patient_id);
$stmt->execute();

$patient = $stmt->get_result()->fetch_assoc();

$payment_stmt = $conn->prepare(
    "SELECT * FROM payments WHERE id = ? AND patient_id = ?"
);

$payment_stmt->bind_param(
    "ii",
    $payment_id,
    $patient_id
);

$payment_stmt->execute();

$payment = $payment_stmt->get_result()->fetch_assoc();

if (!$patient || !$payment) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>إيصال الدفع</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="card">

        <div class="success-icon">✅</div>

        <h1>تم الدفع بنجاح</h1>

        <div class="info">
            <span>اسم المريض</span>
            <strong>
                <?php echo htmlspecialchars($patient["full_name"]); ?>
            </strong>
        </div>

        <div class="info">
            <span>رقم الملف</span>
            <strong>
                <?php echo htmlspecialchars($patient["file_number"]); ?>
            </strong>
        </div>

        <div class="info">
            <span>رقم الهوية</span>
            <strong>
                <?php echo htmlspecialchars($patient["national_id"]); ?>
            </strong>
        </div>

        <div class="info">
            <span>المبلغ المدفوع</span>
            <strong>
                <?php echo htmlspecialchars($payment["amount"]); ?> ريال
            </strong>
        </div>

        <div class="queue">
            <span>رقم العملية</span>

            <div class="queue-number">
                <?php echo $payment["id"]; ?>
            </div>
        </div>

        <button onclick="window.print()">
            طباعة الإيصال
        </button>

        <a href="index.php" class="btn secondary">
            عملية جديدة
        </a>

    </div>

</div>

</body>
</html>