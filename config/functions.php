<?php 
function generateQuestions($numQuestions, $level)
{
    global $questions;

    $questions = array();

    while (count($questions) < $numQuestions) {
        if($level == 1){
            $question1 = rand(1, 10);
            $question2 = rand(1, 10);
        } elseif($level == 2){
            $question1 = rand(1, 10);
            $question2 = rand(1, 10);
        } elseif($level == 3){
            $question1 = rand(1, 15);
            $question2 = rand(1, 15);
        } elseif($level == 4){
            $question1 = rand(5, 20);
            $question2 = rand(5, 20);
        } elseif($level >= 5){
            $question1 = rand(5, 25);
            $question2 = rand(5, 25);
        }
        $operators = ['+', '-', '*', '/'];
        $randomOperator = $operators[array_rand($operators)];

        switch ($randomOperator) {
            case '+':
                $answer = $question1 + $question2;
                break;
            case '-':
                $answer = $question1 - $question2;
                break;
            case '*':
                $answer = $question1 * $question2;
                break;
            case '/':
                if ($question1 % $question2 == 0) {
                    $answer = $question1 / $question2;
                } else {
                    continue 2;
                }
                break;
        }

        $questions["$answer"] = "$question1 $randomOperator $question2";
    }

    $_SESSION['questions'] = $questions;
}

function generateCloseAlternatives($correctAnswer) {
    $alternatives = [];

    // Add alternatives close by one unit
    $alternatives[] = $correctAnswer + 1;
    $alternatives[] = $correctAnswer - 1;

    // Add alternatives close by two units
    $alternatives[] = $correctAnswer + 2;
    $alternatives[] = $correctAnswer - 2;

    return $alternatives;
}

