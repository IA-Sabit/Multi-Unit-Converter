<!DOCTYPE html>
<html lang="en">
<head>
    <title>Advanced Unit Converter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="scripts.js" defer></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Advanced Unit Converter</h2>
        
   <form method="post" class="card p-4">
    <div class="mb-3">
        <label class="form-label">Unit Type:</label>
        <select name="unit_type" class="form-select" id="unitType" onchange="updateUnits()">
            <option value="temperature">Temperature</option>
            <option value="length">Length</option>
            <option value="weight">Weight</option>
            <option value="volume">Volume</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Enter Value:</label>
        <input type="number" step="any" name="value" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">From:</label>
        <select name="from_unit" id="fromUnit" class="form-select" required></select>
    </div>
    <div class="mb-3">
        <label class="form-label">To:</label>
        <select name="to_unit" id="toUnit" class="form-select" required></select>
    </div>
    <button type="submit" name="convert" class="btn btn-primary">Convert</button>
</form>
        <!-- Weather API Integration -->
        <div class="card p-4 mt-4">
            <h4>Convert Current Weather</h4>
            <input type="text" id="city" class="form-control mb-2" placeholder="Enter city">
            <button onclick="fetchWeather()" class="btn btn-secondary">Get Temperature</button>
            <div id="weatherResult"></div>
        </div>

        <!-- Conversion Result -->
        <?php
        if (file_exists(__DIR__ . '/db.php')) {
            require_once __DIR__ . '/db.php';
        } else {
            die("Error: db.php not found in " . __DIR__);
        }

        function convertUnit($value, $from, $to, $type) {
            $conversions = [
                'temperature' => [
                    'celsius' => ['fahrenheit' => fn($v) => ($v * 9/5) + 32, 'kelvin' => fn($v) => $v + 273.15],
                    'fahrenheit' => ['celsius' => fn($v) => ($v - 32) * 5/9, 'kelvin' => fn($v) => (($v - 32) * 5/9) + 273.15],
                    'kelvin' => ['celsius' => fn($v) => $v - 273.15, 'fahrenheit' => fn($v) => (($v - 273.15) * 9/5) + 32]
                ],
                'length' => [
                    'meter' => ['foot' => fn($v) => $v * 3.28084, 'inch' => fn($v) => $v * 39.3701],
                    'foot' => ['meter' => fn($v) => $v / 3.28084, 'inch' => fn($v) => $v * 12],
                    'inch' => ['meter' => fn($v) => $v / 39.3701, 'foot' => fn($v) => $v / 12]
                ],
                'weight' => [
                    'kilogram' => ['pound' => fn($v) => $v * 2.20462, 'ounce' => fn($v) => $v * 35.274],
                    'pound' => ['kilogram' => fn($v) => $v / 2.20462, 'ounce' => fn($v) => $v * 16],
                    'ounce' => ['kilogram' => fn($v) => $v / 35.274, 'pound' => fn($v) => $v / 16]
                ],
                'volume' => [
                    'liter' => ['gallon' => fn($v) => $v * 0.264172, 'milliliter' => fn($v) => $v * 1000],
                    'gallon' => ['liter' => fn($v) => $v / 0.264172, 'milliliter' => fn($v) => $v / 0.000264172],
                    'milliliter' => ['liter' => fn($v) => $v / 1000, 'gallon' => fn($v) => $v * 0.000264172]
                ]
            ];

            if ($from === $to || !isset($conversions[$type][$from][$to])) return $value;
            return $conversions[$type][$from][$to]($value);
        }

        if (isset($_POST['convert'])) {
            $value = isset($_POST['value']) ? floatval($_POST['value']) : 0;
            $from = isset($_POST['from_unit']) ? $_POST['from_unit'] : '';
            $to = isset($_POST['to_unit']) ? $_POST['to_unit'] : '';
            $type = isset($_POST['unit_type']) ? $_POST['unit_type'] : 'temperature';

            if ($type === 'temperature' && $from === 'kelvin' && $value < 0) {
                echo "<div class='alert alert-danger mt-3'>Error: Kelvin cannot be negative!</div>";
            } elseif (empty($from) || empty($to)) {
                echo "<div class='alert alert-danger mt-3'>Error: Please select 'From' and 'To' units!</div>";
            } else {
                $result = convertUnit($value, $from, $to, $type);
                $stmt = $pdo->prepare("INSERT INTO conversions (input_value, from_unit, to_unit, result, unit_type) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$value, $from, $to, $result, $type]);
                echo "<div class='alert alert-success mt-3'>Result: $result " . ucfirst($to) . "</div>";
            }
        }
        ?>

        <!-- Conversion History -->
        <h4 class="mt-4">Conversion History</h4>
        <canvas id="conversionChart" class="mb-3"></canvas>
        <a href="history.php" class="btn btn-info">View Full History</a>
        <a href="export.php" class="btn btn-success">Export as CSV</a>
    </div>
</body>
</html>