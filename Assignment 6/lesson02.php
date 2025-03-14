<?php
date_default_timezone_set("UTC"); 
$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");
$hostName = gethostname();
$ipAddress = $_SERVER['SERVER_ADDR'] ?? 'Unknown';
$operatingSystem = PHP_OS;
$phpVersion = PHP_VERSION;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <title>Server Information</title>
  <link rel="stylesheet" href="/styles.css">
</head>
<body>
  <h1>Server Information</h1>
  <ul class="list">
    <li>Date: <?php echo $currentDate; ?></li>
    <li>Time: <?php echo $currentTime; ?></li>
    <li>Host Name: <?php echo $hostName; ?></li>
    <li>IP Address: <?php echo $ipAddress; ?></li>
    <li>Operating System: <?php echo $operatingSystem; ?></li>
    <li>PHP Version: <?php echo $phpVersion; ?></li>
  </ul>

  <h3> Navigation </h3>
    <section class="link">
    <ul class="navList">  
    <li><a href="/lesson01.php">Lesson 1</a></li>
          <li><a href="/lesson02.php">Lesson 2</a></li>
          <li><a href="/lesson03.php">Lesson 3</a></li>
          <li><a href="/lesson04.php">Lesson 4</a></li>
          <li><a href="/lesson05.php">Lesson 5</a></li>
          <li><a href="/lesson06.php">Lesson 6</a></li>
          <li><a href="/lesson07.php">Lesson 7</a></li>
          <li><a href="/lesson08.php">Lesson 8</a></li>
          <li><a href="/lesson09.php">Lesson 9</a></li>
          <li><a href="/lesson10.php">Lesson 10</a></li>
          <li><a href="/lesson11.php">Lesson 11</a></li>
          <li><a href="/lesson12.php">Lesson 12</a></li>
          <li><a href="/final_project.php">Final Project</a></li>
        </ul>
    </section>
</body>
</html>