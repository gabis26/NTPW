<?php
include("../config.php");

//session_start();
//require_once "../db.php";

// Zaštita – samo admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Dohvat izvještaja
$sql = "
SELECT 
    izvjestaji.id,
    izvjestaji.status,
    izvjestaji.datum,
    kumce.ime AS dijete_ime,
    kumce.prezime AS dijete_prezime,
    kumovi.email AS kum_email
FROM izvjestaji
LEFT JOIN kumce ON izvjestaji.kumce_id = kumce.id
LEFT JOIN kumovi ON izvjestaji.kum_id = kumovi.id
ORDER BY izvjestaji.datum DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Izvještaji</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="../index.html">Početna</a></li>
            <li><a href="reports.php">Izvještaji</a></li>
            <li><a href="logout.php">Odjava</a></li>
        </ul>
    </nav>
</header>

<main style="padding:2em">

    <h1>Izvještaji o djeci</h1>

    <p>
        <a href="add-report.php" style="font-weight:bold;">➕ Dodaj novi izvještaj</a>
    </p>

    <table border="1" cellpadding="10" cellspacing="0" width="100%">
        <tr style="background:#f2f2f2">
            <th>ID</th>
            <th>Dijete</th>
            <th>Kum (email)</th>
            <th>Datum</th>
            <th>Status</th>
            <th>Akcije</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['dijete_ime'] . " " . $row['dijete_prezime']; ?></td>
                    <td><?= $row['kum_email']; ?></td>
                    <td><?= date("d.m.Y", strtotime($row['datum'])); ?></td>
                    <td>
                        <?php if ($row['status'] == 'gotov'): ?>
                            <span style="color:green;font-weight:bold;">Gotov</span>
                        <?php else: ?>
                            <span style="color:orange;font-weight:bold;">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="view-report.php?id=<?= $row['id'] ?>">Pregled</a> |
                        <a href="edit-report.php?id=<?= $row['id'] ?>">Uredi</a> |
                        <a href="delete-report.php?id=<?= $row['id'] ?>" onclick="return confirm('Jeste li sigurni?')">Obriši</a>
            
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Nema izvještaja u bazi.</td>
            </tr>
        <?php endif; ?>

    </table>

</main>

<footer>
    <p>&copy; <?= date("Y"); ?> – Admin panel</p>
</footer>

</body>
</html>
