<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css">
    <title>Wildfire Data Viewer</title>
    <style>
        .magnitude-1 { background-color:rgb(124, 249, 124); } /* Very small areas */
        .magnitude-10 { background-color:rgb(255, 255, 105); } /* Small areas */
        .magnitude-100 { background-color:rgb(250, 197, 100); } /* Medium areas */
        .magnitude-1000 { background-color:rgb(255, 82, 82); } /* Large areas */
    </style>
</head>
<body>
    <h1>Upload Wildfire Data</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <p><label for="fileUpload">Choose a CSV file:</label></p>
        <p><input type="file" id="fileUpload" name="fileUpload" class="fileUpload" accept=".csv" required></p>
        <p><button type="submit" name="upload">Upload and Process</button></p>
    </form>

    <?php
    define('MAGNITUDE_1', 1);
    define('MAGNITUDE_10', 10);
    define('MAGNITUDE_100', 100);
    define('MAGNITUDE_1000', 1000);

    // Function to determine magnitude class
    function get_magnitude_class($area) {
        if ($area >= MAGNITUDE_1000) return 'magnitude-1000';
        if ($area >= MAGNITUDE_100) return 'magnitude-100';
        if ($area >= MAGNITUDE_10) return 'magnitude-10';
        return 'magnitude-1';
    }

    // Process the uploaded CSV file
    function process_csv($file) {
        $data = [];
        if (($handle = fopen($file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',', '"', '\\')) !== FALSE) {
                if (count($row) === 3) {
                    $date = $row[0];
                    $wildfireName = $row[1];
                    $area = (float)$row[2];
                    $magnitudeClass = get_magnitude_class($area);
                    $data[] = [
                        'date' => $date,
                        'wildfireName' => $wildfireName,
                        'area' => $area,
                        'magnitudeClass' => $magnitudeClass
                    ];
                }
            }
            fclose($handle);
        }
        usort($data, function ($a, $b) {
            return $b['area'] <=> $a['area'];
        });
        return $data;
    }

    // Handling file upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileUpload'])) {
        $file = $_FILES['fileUpload']['tmp_name'];
        if ($file && is_uploaded_file($file)) {
            $wildfireData = process_csv($file);
            if ($wildfireData) {
                echo "<h1>Processed Wildfire Data</h1>";
                echo "<table border='1' style='border-collapse: collapse; text-align: left;'>";
                echo "<tr><th>Date</th><th>Wildfire Name</th><th>Area Impacted (sq. km)</th></tr>";
                foreach ($wildfireData as $wildfire) {
                    echo "<tr class='" . htmlspecialchars($wildfire['magnitudeClass']) . "'>";
                    echo "<td>" . htmlspecialchars($wildfire['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($wildfire['wildfireName']) . "</td>";
                    echo "<td>" . number_format($wildfire['area']) . "</td>";
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
    <h3>Navigation</h3>
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
