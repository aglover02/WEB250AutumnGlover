<?php
session_start();
if (isset($_GET['new_game'])) {
    session_unset(); //resets the session to start a new game
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/styles.css">
    <title>Math Expression Game</title>
</head>
<body>
    <h1>Math Expression Game</h1>
<?php
//call main function that does the game logic
main();

function main() {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case "GET":
            if (!isset($_SESSION['first_operand'])) {
                displayMainForm();
            } else {
                display_question();
            }
            break;
        
        case "POST":
            process_answer();
            display_question();
            break;

        default:
            echo "Unexpected request method: " . $_SERVER["REQUEST_METHOD"];
            break;
    }
}

function displayMainForm(): void
{
    echo <<<FORM
    <form method="GET">
        <p><label for="first_operand">Enter a number for the first operand:</label>
        <input type="number" id="first_operand" name="first_operand" min="1" max="10" required></p>
        <p><label for="total_questions">How many questions would you like to answer?</label>
        <input type="number" id="total_questions" name="total_questions" min="1" max="10" required></p>
        <p><input type="submit" value="Start Game"></p>
    </form>
    FORM;
}

function display_question() {
    //generate a random second operand 
    $second_operand = mt_rand(0, $_SESSION['first_operand']);
    $current_expression = $_SESSION['first_operand'] . " + " . $second_operand;
    $correct_answer = $_SESSION['first_operand'] + $second_operand;

    //display result if the game is over
    if ($_SESSION['remaining_questions'] <= 0) {
        echo "<h2>Game Over!</h2>";
        echo "<p>You answered " . $_SESSION['correct_answers'] . " out of " . $_SESSION['total_questions'] . " questions correctly.</p>";
        echo "<a href='?new_game=true'>Start a New Game</a>";
        session_unset();
        return;
    }

    //shows the current expression 
    echo "<h2>Question " . ($_SESSION['total_questions'] - $_SESSION['remaining_questions'] + 1) . " of " . $_SESSION['total_questions'] . "</h2>";
    echo "<p>Solve this: $current_expression = ";
    echo "<form method='POST'>";
    echo "<input type='text' name='user_answer' required>";
    echo "<input type='hidden' name='correct_answer' value='$correct_answer'>";
    echo "<input type='submit' value='Submit Answer'>";
    echo "</form>";
}

function process_answer() {
    //gets the user's answer and the correct answer from the form
    $user_answer = isset($_POST['user_answer']) ? intval($_POST['user_answer']) : null;
    $correct_answer = isset($_POST['correct_answer']) ? intval($_POST['correct_answer']) : 0;

    //check if the answer is correct
    if ($user_answer === $correct_answer) {
        $_SESSION['correct_answers']++;
        echo "<p class='feedback'>Correct!</p>";
    } else {
        echo "<p class='feedback'>Incorrect! The correct answer was $correct_answer.</p>";
    }

    $_SESSION['remaining_questions']--;
}

//handles starting game from the form
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['first_operand'], $_GET['total_questions'])) {
    $_SESSION['first_operand'] = intval($_GET['first_operand']);
    $_SESSION['total_questions'] = intval($_GET['total_questions']);
    $_SESSION['remaining_questions'] = $_SESSION['total_questions']; //tracks how many questions are left
    $_SESSION['correct_answers'] = 0; //tracks how many answers are correct
    echo "<h2>Game Started!</h2>";
    display_question();
}
?>
</body>
</html>