<?php



//session_start();
require_once "../config.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("DELETE FROM izvjestaji WHERE id = $id");
    header("Location: reports.php");
}
?>

