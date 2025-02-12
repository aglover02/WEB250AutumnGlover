<?php

$yards = process_yards();
$feet = process_feet();
$inches = process_inches();

function process_yards() {
    if (isset($_GET["yards"]) && is_numeric($_GET["yards"])) {
        $yards = $_GET["yards"];
        $feet = $yards * 3;
        $inches = $yards * 36;
        $result = $yards . " yards is " . $feet . " feet or " . $inches . " inches.";
    }
    else {
        $result = "";
    }

    return $result;
}

function process_feet() {
    if (isset($_POST["feet"]) && is_numeric($_POST["feet"])) {
        $feet = $_POST["feet"];
        $yards = $feet / 3;
        $inches = $feet * 12;
        $result = $feet . " feet is " . $yards . " yards or " . $inches . " inches.";
    }
    else {
        $result = "";
    }

    return $result;
}

function process_inches() {
    if (isset($_POST["inches"]) && is_numeric($_POST["inches"])) {
        $inches = $_POST["inches"];
        $yards = $inches / 36;
        $feet = $inches / 12;
        $result = $inches . " inches is " . $yards . " yards or " . $feet . " feet.";
    }
    else {
        $result = "";
    }

    return $result;
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Length Conversions</title>
    <link rel="stylesheet" href="/styles.css">
</head>
  <body>
    <h1>Length Conversions</h1>
        <form method="GET">
            <p>
                <label for="yards">Enter yards:</label>
                <input type="text" id="yards" name="yards">
                <input type="submit" value="Submit">
            </p>
            <p>
                <output><?=$yards?></output>
            </p>
        </form>
        <form method="POST">
            <p>
                <label for="feet">Enter feet:</label>
                <input type="text" id="feet" name="feet">
                <input type="submit" value="Submit">
            </p>
            <p>
                <output><?=$feet?></output>
            </p>
        </form>
        <form method="POST">
            <p>
                <label for="inches">Enter inches:</label>
                <input type="text" id="inches" name="inches">
                <input type="submit" value="Submit">
            </p>
            <p>
                <output><?=$inches?></output>
            </p>
        </form>
</body>
</html>
