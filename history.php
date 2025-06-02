<?php
require 'db.php';
$stmt = $pdo->query("SELECT * FROM conversions ORDER BY created_at DESC");
$conversions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Conversion History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Conversion History</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Value</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Result</th>
                    <th>Type</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conversions as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['input_value'] ?></td>
                        <td><?= ucfirst($row['from_unit']) ?></td>
                        <td><?= ucfirst($row['to_unit']) ?></td>
                        <td><?= $row['result'] ?></td>
                        <td><?= ucfirst($row['unit_type']) ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-primary">Back to Converter</a>
    </div>
</body>
</html>