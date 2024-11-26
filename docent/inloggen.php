<?php 
require('../config/conn.php');

if(isset($_SESSION['user_id'])){
    echo header('Location: home');
    exit;
}

if(isset($_POST['inloggen'])){
     
    function sanitize_username($username) {
        return preg_replace('/[^A-Za-z0-9_.]/', '', $username);
    }
    
    $gebruikersnaam = sanitize_username($_POST['gebruikersnaam']);
    $wachtwoord = $_POST['wachtwoord'];
    
    try {
        $stmt = $conn->prepare("SELECT id, gebruikersnaam, wachtwoord FROM docenten WHERE gebruikersnaam = :gebruikersnaam");
        $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user) {
            if(password_verify($wachtwoord, $user['wachtwoord'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['gebruikersnaam'] = $user['gebruikersnaam'];
                echo header('Location: home');
                exit;
            } else {
                $_SESSION['error'] = 'Onjuiste gebruikersnaam of wachtwoord.';
                echo header('Location: inloggen');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Onjuiste gebruikersnaam of wachtwoord.';
            echo header('Location: inloggen');
            exit;
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
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
                                <li class="breadcrumb-item"><a href="../" class="fw-bold text-decoration-none text-white">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Inloggen</li>
                            </ol>
                        </nav>
                        <h1 class="text-white">Als docent inloggen</h1>
                        <label for="naam" class="text-white">Gebruikersnaam <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <input type="text" name="gebruikersnaam" class="form-control form-input bg-transparent shadow-none rounded-0" placeholder="Gebruikersnaam" aria-label="Gebruikersnaam" aria-describedby="button-addon" required="" autofocus>
                        </div>
                        <label for="naam" class="text-white">Wachtwoord <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <input type="password" name="wachtwoord" class="form-control form-input bg-transparent shadow-none rounded-0" placeholder="Wachtwoord" aria-label="Wachtwoord" aria-describedby="button-addon" autofocus>
                        </div>
                        <button class="button w-100" name="inloggen" type="submit" id="button-addon">Inloggen</button>
                        <p class="text-center mt-3">Nog geen account? <a href="registreren" class="text-white">Registreren</a></p>

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