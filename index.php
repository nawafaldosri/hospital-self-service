<?php
session_start();
require "db.php";

$error = "";


/* =========================
   التحقق من رقم الهوية
   ========================= */

function validateSaudiId($id) {

    // لازم يكون 10 أرقام ويبدأ بـ 1 أو 2
    if (!preg_match('/^[12][0-9]{9}$/', $id)) {
        return false;
    }

    // يمنع مثل:
    // 1111111111
    // 2222222222
    if (preg_match('/^(\d)\1{9}$/', $id)) {
        return false;
    }

    return true;
}


/* =========================
   عند الضغط على متابعة
   ========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $national_id = trim($_POST["national_id"] ?? "");

    if ($national_id === "") {

        $error = "يرجى إدخال رقم الهوية أو الإقامة";

    } elseif (!validateSaudiId($national_id)) {

        $error = "رقم الهوية أو الإقامة غير مسجل بشكل صحيح";

    } else {

        /* البحث عن المريض */
        $stmt = $conn->prepare(
            "SELECT id, file_number
             FROM patients
             WHERE national_id = ?"
        );

        $stmt->bind_param("s", $national_id);
        $stmt->execute();

        $result = $stmt->get_result();


        /* إذا المريض موجود */
        if ($result->num_rows > 0) {

            $patient = $result->fetch_assoc();

            $_SESSION["patient_id"] = $patient["id"];

            header("Location: pay.php");
            exit();

        } else {

            /* إذا المريض غير موجود */
            $_SESSION["new_national_id"] = $national_id;

            header("Location: register.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        الخدمة الذاتية للمستشفى
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>

<body>

<div class="container">

    <div class="card">


        <!-- الشعار -->

        <div class="logo">

            <img
                src="images/logo.png"
                alt="شعار المستشفى"
            >

        </div>


        <h1>
            الخدمة الذاتية للمستشفى
        </h1>


        <p class="subtitle">
            أدخل رقم الهوية أو الإقامة للمتابعة
        </p>


        <!-- رسالة الخطأ -->

        <?php if ($error !== ""): ?>

            <div class="error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <!-- النموذج -->

        <form method="POST">


            <label for="national_id">

                رقم الهوية / الإقامة

            </label>


            <input
                type="text"

                id="national_id"

                name="national_id"

                inputmode="numeric"

                maxlength="10"

                oninput="
                    this.value =
                    this.value.replace(/[^0-9]/g,'')
                "

                placeholder="أدخل 10 أرقام"

                autocomplete="off"

                required
            >


            <button type="submit">

                متابعة

            </button>

        </form>


        <!-- واتساب -->

        <a
            href="https://wa.me/966549568361?text=السلام%20عليكم%20لدي%20مشكلة%20في%20الخدمة%20الذاتية"

            target="_blank"

            class="whatsapp-button"
        >

            💬 تواصل مع الدعم عبر واتساب

        </a>


    </div>

</div>

</body>

</html>