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
</body>
</html>