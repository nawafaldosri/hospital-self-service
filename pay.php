<?php
session_start();
require "db.php";

if (!isset($_SESSION["patient_id"])) {
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION["patient_id"];

$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();

$patient = $stmt->get_result()->fetch_assoc();

if (!$patient) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$fee = 150;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $clinic = $_POST["clinic"] ?? "";
    $doctor = $_POST["doctor"] ?? "";

    if ($clinic === "" || $doctor === "") {
        $error = "اختر التخصص والطبيب";
    } else {

        $_SESSION["clinic"] = $clinic;
        $_SESSION["doctor"] = $doctor;

        $status = "paid";

        $payment = $conn->prepare(
            "INSERT INTO payments (patient_id, amount, status)
             VALUES (?, ?, ?)"
        );

        $payment->bind_param("ids", $patient_id, $fee, $status);
        $payment->execute();

        $_SESSION["payment_id"] = $conn->insert_id;

        header("Location: receipt.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الدفع - مستشفى الهدى</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="payment-page">

<header class="hospital-header">

    <div class="brand-box">
        <img src="images/logo.png" class="main-logo" alt="مستشفى الهدى">
    </div>

    <div class="secure-box">
        <img src="images/security-icon.png" class="small-icon" alt="أمان">
        <div>
            <strong>دفع آمن وموثوق</strong>
            <span>جميع معاملاتك محمية</span>
        </div>
    </div>

</header>


<main class="payment-wrapper">

    <section class="payment-card">

        <div class="payment-top-icon">
            <img src="images/payment-icon.png" alt="الدفع">
        </div>

        <h1>الدفع</h1>

        <p class="subtitle">
            يرجى مراجعة بياناتك واختيار التخصص والطبيب
        </p>

        <?php if ($error): ?>
            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>


        <div class="patient-info">

            <div class="info-row">

                <div class="info-title">
                    <img src="images/user-icon.png" class="field-icon" alt="">
                    <span>اسم المريض</span>
                </div>

                <strong>
                    <?php echo htmlspecialchars($patient["full_name"]); ?>
                </strong>

            </div>


            <div class="info-row">

                <div class="info-title">
                    <img src="images/folder-icon.png" class="field-icon" alt="">
                    <span>رقم الملف</span>
                </div>

                <strong>
                    <?php echo htmlspecialchars($patient["file_number"]); ?>
                </strong>

            </div>


            <div class="info-row">

                <div class="info-title">
                    <img src="images/id-icon.png" class="field-icon" alt="">
                    <span>رقم الهوية</span>
                </div>

                <strong>
                    <?php echo htmlspecialchars($patient["national_id"]); ?>
                </strong>

            </div>

        </div>


        <form method="POST">

            <div class="selection-row">

                <div class="selection-label">
                    <img src="images/specialty-icon.png" class="field-icon" alt="">
                    <span>اختر التخصص</span>
                </div>

                <select
                    name="clinic"
                    id="clinic"
                    required
                    onchange="updateDoctors()"
                >
                    <option value="">اختر التخصص</option>
                    <option value="باطنية">باطنية</option>
                    <option value="عظام">عظام</option>
                    <option value="قلب">قلب</option>
                    <option value="جلدية">جلدية</option>
                </select>

            </div>


            <div class="selection-row">

                <div class="selection-label">
                    <img src="images/doctor-icon.png" class="field-icon" alt="">
                    <span>اختر الطبيب</span>
                </div>

                <select
                    name="doctor"
                    id="doctor"
                    required
                >
                    <option value="">اختر الطبيب</option>
                </select>

            </div>


            <div class="fee-card">

                <div class="fee-title">
                    <img src="images/money-icon.png" class="money-icon" alt="">
                    <div>
                        <strong>قيمة الكشف</strong>
                        <span>رسوم الزيارة الطبية</span>
                    </div>
                </div>

                <div class="fee-number">
                    <strong>150</strong>
                    <span>ريال</span>
                </div>

            </div>


            <button type="submit" class="pay-button">

                <img src="images/lock-icon.png" class="button-icon" alt="">

                <span>دفع الآن</span>

            </button>

        </form>


        <a href="index.php" class="back-button">
            رجوع
        </a>

    </section>

</main>


<footer class="features-row">

    <div class="feature-item">

        <img src="images/support-icon.png" alt="دعم">

        <h3>دعم على مدار الساعة</h3>

        <p>نحن هنا لمساعدتك</p>

    </div>


    <div class="feature-item">

        <img src="images/speed-icon.png" alt="سهولة وسرعة">

        <h3>سهولة وسرعة</h3>

        <p>عملية دفع آمنة ومبسطة</p>

    </div>


    <div class="feature-item">

        <img src="images/lock-icon.png" alt="خصوصية وأمان">

        <h3>خصوصية وأمان</h3>

        <p>بياناتك محمية بالكامل</p>

    </div>

</footer>


<script>

function updateDoctors() {

    const clinic =
        document.getElementById("clinic").value;

    const doctor =
        document.getElementById("doctor");

    doctor.innerHTML =
        '<option value="">اختر الطبيب</option>';

    let doctors = [];

    if (clinic === "باطنية") {

        doctors = [
            "د. خالد العتيبي",
            "د. أحمد القحطاني"
        ];

    } else if (clinic === "عظام") {

        doctors = [
            "د. محمد الحربي",
            "د. فهد الشمري"
        ];

    } else if (clinic === "قلب") {

        doctors = [
            "د. عبدالله السبيعي",
            "د. تركي المطيري"
        ];

    } else if (clinic === "جلدية") {

        doctors = [
            "د. سارة الغامدي",
            "د. نورة الزهراني"
        ];

    }

    doctors.forEach(function(name) {

        const option =
            document.createElement("option");

        option.value = name;
        option.textContent = name;

        doctor.appendChild(option);
    });
}

</script>

</body>
</html>