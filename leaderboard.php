<?php require('config/conn.php'); 
if(isset($_GET['klas'])){
    $klascode = $_GET['klas'];
} else {
    $klascode = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/bg.css">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <script src="https://kit.fontawesome.com/20865e7038.js" crossorigin="anonymous"></script>
    <title>Leaderboard - Het Ultieme Rekenspel!</title>
</head>
<body data-bs-theme="dark" class="gradient">

    <div id="spinner" class="show gradient position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-white" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>

    <div id='stars'></div>
    <div id='stars2'></div>
    <div id='stars3'></div>

    <section class="py-5 mb-5 vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-2 col-sm-12"></div>
                <div class="col-lg-6 col-md-8 col-sm-12">
                   <div class="mt-3 mb-3">
                    <a href="./" class="text-white text-decoration-none fw-bold"><i class="fa-solid fa-arrow-left"></i> Teruggaan</a>
                   </div>
                   <?php 
                    if(isset($_GET['status']) && $_GET['status'] == 'goed'){
                        echo '<div class="alert alert-success" role="alert">
                        Goed gedaan! Je hebt het leaderboard behaald!
                        </div>';
                    } 
                    if(isset($_GET['status']) && $_GET['status'] == 'helaas'){
                        echo '<div class="alert alert-danger" role="alert">
                        Volgende keer beter! Helaas heb je het leaderboard niet behaald.
                        </div>';
                    }
                   ?>
                   <div class="p-3 bg-white" data-bs-theme="light">
                      <h1 class="text-black">🏆 Leaderboard</h1>
                      <ul class="list-group list-group-numbered list-group-flush">
                      <?php 
                        if(!$klascode){
                            $stmt = $conn->query('SELECT * FROM oefeningen ORDER BY score DESC LIMIT 10');
                        } else {
                            $stmt = $conn->prepare('SELECT * FROM klasoefeningen WHERE klas = :klas ORDER BY score DESC LIMIT 10');
                            $stmt->bindParam(':klas', $klascode);
                            $stmt->execute();
                        }
                        $leerlingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        $entriesGevonden = false;

                        foreach($leerlingen as $leerling){
                            if ($leerling['score'] != 0) {
                                echo '<li class="list-group-item bg-transparent p-3 text-capitalize">';
                                echo $leerling['naam'] . " <span class='float-end'>" . $leerling['score'] . " punten</span>";
                                echo '</li>';
                                $entriesGevonden = true;
                            }
                        }

                        if (!$entriesGevonden) {
                            echo '<li class="bg-transparent p-3 text-black">';
                            echo 'Het leaderboard is verbaaswekkend leeg...';
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
    <script src="assets/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>