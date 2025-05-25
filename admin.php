<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "construction_db";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// อัปเดตสถานะ
if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if (in_array($action, ['อนุมัติ', 'ปฏิเสธ'])) {
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $action, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin.php");
        exit();
    }
}

// คำสั่งค้นหา
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

$where = "WHERE 1";
if ($search !== '') {
    $where .= " AND (fullname LIKE '%$search%' OR phone LIKE '%$search%' OR location LIKE '%$search%' OR ref_number LIKE '%$search%')";
}
if ($status_filter !== '') {
    $where .= " AND status = '$status_filter'";
}

$sql = "SELECT * FROM requests $where ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ระบบขออนุญาตก่อสร้าง อบต.บึงบา</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1080px;
            margin: 40px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        header img {
            height: 70px;
        }
        .title-box {
            text-align: center;
        }
        .title-box h1 {
            margin: 0;
            font-size: 22px;
            color: #2c3e50;
        }
        .title-box h2 {
            margin: 0;
            font-size: 16px;
            color: #666;
            font-weight: normal;
        }
        .actions {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .actions form {
            display: flex;
            gap: 10px;
        }
        input[type="text"], select {
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button.print-button {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .approved { color: green; }
        .rejected { color: red; }
        .pending { color: orange; }
        a.button {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }
        a.approve { background-color: #28a745; }
        a.reject { background-color: #dc3545; }
        a.button:hover { opacity: 0.85; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <img src="img/logobuengba.png" alt="โลโก้">
        <div class="title-box">
            <h1>ระบบขออนุญาตก่อสร้าง องค์การบริหารส่วนตำบลบึงบา</h1>
            <h2>อำเภอหนองเสือ จังหวัดปทุมธานี</h2>
        </div>
    </header>

    <div class="actions">
        <form method="get">
            <input type="text" name="search" placeholder="ค้นหาคำขอ..." value="<?= htmlspecialchars($search) ?>">
            <select name="status">
                <option value="">-- สถานะทั้งหมด --</option>
                <option value="รอดำเนินการ" <?= $status_filter === 'รอดำเนินการ' ? 'selected' : '' ?>>รอดำเนินการ</option>
                <option value="อนุมัติ" <?= $status_filter === 'อนุมัติ' ? 'selected' : '' ?>>อนุมัติ</option>
                <option value="ปฏิเสธ" <?= $status_filter === 'ปฏิเสธ' ? 'selected' : '' ?>>ปฏิเสธ</option>
            </select>
            <button type="submit">ค้นหา</button>
        </form>
        <button class="print-button" onclick="window.print()">🖨 พิมพ์รายงาน</button>
    </div>

    <table>
        <tr>
            <th>เลขอ้างอิง</th>
            <th>ชื่อผู้ยื่น</th>
            <th>เบอร์โทร</th>
            <th>ที่อยู่</th>
            <th>ไฟล์แนบ</th>
            <th>สถานะ</th>
            <th>เวลา</th>
            <th>การดำเนินการ</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['ref_number'] ?></td>
            <td><?= $row['fullname'] ?></td>
            <td><?= $row['phone'] ?></td>
            <td><?= $row['location'] ?></td>
            <td><a href="uploads/<?= $row['document'] ?>" target="_blank">ดูไฟล์</a></td>
            <td>
                <?php
                if ($row['status'] === 'อนุมัติ') echo "<span class='approved'>✅ {$row['status']}</span>";
                else if ($row['status'] === 'ปฏิเสธ') echo "<span class='rejected'>❌ {$row['status']}</span>";
                else echo "<span class='pending'>🕓 {$row['status']}</span>";
                ?>
            </td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a class="button approve" href="?id=<?= $row['id'] ?>&action=อนุมัติ">✅ อนุมัติ</a>
                <a class="button reject" href="?id=<?= $row['id'] ?>&action=ปฏิเสธ">❌ ปฏิเสธ</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
