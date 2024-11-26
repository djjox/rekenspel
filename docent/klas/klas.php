<?php
require('../../config/conn.php');

if(!isset($_SESSION['user_id'])){
  echo header('Location: ../inloggen');
  exit;
} else {
    $klasDocent = $_SESSION['user_id'];

    $stmt = $conn->prepare('SELECT * FROM klassen WHERE klasCode = :klasCode');
    $stmt->execute(array(':klasCode' => $_GET['id']));
    $row = $stmt->fetch();

    $klascode = $row['klasCode'];

    if($row['klasID'] == ''){
        echo header('Location: ../klassen');
        exit;
    }

    if($row['klasDocent'] !== $klasDocent){
        echo header('Location: ../klassen');
        exit;
    }

    $stmt = $conn->prepare('SELECT * FROM klasoefeningen WHERE klas = :klas ORDER BY score DESC LIMIT 10');
    $stmt->bindParam(':klas', $klascode);
    $stmt->execute();

    $leerlingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totaalScore = 0;
    $totaalLeerlingen = count($leerlingen);

    foreach ($leerlingen as $leerling) {
        $totaalScore += $leerling['score'];
    }

    if(isset($_POST['verwijderen'])) {
        $stmt = $conn->prepare('DELETE FROM klasoefeningen WHERE klas = :klas');
        $stmt->bindParam(':klas', $klascode);
        $stmt->execute();
        
        $stmt = $conn->prepare('DELETE FROM klassen WHERE klasCode = :klasCode');
        $stmt->bindParam(':klasCode', $klascode);
        $stmt->execute();

        $_SESSION['succes'] = 'Klas verwijderd.';
        echo header('Location: ../klassen');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/bg.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title><?php echo htmlspecialchars($row['klasCode']); ?> - Het Ultieme Rekenspel!</title>
</head>
<body data-bs-theme="dark" class="gradient">

    <div id="spinner" class="show gradient position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-white" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>

    <div id='stars'></div>
    <div id='stars2'></div>
    <div id='stars3'></div>

    <div class="modal fade" id="verwijderen" tabindex="-1" data-bs-theme="light">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header border-0 shadow-sm">
                    <h5 class="modal-title text-black fs-5 fw-bold" id="exampleModalLabel">Weet u dat zeker?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body border-0 shadow-sm text-black">
                    <form action="" method="POST">
                    Alle data onder deze klas is dan verloren.
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="verwijderen" class="button button-gevaar">Verwijderen</button>
                  </form>
                </div>
            </div>
        </div>
    </div>

    <section class="py-5 mt-5 mb-5 vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../klassen" class="fw-bold text-decoration-none text-white">Klassen</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($row['klasCode']); ?></li>
                    </ol>
                </nav>
                <h1 class="text-white"><?php echo htmlspecialchars($row['klasCode']); ?></h1>
                <div class="col-lg-3 col-md-2 col-sm-12">
                    <div class="p-3 bg-white" data-bs-theme="light">
                        <h3 class="text-black">Informatie</h3>
                        <ul class="text-black list-unstyled">
                            <li>
                            <?php 
                            if ($totaalLeerlingen > 0) {
                                $gemiddeldeScore = $totaalScore / $totaalLeerlingen;
                                $gemiddeldeScore = round($gemiddeldeScore);
                                echo "Gemiddelde score: " . $gemiddeldeScore;
                            } else {
                                echo "Geen leerlingen gevonden in deze klas.";
                            }
                            ?>
                            </li>
                        </ul>
                        <a style="cursor: pointer;" class="button button-gevaar w-100" data-bs-toggle="modal" data-bs-target="#verwijderen">Klas verwijderen</a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-8 col-sm-12">
                    <div class="p-3 bg-white" data-bs-theme="light">
                    <pre class="text-black" style="height: 250px;"><ul class="list-group list-group-numbered list-group-flush">
                    <?php 
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
                    </ul></pre>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2 col-sm-12"></div>
            </div>
        </div>
    </section>

    <script src="../../assets/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../../assets/js/jquery.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>