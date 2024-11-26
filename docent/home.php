<?php
require('../config/conn.php');

if(!isset($_SESSION['user_id'])){
  echo header('Location: inloggen');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/bg.css">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>Het Ultieme Rekenspel!</title>
</head>
<body data-bs-theme="dark" class="gradient">

    <div id="spinner" class="show gradient position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-white" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>

    <div id='stars'></div>
    <div id='stars2'></div>
    <div id='stars3'></div>

    <section class="py-5 mt-5 mb-5 vh-100 d-flex align-items-center">
            <div class="container text-center">
                <h1 class="display-2 text-white">Hallo <?php echo $_SESSION['gebruikersnaam']; ?>! Wat wilt u doen?</h1>
                <a href="klas-aanmaken" class="button">📖 Klas aanmaken</a>
                <a href="klassen" class="button button-alt">Klassen bekijken 🏫</a>
                <a href="uitloggen" class="button button-alt">🚪 Uitloggen</a>
            </div>
    </section>

    <script src="../assets/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>