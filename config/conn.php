<?php
session_start();

require('functions.php');

$servernaam = "localhost";
$gebruikersnaam = "root";
$wachtwoord = "";
$dbnaam = "rekenspel2";

try {
  $conn = new PDO("mysql:host=$servernaam;dbname=$dbnaam", $gebruikersnaam, $wachtwoord);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connectie gefaald: " . $e->getMessage();
}
?>