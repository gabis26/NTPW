<?php
require_once "../config.php";

$kumce_id = intval($_GET['kumce_id']);

$sql = "
SELECT k.id, k.ime, k.prezime, k.email
FROM kumovi k
JOIN kumstvo kk ON kk.kum_id = k.id
WHERE kk.kumce_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $kumce_id);
$stmt->execute();
$res = $stmt->get_result();

$kumovi = [];
while ($row = $res->fetch_assoc()) {
    $kumovi[] = $row;
}

header('Content-Type: application/json');
echo json_encode($kumovi);
