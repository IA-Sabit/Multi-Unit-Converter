<?php
require 'db.php';
header('Content-Type: application/json');
$stmt = $pdo->query("SELECT result, created_at FROM conversions ORDER BY created_at DESC LIMIT 10");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>