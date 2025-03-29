<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jersey+10&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/styles.css">
    <title>Tsunami Data Viewer</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Tsunami Data Viewer</h1>
    <form method="post">
        <button type="submit" name="loadData">Load Tsunami Data</button>
    </form>

    <?php

    //URL and SPARQL query for getting the tsunami data
    define('WIKIDATA_URL', "https://query.wikidata.org/sparql?query=%23%20Tsunamis%20by%20Date%0A%0ASELECT%20%3FDate%20%3FTsunami%20%3FContinent%20%3FCountry%0AWHERE%20%0A%7B%0A%20%20%3FtsunamiItem%20wdt%3AP31%20wd%3AQ8070.%0A%20%20%7B%0A%20%20%20%20%3FtsunamiItem%20rdfs%3Alabel%20%3FTsunami.%0A%20%20%20%20FILTER((LANG(%3FTsunami))%20%3D%20%22en%22)%0A%20%20%7D%0A%20%20%0A%20%20%3FtsunamiItem%20wdt%3AP585%20%3FDate.%0A%20%20%0A%20%20%3FtsunamiItem%20wdt%3AP17%20%3FcountryItem.%0A%20%20%7B%0A%20%20%20%20%3FcountryItem%20rdfs%3Alabel%20%3FCountry.%0A%20%20%20%20FILTER((LANG(%3FCountry))%20%3D%20%22en%22)%0A%20%20%7D%0A%20%20%0A%20%20%3FcountryItem%20wdt%3AP30%20%3FcontinentItem.%0A%20%20%7B%0A%20%20%20%20%3FcontinentItem%20rdfs%3Alabel%20%3FContinent.%0A%20%20%20%20FILTER((LANG(%3FContinent))%20%3D%20%22en%22)%0A%20%20%7D%0A%7D%0AORDER%20BY%20(%3FDate)&format=json");

    main();

    function main() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["loadData"])) {
            echo "<h3>Tsunami Data from Wikidata</h3>";
            echo get_data(WIKIDATA_URL);
        }
    }

    //get and process data
    function get_data($url) {
        try {
            $data = get_raw_data($url);
            //process the data into formatted records
            $records = get_records($data);
            return $records;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    function get_raw_data($url) {
        $user_agent = "Required by Wikidata";
        $options  = array("http" => array("user_agent" => $user_agent));
        $context  = stream_context_create($options);

        //get data from the URL
        $data = @file_get_contents($url, false, $context);
        if ($data === false) {
            throw new Exception("Failed to fetch data from API.");
        }

        //decode JSON data
        $data = json_decode($data, true);
        if ($data === null) {
            throw new Exception("Invalid JSON received from API.");
        }

        return $data;
    }

    //process and sort records
    function get_records($data) {
        $records = [];
        //get relevant information from each data entry
        foreach ($data["results"]["bindings"] as $array) {
            $record = get_record($array);
            array_push($records, $record);
        }
        //sort records by continent and country
        usort($records, "compare_records");
        return format_table($records);
    }

    //get individual record details
    function get_record($array) {
        return [
            "date" => $array["Date"]["value"],
            "tsunami" => $array["Tsunami"]["value"],
            "continent" => $array["Continent"]["value"],
            "country" => $array["Country"]["value"]
        ];
    }

    //comparison function for sorting records
    function compare_records($a, $b) {
        if ($a["continent"] === $b["continent"]) {
            return strcmp($a["country"], $b["country"]);
        }
        return strcmp($a["continent"], $b["continent"]);
    }

    //format the records as an HTML table with color-coded rows
    function format_table($records) {
        $result = "<table><tr><th>Date</th><th>Tsunami</th><th>Continent</th><th>Country</th></tr>";

        foreach ($records as $record) {
            $color = get_continent_color($record["continent"]);
            $result .= "<tr style=\"background-color: $color;\">";
            $result .= "<td>" . htmlspecialchars($record["date"]) . "</td>";
            $result .= "<td>" . htmlspecialchars($record["tsunami"]) . "</td>";
            $result .= "<td>" . htmlspecialchars($record["continent"]) . "</td>";
            $result .= "<td>" . htmlspecialchars($record["country"]) . "</td>";
            $result .= "</tr>";
        }

        $result .= "</table>";
        return $result;
    }

    //return color based on continent
    function get_continent_color($continent) {
        $colors = [
            "Africa" => "#FFDDC1",
            "Asia" => "#FFABAB",
            "Europe" => "#FFC3A0",
            "North America" => "#D5AAFF",
            "South America" => "#85E3FF",
            "Oceania" => "#B9FBC0",
            "Antarctica" => "#E4C1F9"
        ];
        return $colors[$continent] ?? "#FFFFFF";
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
