<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css">
    <meta charset="UTF-8">
    <style>
        .category-1 { color: #009966;}
        .category-2 { color: #ffcc00;}
        .category-3 { color: #ff6600;}
        .category-4 { color: #ff0000;}
        .category-5 { color: #990000;}
    </style>
</head>
<body>
    <h1>Upload Storm Data</h1>
        <form action="" method="post" enctype="multipart/form-data"> 
            <p> <label for="fileUpload">Choose a CSV file:</label> </p>
            <p> <input type="file" id="fileUpload" class="fileUpload" name="fileUpload" accept=".csv" required></p>
            <p> <button type="submit" name="upload">Upload and Process</button> </p>
         </form>
    <?php
    define('CATEGORY_1', 74); 
    define('CATEGORY_2', 96); 
    define('CATEGORY_3', 111);
    define('CATEGORY_4', 130);
    define('CATEGORY_5', 157);

    //convert km/h to mph
    function convertToMph($kmh) {
        return round($kmh * 0.621371);
    }

    //function to determine the Saffir-Simpson category
    function getCategory($mph) {
        if ($mph >= CATEGORY_5) {
            return 'category-5';
        } elseif ($mph >= CATEGORY_4) {
            return 'category-4';
        } elseif ($mph >= CATEGORY_3) {
            return 'category-3';
        } elseif ($mph >= CATEGORY_2) {
            return 'category-2';
        } else {
            return 'category-1';
        }
    }

    //process the uploaded CSV file
    function processCsv($file) {
        $data = [];
        if (($handle = fopen($file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',', '"', '\\')) !== FALSE) {
                //CSV format: date, storm name, max wind speed (km/h)
                if (count($row) === 3) {
                    $date = $row[0];
                    $stormName = $row[1];
                    $maxWindKmH = (int)$row[2];
                    $maxWindMph = convertToMph($maxWindKmH);
                    $categoryClass = getCategory($maxWindMph);
                    $data[] = [
                        'date' => $date,
                        'stormName' => $stormName,
                        'maxWindKmH' => $maxWindKmH,
                        'maxWindMph' => $maxWindMph,
                        'categoryClass' => $categoryClass
                    ];
                }
            }
            fclose($handle);
        }
        return $data;
    }

    //handling file upload
    if (isset($_POST['upload']) && isset($_FILES['fileUpload'])) {
        $file = $_FILES['fileUpload']['tmp_name'];
        if ($file && is_uploaded_file($file)) {
            $stormData = processCsv($file);
            if ($stormData) {
                echo "<h1>Processed Storm Data</h1>";
                echo "<table border='1'><tr><th>Date</th><th>Storm Name</th><th>Max Wind (km/h)</th><th>Max Wind (mph)</th><th>Category</th></tr>";
                foreach ($stormData as $storm) {
                    echo "<tr class='" . $storm['categoryClass'] . "'>";
                    echo "<td>" . htmlspecialchars($storm['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($storm['stormName']) . "</td>";
                    echo "<td>" . $storm['maxWindKmH'] . "</td>";
                    echo "<td>" . $storm['maxWindMph'] . "</td>";
                    echo "<td>" . ucfirst(str_replace('-', ' ', $storm['categoryClass'])) . "</td>";
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
</body>
</html>