<?php
$name = $phone = $location = $filename = "";
$success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["fullname"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $location = htmlspecialchars($_POST["location"]);

    if ($_FILES["document"]["error"] === UPLOAD_ERR_OK) {
        $upload_dir = "uploads/";
        $filename = basename($_FILES["document"]["name"]);
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES["document"]["tmp_name"], $target_path)) {
            $success = true;
        } else {
            $error_message = "❌ อัปโหลดไม่สำเร็จ";
        }
    } else {
        $error_message = "❌ มีข้อผิดพลาดในการอัปโหลดไฟล์";
    }
} else {
    $error_message = "❌ วิธีการส่งข้อมูลไม่ถูกต้อง";
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ผลการส่งคำขอ</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .center {
            max-width: 600px;
            margin: 60px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            color: green;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            margin: 8px 0;
        }

        .error {
            color: red;
        }

        .btn-back {
            margin-top: 20px;
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
        }

        .btn-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="center">
    <?php if ($success): ?>
        <h2>✅ ส่งคำขอสำเร็จ!</h2>
        <p><strong>ชื่อ:</strong> <?= $name ?></p>
        <p><strong>เบอร์โทร:</strong> <?= $phone ?></p>
        <p><strong>ที่อยู่ก่อสร้าง:</strong> <?= nl2br($location) ?></p>
        <p><strong>เอกสารแนบ:</strong> <?= $filename ?></p>
    <?php else: ?>
        <h2 class="error"><?= $error_message ?></h2>
    <?php endif; ?>

    <a href="index.html" class="btn-back">🔙 กลับหน้าแรก</a>
</div>

</body>
</html>
