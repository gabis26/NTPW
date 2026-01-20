<?php
//session_start();



require_once "../config.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}


$id = $_GET['id'];

// dohvat izvještaja
$sql = "SELECT i.*, k.ime AS kum_ime, k.prezime AS kum_prezime, k.email AS kum_email,
        d.ime AS dijete_ime, d.prezime AS dijete_prezime, d.datum_rodenja, d.spol
        FROM izvjestaji i
        JOIN kumovi k ON k.id = i.kum_id
        JOIN kumce d ON d.id = i.kumce_id
        WHERE i.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style.css">
    <title>Pregled izvještaja</title>
</head>
<body>
    <h1>Izvještaj #<?= $report['id'] ?></h1>
    <p><b>Datum:</b> <?= $report['datum'] ?></p>
    <p><b>Dijete:</b> <?= $report['dijete_ime']." ".$report['dijete_prezime'] ?></p>
    <p><b>Kum:</b> <?= $report['kum_ime']." ".$report['kum_prezime'] ?> (<?= $report['kum_email'] ?>)</p>
    <p><b>Status:</b> <?= $report['status'] ?></p>
    <hr>
    <h3>Opis</h3>
    <p><?= nl2br($report['opis']) ?></p>

    <h3>Škola</h3>
    <p><?= $report['skola'] ?></p>

    <h3>Ocjene</h3>
    <p><?= $report['ocjena'] ?></p>

    <h3>Zahvala</h3>
    <p><?= nl2br($report['zahvala']) ?></p>

     <a href="reports.php" class="back-link">← Natrag na izvještaje</a>

</body>
</html>
