<?php 
require('../config/conn.php');

if(isset($_POST['registreren'])){
    
    function sanitize_username($username) {
        return preg_replace('/[^A-Za-z0-9_.]/', '', $username);
    }
    
    function hash_password($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    $gebruikersnaam = sanitize_username($_POST['gebruikersnaam']);
    $wachtwoord = hash_password($_POST['wachtwoord']);
    
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM docenten WHERE gebruikersnaam = :gebruikersnaam");
        $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result['count'] > 0) {
            $_SESSION['error'] = 'Gebruikersnaam bestaat al.';
            echo header('Location: registreren');
            exit;
        } else {
            $stmt = $conn->prepare("INSERT INTO docenten (gebruikersnaam, wachtwoord) VALUES (:gebruikersnaam, :wachtwoord)");
            $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
            $stmt->bindParam(':wachtwoord', $wachtwoord);
            
            if($stmt->execute()) {
                $_SESSION['succes'] = 'Registratie succesvol! U kunt nu inloggen.';
                echo header('Location: inloggen');
                exit;
            } else {
                $_SESSION['error'] = 'Er is een fout opgetreden tijdens het registreren.';
                echo header('Location: registreren');
                exit;
            }
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
                        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="../" class="fw-bold text-decoration-none text-white">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Registreren</li>
                            </ol>
                        </nav>
                        <h1 class="text-white">Als docent registreren</h1>
                        <label for="naam" class="text-white">Gebruikersnaam <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <input type="text" name="gebruikersnaam" class="form-control form-input bg-transparent shadow-none rounded-0" placeholder="Gebruikersnaam" aria-label="Gebruikersnaam" aria-describedby="button-addon" required="" autofocus>
                        </div>
                        <label for="naam" class="text-white">Wachtwoord <span class="text-danger">*</span></label>
                        <div class="mb-3">
                            <input type="password" name="wachtwoord" class="form-control form-input bg-transparent shadow-none rounded-0" placeholder="Wachtwoord" aria-label="Wachtwoord" aria-describedby="button-addon" autofocus>
                        </div>
                        <button class="button w-100" name="registreren" type="submit" id="button-addon">Registreren</button>
                        <p class="text-center mt-3">Al een account? <a href="inloggen" class="text-white">Inloggen</a></p>    
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