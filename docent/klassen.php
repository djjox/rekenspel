<?php
require('../config/conn.php');

if(!isset($_SESSION['user_id'])){
  echo header('Location: inloggen');
  exit;
} else {
    $klasDocent = $_SESSION['user_id'];
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
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-2 col-sm-12"></div>
                <div class="col-lg-6 col-md-8 col-sm-12">
                    <?php if(isset($_SESSION['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error']; ?>
                    </div>
                    <?php unset($_SESSION['error']);
                    } ?>
                    <?php if(isset($_SESSION['succes'])) { ?>
                    <div class="alert alert-success" role="alert">
                    <?php echo $_SESSION['succes']; ?>
                    </div>
                    <?php unset($_SESSION['succes']);
                    } ?>
                    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="./" class="fw-bold text-decoration-none text-white">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Klassen</li>
                        </ol>
                    </nav>
                    <h1 class="text-white">Klassen</h1>
                    <div class="p-3 bg-white" data-bs-theme="light">
                    <ul class="list-group list-group-numbered list-group-flush">
                    <?php 
                    $stmt = $conn->prepare('SELECT * FROM klassen WHERE klasDocent = :klasDocent ORDER BY klasID DESC LIMIT 10');
                    $stmt->bindParam(':klasDocent', $klasDocent);
                    $stmt->execute();

                    $klassen = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $entriesGevonden = false;

                    foreach($klassen as $klas){
                        echo '<li class="list-group-item bg-transparent p-3 text-uppercase">';
                        echo $klas['klasCode'] . " <span class='float-end'><a href='klas/".$klas['klasCode']."' class='fw-bold text-black text-decoration-none'>Inzien</a></span>";
                        echo '</li>';
                        $entriesGevonden = true;
                    }

                    if (!$entriesGevonden) {
                        echo '<li class="bg-transparent p-3 text-black">';
                        echo 'U heeft nog geen klassen aangemaakt.';
                        echo '</li>';
                    }
                    ?>
                    </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12"></div>
            </div>
        </div>
    </section>

    <script src="../assets/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>
</html>