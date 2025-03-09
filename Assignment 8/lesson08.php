<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tsunami Data Viewer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .Asia { background-color: #ffd1dc; }
        .Africa { background-color: #d1ffd1; }
        .Europe { background-color: #d1d1ff; }
        .Oceania { background-color: #ffffd1; }
        .Americas { background-color: #ffd1a6; }
    </style>
</head>
<body>
    <h1>Tsunami Data Viewer</h1>
    <form method="get">
        <button type="submit">Load Tsunami Data</button>
    </form>

    <?php
    define('WIKIDATA_URL', 'https://query.wikidata.org/sparql');
    define('TSUNAMI_QUERY', '
        SELECT ?Date ?Tsunami ?Continent ?Country WHERE {
          ?tsunami wdt:P31 wd:Q7942;
                   wdt:P276 ?location;
                   wdt:P585 ?Date.
          ?location wdt:P17 ?country.
          ?country wdt:P30 ?continent.
          ?tsunami rdfs:label ?Tsunami.
          ?continent rdfs:label ?Continent.
          ?country rdfs:label ?Country.
          FILTER(LANG(?Tsunami) = "en")
          FILTER(LANG(?Continent) = "en")
          FILTER(LANG(?Country) = "en")
        }
        ORDER BY ?Continent ?Country
    ');

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        echo loadTsunamiData();
    }

    function loadTsunamiData() {
        try {
            $rawData = fetchData(WIKIDATA_URL, TSUNAMI_QUERY);
            $records = parseData($rawData);
            return displayData($records);
        } catch (Exception $e) {
            return '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }

    function fetchData($url, $query) {
        $fullUrl = $url . '?query=' . urlencode($query) . '&format=json';
        $context = stream_context_create(['http' => ['user_agent' => 'PHP Fetch']]);
        $response = file_get_contents($fullUrl, false, $context);
        if ($response === false) {
            throw new Exception('Failed to fetch data.');
        }
        return json_decode($response, true);
    }

    function parseData($data) {
        $records = [];
        foreach ($data['results']['bindings'] as $entry) {
            $records[] = [
                'date' => $entry['Date']['value'],
                'tsunami' => $entry['Tsunami']['value'],
                'continent' => $entry['Continent']['value'],
                'country' => $entry['Country']['value']
            ];
        }
        usort($records, fn($a, $b) => strcmp($a['continent'] . $a['country'], $b['continent'] . $b['country']));
        return $records;
    }

    function displayData($records) {
        if (empty($records)) {
            return '<p>No tsunami data available.</p>';
        }

        $output = '<table><tr><th>Date</th><th>Tsunami</th><th>Continent</th><th>Country</th></tr>';
        foreach ($records as $record) {
            $class = htmlspecialchars($record['continent']);
            $output .= '<tr class="' . $class . '">';
            $output .= '<td>' . htmlspecialchars($record['date']) . '</td>';
            $output .= '<td>' . htmlspecialchars($record['tsunami']) . '</td>';
            $output .= '<td>' . htmlspecialchars($record['continent']) . '</td>';
            $output .= '<td>' . htmlspecialchars($record['country']) . '</td>';
            $output .= '</tr>';
        }
        $output .= '</table>';

        return $output;
    }
    ?>
</body>
</html>
