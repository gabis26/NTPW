<?php
include("../config.php");

// Ako je već prijavljen admin, preusmjeri ga
if (isset($_SESSION['admin'])) {
    header("Location: reports.php");
    exit();
}

// Obrada prijave
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Ako kasnije hashiramo lozinke, override s password_verify
        if ($password === $user['password']) {
            $_SESSION['admin'] = $user['username'];
            header("Location: reports.php");
            exit();
        } else {
            $error = "Pogrešna lozinka.";
        }
    } else {
        $error = "Korisnik ne postoji.";
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin prijava</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<section class="login-section">
    <div class="login-container">
        <h2>Prijava</h2>

        <?php if (!empty($error)): ?>
            <div class="login-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Korisničko ime</label>
            <input type="text" name="username" id="username" required>

            <label for="password">Lozinka</label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Prijavi se</button>
        </form>
    </div>
</section>

</body>
</html>
