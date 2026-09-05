<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "hospital_db"
);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات");
}

$conn->set_charset("utf8mb4");

?>