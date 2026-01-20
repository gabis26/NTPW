<?php
//session_start();
require_once "../config.php";  // ili db.php - ovisno kako ste nazvali

// 1) Zaštita – samo admin
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// 2) Dohvat djece
$kumce = $conn->query("SELECT id, ime, prezime FROM kumce");

// 3) Spremanje forme
if (isset($_POST['spremi'])) {

    $kumce_id = $_POST['kumce_id'];
    $kum_id = $_POST['kum_id'];
    $opis = $_POST['opis'];
    $skola = $_POST['skola'];
    $ocjena = $_POST['ocjena'];
    $zahvala = $_POST['zahvala'];
    $status = $_POST['status'];

    // ➤ PROVJERA: je li taj kum povezan s tim kumčetom?
    $provjera = $conn->prepare("
        SELECT COUNT(*) AS broj
        FROM kumstvo
        WHERE kum_id = ? AND kumce_id = ?
    ");
    $provjera->bind_param("ii", $kum_id, $kumce_id);
    $provjera->execute();
    $rez = $provjera->get_result()->fetch_assoc();

    if ($rez['broj'] == 0) {
        $error = "Greška: odabrani kum nije povezan s odabranim djetetom.";
    } else {

    

        $sql = "INSERT INTO izvjestaji 
            (kumce_id, kum_id, opis, skola, ocjena, zahvala, status, datum)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iisssss",
            $kumce_id,
            $kum_id,
            $opis,
            $skola,
            $ocjena,
            $zahvala,
            $status
        );

        
        if ($stmt->execute()) {
            header("Location: reports.php");
            exit();
        } else {
            $error = "Greška pri spremanju izvještaja.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <title>Dodaj izvještaj</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="reports.php">← Natrag na izvještaje</a></li>
            <li><a href="logout.php">Odjava</a></li>
        </ul>
    </nav>
</header>

<main style="padding:2em">

<h1>Dodavanje izvještaja</h1>

<?php if (!empty($error)): ?>
    <p style="color:red;font-weight:bold;"><?= $error ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">

    <label>Dijete</label>
    <select id="kumce_id" name="kumce_id" required>
        <option value="">-- Odaberi dijete --</option>
        <?php while ($d = $kumce->fetch_assoc()): ?>
            <option value="<?= $d['id']; ?>">
                <?= $d['ime'] . " " . $d['prezime']; ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label>Kum (email)</label>
    <select id="kum_id" name="kum_id" required>
        <option value="">-- Prvo odaberi dijete --</option>
    </select>

    <label>Opis (kako se dijete osjeća)</label>
    <textarea name="opis" rows="5" required></textarea>

    <label>Škola</label>
    <input type="text" name="skola">

    <label>Ocjena</label>
    <textarea name="ocjena" rows="3"></textarea>

    <label>Zahvala kumu</label>
    <textarea name="zahvala" rows="4"></textarea>

    <label>Status izvještaja</label>
    <select name="status">
        <option value="draft">Draft</option>
        <option value="gotov">Gotov</option>
    </select>

    <br><br>
    <button type="submit" name="spremi">Spremi izvještaj</button>

</form>

</main>

<script>
// AJAX: kad se odabere kumče, dohvatimo kumove
document.getElementById("kumce_id").addEventListener("change", function(){
    const kumceId = this.value;

    fetch("get-kumovi.php?kumce_id=" + kumceId)
        .then(res => res.json())
        .then(data => {
            const kumSelect = document.getElementById("kum_id");
            kumSelect.innerHTML = "";

            if (data.length === 0) {
                kumSelect.innerHTML = "<option value=''>Nema kuma za ovo dijete</option>";
                return;
            }

            data.forEach(kum => {
                const opt = document.createElement("option");
                opt.value = kum.id;
                opt.text = kum.ime + " " + kum.prezime + " (" + kum.email + ")";
                kumSelect.appendChild(opt);
            });
        });
});
</script>

</body>
</html>
