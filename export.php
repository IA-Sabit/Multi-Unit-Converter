<?php
require 'db.php';
$stmt = $pdo->query("SELECT * FROM conversions");
$conversions = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="conversions.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID', 'Input Value', 'From Unit', 'To Unit', 'Result', 'Unit Type', 'Created At']);

foreach ($conversions as $row) {
    fputcsv($output, $row);
}
fclose($output);
exit;
?>