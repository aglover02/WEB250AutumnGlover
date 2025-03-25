<!DOCTYPE html>
<html lang="en">
<head>
<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css">
    <title>Earthquake Data Viewer</title>
    <style>
        .micro { color:rgb(2, 146, 26); }
        .minor { color:rgba(122, 255, 51, 0.64); }
        .light { color:rgb(212, 212, 53); }
        .moderate { color:rgba(255, 204, 0, 0.69); }
        .strong { color:rgb(251, 145, 40); }
        .major { color: #ff6600; }
        .great { color:rgb(187, 9, 9); }
    </style>
</head>
<body>
    <h1>Upload Earthquake Data</h1>
    <form action="" method="post" enctype="multipart/form-data"> 
        <p><label for="fileUpload">Choose a CSV file:</label></p>
        <p><input type="file" id="fileUpload" name="fileUpload" class="fileUpload" accept=".csv" required></p>
        <p><button type="submit" name="upload">Upload and Process</button></p>
    </form>

    <?php
    define('MICRO', 2.0);
    define('MINOR', 4.0);
    define('LIGHT', 6.0);
    define('MODERATE', 7.0);
    define('STRONG', 8.0);
    define('MAJOR', 10.0);

    // Function to determine magnitude description
    function get_magnitude_description($magnitude) {
        if ($magnitude < MICRO) return 'micro';
        if ($magnitude < MINOR) return 'minor';
        if ($magnitude < LIGHT) return 'light';
        if ($magnitude < MODERATE) return 'moderate';
        if ($magnitude < STRONG) return 'strong';
        if ($magnitude < MAJOR) return 'major';
        return 'great';
    }

    // Process the uploaded CSV file
    function process_csv($file) {
        $data = [];
        if (($handle = fopen($file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',', '"', '\\')) !== FALSE) {
                if (count($row) === 3) {
                    $date = $row[0];
                    $earthquakeName = $row[1];
                    $magnitude = (float)$row[2];
                    $description = get_magnitude_description($magnitude);
                    $data[] = [
                        'date' => $date,
                        'earthquakeName' => $earthquakeName,
                        'magnitude' => $magnitude,
                        'description' => $description
                    ];
                }
            }
            fclose($handle);
        }
        usort($data, function ($a, $b) {
            return $b['magnitude'] <=> $a['magnitude'];
        });
        return $data;
    }

    // Handling file upload
    if (isset($_POST['upload']) && isset($_FILES['fileUpload'])) {
        $file = $_FILES['fileUpload']['tmp_name'];
        if ($file && is_uploaded_file($file)) {
            $earthquakeData = process_csv($file);
            if ($earthquakeData) {
                echo "<h1>Processed Earthquake Data</h1>";
                echo "<table border='1'><tr><th>Date</th><th>Earthquake Name</th><th>Magnitude</th><th>Description</th></tr>";
                foreach ($earthquakeData as $earthquake) {
                    echo "<tr class='" . htmlspecialchars($earthquake['description']) . "'>";
                    echo "<td>" . htmlspecialchars($earthquake['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($earthquake['earthquakeName']) . "</td>";
                    echo "<td>" . number_format($earthquake['magnitude'], 1) . "</td>";
                    echo "<td>" . ucfirst($earthquake['description']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No valid data found in the file.";
            }
        } else {
            echo "There was an error uploading the file.";
        }
    }
    ?>
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
