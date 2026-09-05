<?php
session_start();
require "db.php";

if (!isset($_SESSION["new_national_id"])) {
    header("Location: index.php");
    exit();
}

$national_id = $_SESSION["new_national_id"];

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    // التحقق من الاسم
    if ($full_name === "") {

        $error = "يرجى إدخال الاسم الكامل";

    }

    // التحقق من الجوال
    elseif (!preg_match('/^05\d{8}$/', $phone)) {

        $error =
            "رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام";

    }

    else {

        $stmt = $conn->prepare(
            "INSERT INTO patients
            (national_id, full_name, phone)
            VALUES (?, ?, ?)"
        );

        $stmt->bind_param(
            "sss",
            $national_id,
            $full_name,
            $phone
        );

        if ($stmt->execute()) {

            $patient_id = $conn->insert_id;

            $file_number =
                "H" .
                str_pad(
                    $patient_id,
                    6,
                    "0",
                    STR_PAD_LEFT
                );

            $update = $conn->prepare(
                "UPDATE patients
                 SET file_number = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "si",
                $file_number,
                $patient_id
            );

            $update->execute();

            $_SESSION["patient_id"] = $patient_id;

            unset($_SESSION["new_national_id"]);

            header("Location: pay.php");
            exit();

        } else {

            $error = "حدث خطأ أثناء إنشاء الملف";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>إنشاء ملف طبي جديد</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="card">

        <div class="logo">
            <img src="images/logo.png" alt="شعار المستشفى">
        </div>

        <h1>إنشاء ملف طبي جديد</h1>

        <p class="subtitle">
            لا يوجد ملف مسجل بهذه الهوية
        </p>

        <div class="info">

            <span>رقم الهوية</span>

            <strong>
                <?php echo htmlspecialchars($national_id); ?>
            </strong>

        </div>

        <?php if ($error != ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <label for="full_name">
                الاسم الكامل
            </label>

            <input
                type="text"
                id="full_name"
                name="full_name"
                placeholder="اكتب الاسم الكامل"
                required
            >


            <label for="phone">
                رقم الجوال
            </label>

            <input
                type="text"
                id="phone"
                name="phone"

                inputmode="numeric"

                minlength="10"
                maxlength="10"

                pattern="05[0-9]{8}"

                oninput="
                    this.value =
                    this.value.replace(/[^0-9]/g,'')
                "

                placeholder="05xxxxxxxx"
                required
            >

            <button type="submit">
                إنشاء الملف
            </button>

        </form>


        <a href="index.php" class="btn secondary">
            رجوع
        </a>

    </div>

</div>

</body>
</html>