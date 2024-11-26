<?php 
require('config/conn.php');

$bg = "gradient";
$score = 0;

if(empty($_SESSION['gebruiker'])){

   if(isset($_POST['instellen'])){
     $naam = $_POST['naam'];
     $klascode = $_POST['klascode'];
     if(isset($_POST['multiple_choice'])){
        $multiple_choice = $_POST['multiple_choice'];
     }

     $welke = !$klascode ? 'oefeningen' : 'klasoefeningen';

     $stmt = $conn->prepare('SELECT * FROM klassen WHERE klasCode = :klasCode');
     $stmt->bindParam(':klasCode', $klascode);
     $stmt->execute();
     $klascodeBestaat = $stmt->fetchAll(PDO::FETCH_ASSOC);     

     if(!empty($klascode)){
        if($klascodeBestaat == null){
        $_SESSION['error'] = 'Klascode bestaat niet.';
        echo header('Location: spel');
        exit;
        }
     }

     $_SESSION['gebruiker'] = $naam;
     $_SESSION['start_time'] = time();

     if(!$klascode){
        $stmt = $conn->query('SELECT * FROM oefeningen ORDER BY id ASC');
        $leerlingen = $stmt->fetchAll(PDO::FETCH_ASSOC);
     } else {
        $stmt = $conn->prepare('SELECT * FROM klasoefeningen WHERE klas = :klas ORDER BY id ASC');
        $stmt->bindParam(':klas', $klascode);
        $stmt->execute();
        $leerlingen = $stmt->fetchAll(PDO::FETCH_ASSOC);        
     }

     $entryBestaat = false; 

     foreach($leerlingen as $leerling){
        $leerlingNaam = strtolower($leerling['naam']);
        $naam = strtolower($naam);
        if($naam == $leerlingNaam){
           $entryBestaat = true;
           $kansen = $leerling['kansen'];
           $kansen++;
           
           if(!$klascode){
            $stmt = $conn->prepare('UPDATE oefeningen SET kansen = :kansen WHERE naam = :naam');
            $stmt->bindParam(':kansen', $kansen);
            $stmt->bindParam(':naam', $naam);
            $stmt->execute();
           } else {
            $stmt = $conn->prepare('UPDATE klasoefeningen SET kansen = :kansen WHERE naam = :naam AND klas = :klas');
            $stmt->bindParam(':kansen', $kansen);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':klas', $klascode);
            $stmt->execute();
           }

           if(!$klascode){
                if($multiple_choice == "on"){
                    echo header('Location: spel-');
                    exit;
                } else {
                    echo header('Location: spel');
                    exit;
                }
           } else {
                if($multiple_choice == "on"){
                    echo header('Location: spel-?klas='.$klascode.'');
                    exit;
                } else {
                    echo header('Location: spel?klas='.$klascode.'');
                    exit;
                }
           }
        }
     }

     if(!$entryBestaat){
        if(!$klascode){
            $stmt = $conn->prepare('INSERT INTO oefeningen (score, kansen, naam) VALUES (:score, :kansen, :naam)');
            $stmt->bindValue(':score', 0);
            $stmt->bindValue(':kansen', 1);
            $stmt->bindParam(':naam', $naam);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare('INSERT INTO klasoefeningen (score, kansen, naam, klas) VALUES (:score, :kansen, :naam, :klas)');
            $stmt->bindValue(':score', 0);
            $stmt->bindValue(':kansen', 1);
            $stmt->bindParam(':naam', $naam);
            $stmt->bindParam(':klas', $klascode);
            $stmt->execute();
        }

        if(!$klascode){
            if($multiple_choice == "on"){
                echo header('Location: spel-');
                exit;
            } else {
                echo header('Location: spel');
                exit;
            }
        } else {
            if($multiple_choice == "on"){
                echo header('Location: spel-?klas='.$klascode.'');
                exit;
            } else {
                echo header('Location: spel?klas='.$klascode.'');
                exit;
            }
        }
     }
   }

   echo '<!DOCTYPE html>
   <html lang="en">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="stylesheet" href="assets/css/main.css">
       <link rel="stylesheet" href="assets/css/bg.css">
       <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
       <script src="https://kit.fontawesome.com/20865e7038.js" crossorigin="anonymous"></script>
       <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
       <title>Het Ultieme Rekenspel!</title>
   </head>
   <body data-bs-theme="dark" class="gradient">

       <div id="spinner" class="show gradient position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-white" role="status" style="width: 3rem; height: 3rem;"></div>
       </div>

       <div id="stars"></div>
       <div id="stars2"></div>
       <div id="stars3"></div>

       <section class="py-5 mt-5 mb-5 vh-100 d-flex align-items-center">
           <div class="container">
               <div class="row">
                   <div class="col-lg-3 col-md-2 col-sm-12"></div>
                   <div class="col-lg-6 col-md-8 col-sm-12">
                       <form action="" method="POST" id="nameForm">
                           <div class="row">';
                           if(isset($_SESSION['error'])) {
                            echo '<div class="alert alert-danger" role="alert">
                            '.$_SESSION['error'].'
                            </div>';
                            unset($_SESSION['error']);
                           }
                           echo '<div class="col-lg-8 col-md-6 col-sm-12">
                            <label for="naam" class="text-white">Wat is je naam? <span class="text-danger">*</span></label>
                            <div class="mb-3">
                                <input type="text" name="naam" class="form-control form-input bg-transparent shadow-none rounded-0" placeholder="Begin hier met typen..." aria-label="Begin hier met typen..." aria-describedby="button-addon" required="" autofocus>
                            </div>
                           </div>
                           <div class="col-lg-4 col-md-6 col-sm-12">
                            <label for="naam" class="text-white">Klascode</label>
                            <div class="mb-3">
                                <input type="text" name="klascode" class="form-control form-input bg-transparent shadow-none rounded-0 text-uppercase" maxlength="4" placeholder="Optioneel" aria-label="Optioneel" aria-describedby="button-addon" autofocus>
                            </div>
                           </div>
                           </div> 
                           <button class="button float-end" name="instellen" type="submit" id="button-addon">Starten</button>
                           <input type="checkbox" name="multiple_choice" class="form-check-input" id="checkbox"> <label for="checkbox">Wil je multiple choice vragen beantwoorden?</label><br>
                           <label><small class="text-white">*Je naam wordt gebruikt voor het leaderboard.</small></label>
                       </form>
                   </div>
                   <div class="col-lg-3 col-md-2 col-sm-12"></div>
               </div>
           </div>
       </section>
       <script src="assets/bootstrap/js/bootstrap.bundle.js"></script>
       <script src="assets/js/jquery.min.js"></script>
       <script src="assets/js/script.js"></script>
   </body>
   </html>';
   exit;
} else {
    if(isset($_GET['klas'])){
        $klascode = $_GET['klas'];
    } else {
        $klascode = null;
    }

    $welke = !$klascode ? 'oefeningen' : 'klasoefeningen';

    if(!$klascode){
        $stmt = $conn->prepare('SELECT * FROM oefeningen WHERE naam = :naam');
        $stmt->bindParam(':naam', $_SESSION['gebruiker']);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare('SELECT * FROM klasoefeningen WHERE naam = :naam AND klas = :klas');
        $stmt->bindParam(':naam', $_SESSION['gebruiker']);
        $stmt->bindParam(':klas', $klascode);
        $stmt->execute();
    }
    $leerling = $stmt->fetch(PDO::FETCH_ASSOC);

    if($leerling['kansen'] == 1){
        $count = 15;
        $level = 1;
    } elseif($leerling['kansen'] == 2){
        $count = 25;
        $level = 2;
    } elseif($leerling['kansen'] == 3){
        $count = 50;
        $level = 3;
    } elseif($leerling['kansen'] == 4){
        $count = 50;
        $level = 4;
    } elseif($leerling['kansen'] >= 5){
        $count = 75;
        $level = 5;
    }

    $entryBestaat = false; 

    if (!isset($_SESSION['questions'])) {
        generateQuestions($count, $level);
    }

    $questions = $_SESSION['questions'];

    if(isset($_POST['nakijken']) || (isset($_POST['sub']) && $_POST['sub'] == 1)) {

        $naam = $_SESSION['gebruiker'];
        if(isset($_GET['klas'])){
            $klascode = $_GET['klas'];
        } else {
            $klascode = null;
        }

        echo '<!DOCTYPE html>
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
        
            <div id="stars"></div>
            <div id="stars2"></div>
            <div id="stars3"></div>
        
            <section class="py-5 mb-5 vh-100 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-2 col-sm-12"></div>
                        <div class="col-lg-6 col-md-8 col-sm-12">
                           <div class="p-3 bg-white" data-bs-theme="light">
                              <h1 class="text-black">👀 Resultaten</h1>
                                <pre class="text-black p-3" style="height: 200px;">';

        $antwoorden = $_POST['antwoorden'];
        
        $operatorErrors = array(
            '+' => 0,
            '-' => 0,
            '*' => 0,
            '/' => 0,
        );
        
        foreach ($questions as $key => $correctQuestion) {
            $answerIndex = array_search($key, array_keys($questions));
        
            if (isset($antwoorden[$answerIndex]) && $antwoorden[$answerIndex] !== '') {
                echo "Vraag: $correctQuestion. Jouw antwoord was " . $antwoorden[$answerIndex] . "<br>";
                $submittedAnswer = eval('return ' . $antwoorden[$answerIndex] . ';');
                $correctAnswer = eval('return ' . $key . ';');
        
                if ($submittedAnswer === $correctAnswer) {
                    echo "Goed antwoord!<br><br>";
                    $score++;
                } else {
                    echo "Fout antwoord, probeer overnieuw.<br><br>";
                    preg_match('/[+\-*\/]/', $correctQuestion, $matches);
                    if (!empty($matches)) {
                        $operator = $matches[0];
                        $operatorErrors[$operator]++;
                    }
                }
            } else {
                echo "Vraag: $correctQuestion. Geen antwoord ingevuld. Fout antwoord.<br><br>";
            }
        }
        
        $maxErrors = max($operatorErrors);
        $maxErrorOperator = array_search($maxErrors, $operatorErrors);
        
        echo "Je score is: " . $score . " / $count";
        echo "<br><br>Je maakte de meeste fouten bij sommen met deze operator: " . str_replace('*', 'x', $maxErrorOperator) . ". Oefen wat meer!";


        if(!$klascode){
            $stmt = $conn->query('SELECT * FROM oefeningen ORDER BY score ASC LIMIT 10');
        } else {
            $stmt = $conn->prepare('SELECT * FROM klasoefeningen WHERE klas = :klas ORDER BY score ASC LIMIT 10');
            $stmt->bindParam(':klas', $klascode);
            $stmt->execute();
        }
        $scoreLeaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $scoreEerst = $scoreLeaderboard[0];

        if($score > $scoreEerst['score'] && $score > 0){
            $behaald = 'goed';
        } else {
            $behaald = 'helaas';
        }
        echo '</pre></div>
                <div class="mt-3 mb-3">';
                if(!$klascode){
                    echo '<a href="leaderboard?status='.$behaald.'" class="text-white text-decoration-none fw-bold float-end">Leaderboard zien <i class="fa-solid fa-arrow-right"></i></a>';
                } else {
                    echo '<a href="leaderboard?status='.$behaald.'&klas='.$klascode.'" class="text-white text-decoration-none fw-bold float-end">Leaderboard zien <i class="fa-solid fa-arrow-right"></i></a>';
                }
                echo '</div>
            </div>
            <div class="col-lg-3 col-md-2 col-sm-12"></div>
        </div>
        </div>
        </section>
        <script src="assets/bootstrap/js/bootstrap.bundle.js"></script>
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/script.js"></script>
        </body>
        </html>';

        if ($score == 0) {
            generateQuestions($count, $level);
        }

        try {
            if(!$klascode){
                $stmt = $conn->query('SELECT * FROM oefeningen ORDER BY id ASC');
            } else {
                $stmt = $conn->prepare('SELECT * FROM klasoefeningen WHERE klas = :klas ORDER BY id ASC');
                $stmt->bindParam(':klas', $klascode);
                $stmt->execute();
            }
            $leerlingen = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($leerlingen as $leerling){
                $leerlingNaam = strtolower($leerling['naam']);
                $naam = strtolower($naam);
                if($naam == $leerlingNaam){
                    $entryBestaat = true;
                }
            }

            if(!$entryBestaat){
                if(!$klascode){
                    if($score > $leerling['score']){
                        $stmt = $conn->prepare('INSERT INTO oefeningen (naam, score) VALUES (:naam, :score)');
                        $stmt->bindParam(':naam', $naam);
                        $stmt->bindParam(':score', $score);
                        $stmt->execute();
                    } else {
                        $stmt = $conn->prepare('INSERT INTO oefeningen (naam) VALUES (:naam)');
                        $stmt->bindParam(':naam', $naam);
                        $stmt->execute();
                    }
                } else {
                    if($score > $leerling['score']){
                        $stmt = $conn->prepare('INSERT INTO klasoefeningen (naam, score, klas) VALUES (:naam, :score, :klas)');
                        $stmt->bindParam(':naam', $naam);
                        $stmt->bindParam(':score', $score);
                        $stmt->bindParam(':klas', $klascode);
                        $stmt->execute();
                    } else {
                        $stmt = $conn->prepare('INSERT INTO klasoefeningen (naam, klas) VALUES (:naam, :klas)');
                        $stmt->bindParam(':naam', $naam);
                        $stmt->bindParam(':klas', $klascode);
                        $stmt->execute();
                    }
                }
            } else {
                if(!$klascode){
                    if($score > $leerling['score']){
                        $stmt = $conn->prepare('UPDATE oefeningen SET score = :score WHERE naam = :naam');
                        $stmt->bindParam(':score', $score);
                        $stmt->bindParam(':naam', $naam);
                        $stmt->execute();
                    }
                } else {
                    if($score > $leerling['score']){
                        $stmt = $conn->prepare('UPDATE klasoefeningen SET score = :score WHERE naam = :naam AND klas = :klas');
                        $stmt->bindParam(':score', $score);
                        $stmt->bindParam(':naam', $naam);
                        $stmt->bindParam(':klas', $klascode);
                        $stmt->execute();
                    }
                }
            }
            echo "Je score is: " . $score . " / $count";
            session_destroy();
            exit;
        } catch (PDOException $e) {
            echo "Databaseverbinding mislukt: " . $e->getMessage();
        }

    }
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
    <script src="https://kit.fontawesome.com/20865e7038.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <title>Het Ultieme Rekenspel!</title>
</head>
<body data-bs-theme="dark" class="<?php echo $bg; ?>">

    <div id="spinner" class="show gradient position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-border text-white" role="status" style="width: 3rem; height: 3rem;"></div>
    </div>

    <div id='stars'></div>
    <div id='stars2'></div>
    <div id='stars3'></div>

    <nav class="navbar navbar-expand-lg bg-transparent fixed-top">
     <div class="container-fluid">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link text-main font-main"><i class="fa-solid fa-stopwatch"></i> <span id="time" class="font-main">00:00</span> - <?php echo htmlspecialchars($_SESSION['gebruiker'], ENT_QUOTES, 'UTF-8'); ?></a>
            </li>
        </ul>
     </div>
    </nav>

    <div class="modal fade" id="beginModal" tabindex="-1" data-bs-theme="light">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header border-0 shadow-sm">
                    <h5 class="modal-title text-black fs-5 fw-bold" id="exampleModalLabel">De timer begint zo meteen te lopen.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body border-0 shadow-sm text-black">
                    Probeer zo veel mogelijk vragen te beantwoorden binnen de tijd. Als je veel correct hebt, kom je op het leaderboard te staan. Druk op "Starten" om het spel te beginnen.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="button" onclick="formStarten()">Starten 🏁</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="eindModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-theme="light">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <div class="modal-header border-0 shadow-sm">
                    <h5 class="modal-title text-black" id="exampleModalLabel">Tijd is op!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body border-0 shadow-sm text-black">
                    Je tijd is op. Je kan nu je vragen indienen.
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="button" onclick="formIndienen()">Oké</button>
                </div>
            </div>
        </div>
    </div>

    <section class="py-5 mt-5 mb-5 vh-100 d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-2 col-sm-12"></div>
                <div class="col-lg-6 col-md-8 col-sm-12">
                    <form action="" method="post" id="quizForm" onsubmit="return validateForm()">
                    <div class="tab-content" id="questionsTabContent">
                        <?php
                        $counter = 0;
                        foreach ($questions as $key => $question) { ?>
                            <div class="tab-pane <?php echo $counter === 0 ? 'fade show active' : ''; ?>" role="tabpanel" aria-labelledby="question<?php echo $key + 1; ?>-tab">
                                <span class="text-muted">Vraag <?php echo $counter + 1; ?> van de <?php echo $count; ?></span><br>  
                                <label for="antwoord<?php echo $counter; ?>" class="display-4 text-white">Wat is <?php echo str_replace('*', 'x', $question); ?>?</label>
                                <div class="input-group mb-3">
                                    <input type="number" name="antwoorden[<?php echo $counter; ?>]" id="antwoord" class="form-control form-input bg-transparent shadow-none rounded-0" placeholder="Begin hier met typen..." aria-label="Begin hier met typen..." aria-describedby="button-addon" autofocus>
                                </div>
                            </div>
                        <?php
                            $counter++;
                        } ?>


                        <input type="hidden" name="sub" value="1">

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="button button-alt rounded-class" id="prevButton" disabled>Vorige</button>
                            <div>
                                <button type="button" class="button button-alt rounded-class" id="nextButton">Volgende</button>
                                <button type="submit" name="nakijken" class="button button-alt rounded-class" id="submitButton" style="display: none;" disabled>Nakijken</button>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="col-lg-3 col-md-2 col-sm-12"></div>
            </div>
        </div>
    </section>
    <script src="assets/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script src="assets/js/timer.js"></script>
    <script>
        var totalSteps = <?php echo count($questions); ?>;

        window.onload = function () {
            var modalShown = <?php echo isset($_SESSION['modal_shown']) ? $_SESSION['modal_shown'] : 0; ?>;
            
            if (!modalShown) {
                $('#beginModal').modal('show');
                <?php $_SESSION['modal_shown'] = 1; ?>
            } else {
                var timerStarted = <?php echo isset($_SESSION['timer_started']) ? $_SESSION['timer_started'] : 0; ?>;
                if (timerStarted) {
                    $('#beginModal').modal('hide');
                    var duratie = <?php echo isset($_SESSION['timer']) ? $_SESSION['timer'] : 300; ?>;
                    var display = document.querySelector('#time');
                    var startTime = <?php echo isset($_SESSION['start_time']) ? $_SESSION['start_time'] : 0; ?>;
                    timerStarten(duratie, display, startTime);
                }
            }
        };

        function formStarten() {
            var duratie = <?php echo isset($_SESSION['timer']) ? $_SESSION['timer'] : 300; ?>;
            var display = document.querySelector('#time');
            var startTime = <?php echo isset($_SESSION['start_time']) ? $_SESSION['start_time'] : 0; ?>;
            
            var timerStarted = <?php echo isset($_SESSION['timer_started']) ? $_SESSION['timer_started'] : 0; ?>;
            if (!timerStarted) {
                timerStarten(duratie, display, startTime);
                <?php $_SESSION['timer_started'] = 1; ?>
            }

            $('#beginModal').modal('hide');
        }

        function validateForm() {
            var answers = document.querySelectorAll('input[name^="antwoorden"]');
            var atLeastOneAnswerProvided = false;

            answers.forEach(function(answer) {
                if (answer.value !== '') {
                    atLeastOneAnswerProvided = true;
                }
            });

            if (!atLeastOneAnswerProvided) {
                alert('Voer tenminste 1 antwoord in.');
                return false;
            }

            return true;
        }

        function formIndienen(){
            console.log("Geklikt");
            var form = document.getElementById("quizForm");
            form.submit();
        }
    </script>


</body>
</html>