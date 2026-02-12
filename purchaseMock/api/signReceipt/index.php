<?php
header('Content-Type: application/json');
// shared secret with client
$secret = "5a7cc61d-fac5-46fc-8ebb-322fbd1a8955"; // typically stored in something not checked in by git..
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// log it
$timestamp = date('Y-m-d H:i:s');
$content = "[{$timestamp}] " . json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
$content .= "------------------------------------------" . PHP_EOL;
$file = __DIR__ . '/payloads.log';
file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

if (!isset($data['receipt'], $data['cc4suffix'], $data['purchase'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required properties']);
    exit;
}

$stringToHash = $data['receipt'] . $data['cc4suffix'] . $data['purchase'] . $secret;
$hash = hash('sha256', $stringToHash);

echo json_encode([
    'hashedsig' => $hash
]);
