<?php
require('../config/conn.php');

if(!isset($_SESSION['user_id'])){
  echo header('Location: inloggen');
  exit;
} else {
    $klasDocent = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM klassen WHERE klasDocent = :klasDocent");
    $stmt->bindParam(':klasDocent', $klasDocent);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result['count'] >= 10){
        $limiet = true;
    } else {
        $limiet = false;
    }
}

if(isset($_POST['aanmaken'])){
    $klascode = $_POST['klascode'];
    $klasdocent = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM klassen WHERE klasCode = :klasCode");
    $stmt->bindParam(':klasCode', $klascode);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result['count'] > 0) {
        $_SESSION['error'] = 'Klas bestaat al.';
        echo header('Location: klas-aanmaken');
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO klassen (klasCode, klasDocent) VALUES (:klasCode, :klasDocent)");
        $stmt->bindParam(':klasCode', $klascode);
        $stmt->bindParam(':klasDocent', $klasdocent);

        if($stmt->execute()) {
            $_SESSION['succes'] = 'Klas aangemaakt!';
            echo header('Location: klas-aanmaken');
            exit;
        } else {
            $_SESSION['error'] = 'Er is een fout opgetreden tijdens het aanmaken van de klas.';
            echo header('Location: klas-aanmaken');
            exit;
        }
    }
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
    <style>
        .klas {
            letter-spacing: calc(6vw - 12px);
            padding-left: calc(6vw - 12px);
            text-align: center;
        }
    </style>
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
                    <form action="" method="POST">
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
                                <li class="breadcrumb-item active" aria-current="page">Klas aanmaken</li>
                            </ol>
                        </nav>
                        <h1 class="text-white">Klas aanmaken</h1>
                        <?php if(!$limiet){ ?>
                        <label for="naam" class="text-white">Klascode <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <input type="text" name="klascode" class="form-control form-input bg-transparent shadow-none rounded-0 text-uppercase klas" placeholder="KLAS" aria-label="KLAS" aria-describedby="button-addon" maxlength="4" required autofocus>
                        </div>
                        <button class="button w-100" name="aanmaken" type="submit" id="button-addon">Aanmaken</button>
                        <?php } else { ?>
                        <p class="lead">U heeft uw limiet bereikt.</p>
                        <?php } ?>
                        </div> 

                    </form>
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