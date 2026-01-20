<?php



//session_start();
require_once "../config.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];

// dohvat izvještaja
$sql = "SELECT * FROM izvjestaji WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$report = $result->fetch_assoc();

// dohvat djece i kumova
$kumce = $conn->query("SELECT id, ime, prezime FROM kumce");
$kumovi = $conn->query("SELECT id, ime, prezime, email FROM kumovi");

if (isset($_POST['spremi'])) {

    $kumce_id = $_POST['kumce_id'];
    $kum_id = $_POST['kum_id'];
    $opis = $_POST['opis'];
    $skola = $_POST['skola'];
    $ocjena = $_POST['ocjena'];
    $zahvala = $_POST['zahvala'];
    $status = $_POST['status'];

    $sql = "UPDATE izvjestaji SET kumce_id=?, kum_id=?, opis=?, skola=?, ocjena=?, zahvala=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssssi", $kumce_id, $kum_id, $opis, $skola, $ocjena, $zahvala, $status, $id);

    if ($stmt->execute()) {
        header("Location:reports.php");
        exit;
    } else {
        $error = "Greška pri spremanju.";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Uredi izvještaj</title>
    <link rel="stylesheet" href="../style.css">

</head>
<body>


    <h1>Uredi izvještaj #<?= $id ?></h1>

    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>

    <form method="post">
        <label>Dijete</label>
        <select name="kumce_id" required>
            <?php while ($d = $kumce->fetch_assoc()): ?>
                <option value="<?= $d['id'] ?>"
                    <?= ($d['id']==$report['kumce_id']) ? "selected" : "" ?>>
                    <?= $d['ime']." ".$d['prezime'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Kum</label>
        <select name="kum_id" required>
            <?php while ($k = $kumovi->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>"
                    <?= ($k['id']==$report['kum_id']) ? "selected" : "" ?>>
                    <?= $k['ime']." ".$k['prezime'] ?> (<?= $k['email'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <label>Opis</label>
        <textarea name="opis" rows="5"><?= $report['opis'] ?></textarea>

        <label>Škola</label>
        <input type="text" name="skola" value="<?= $report['skola'] ?>">

        <label>Ocjena</label>
        <input type="text" name="ocjena" value="<?= $report['ocjena'] ?>">

        <label>Zahvala</label>
        <textarea name="zahvala" rows="4"><?= $report['zahvala'] ?></textarea>

        <label>Status</label>
        <select name="status">
            <option value="draft" <?= $report['status']=='draft' ? 'selected' : '' ?>>Draft</option>
            <option value="gotov" <?= $report['status']=='gotov' ? 'selected' : '' ?>>Gotov</option>
        </select>
 
        <button type="submit" name="spremi">Spremi</button>
         <a href="reports.php" class="back-link">← Odustani</a>
    </form>
</body>
</html>
