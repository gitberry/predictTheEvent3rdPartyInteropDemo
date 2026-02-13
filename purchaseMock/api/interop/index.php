<?php
header('Content-Type: application/json');
// CORS 
// Rate limiting 

// arranged with client - would not be checked into git on prod
$apiURL = "http://icebreakup.localhost/api/interop/";
$apiKey = "d9751288-6974-40bc-892d-8a65564627e0";
$secret = "5a7cc61d-fac5-46fc-8ebb-322fbd1a8955";

$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// log it (3rd party likely would have done this elsewhere)
$timestamp = date('Y-m-d H:i:s');
$content = "[{$timestamp}] " . json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
$file = __DIR__ . '/payloads.log';
file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

// validation (3rd party likely would have done this elsewhere)
if (!isset($data['receipt'], $data['cc4suffix'], $data['purchase'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required properties']);
    exit;
}

//$stringToHash = $data['receipt'] . $data['cc4suffix'] . $data['purchase'] . $secret;
//$hash = hash('sha256', $stringToHash);
$signature = hash_hmac('sha256', $jsonData, $secret);

// this would be a good place to have a server to server API call to transmit the data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiURL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'X-API-Key: ' . $apiKey,
    'X-Signature: ' . $signature
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $result = json_decode($response, true);   
    // belt and suspenders moment:
    // parties could arrange verify another hmac in their response header if both parties need to continuously
    // feel good about each other.. (ie other party API DNS got compromised and takes the data and responds with malicious redirection)
    // appropriate in high stakes situations..
    if (($result['verified'] ?? false) === true) { // On success send them the data with redirect URL - if it exists
        $redirectUrl = $result['redirect_url'] ?? '';
        if (filter_var($redirectUrl, FILTER_VALIDATE_URL)) { // AND the URL is proper
            echo json_encode([
                 'verified' => true
                ,'message' => "Thank you - forwarding you to next step to 'set the guess'... "
                ,'redirect_url' => $redirectUrl
            ]);
            exit;
        }                
    }
}

echo json_encode([
     'verified' => false
    ,'message' => "Error: Could not complete handoff." . $apiURL . "|" . $httpCode . "|" . $response . "|" . $apiKey
]);
