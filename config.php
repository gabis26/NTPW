<?php
$conn = new mysqli("localhost", "root", "", "sirotiste");

if ($conn->connect_error) {
  die("Greška u spajanju");
}

session_start();
?>
