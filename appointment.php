<?php
session_start();

if (!isset($_SESSION["patient"])) {
    header("Location: index.php");
    exit();
}

$patient = $_SESSION["patient"];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>تفاصيل الموعد</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">
    <div class="card">

        <div class="logo">🩺</div>

        <h1>تفاصيل الموعد</h1>

        <div class="info">
            <span>اسم المريض</span>
            <strong><?php echo $patient["name"]; ?></strong>
        </div>

        <div class="info">
            <span>رقم الملف</span>
            <strong><?php echo $patient["file_number"]; ?></strong>
        </div>

        <div class="info">
            <span>العيادة</span>
            <strong><?php echo $patient["clinic"]; ?></strong>
        </div>

        <div class="info">
            <span>الطبيب</span>
            <strong><?php echo $patient["doctor"]; ?></strong>
        </div>

        <div class="info">
            <span>موعد الكشف</span>
            <strong><?php echo $patient["appointment"]; ?></strong>
        </div>

        <div class="info price">
            <span>قيمة الكشف</span>
            <strong><?php echo $patient["fee"]; ?> ريال</strong>
        </div>

        <a href="receipt.php" class="btn">
            دفع تجريبي
        </a>

        <a href="index.php" class="btn secondary">
            رجوع
        </a>

    </div>
</div>

</body>
</html>