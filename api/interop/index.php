<?php
require_once __DIR__ . '/../../config.php'; 
header('Content-Type: application/json');
// shared secret with 3rd party provider
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
    echo json_encode(['verified' => false, 'message' => 'Missing or incorrect properties']);
    exit;
}

$stringToHash = $data['receipt'] . $data['cc4suffix'] . $data['purchase'] . $secret;
$hash = hash('sha256', $stringToHash);

if ($hash == $data['sig']) {
    $file = __DIR__ . '/validPurchases.jsonl';
    $thisToken = generate_uuidV4();
    $givenMessage = 'Valid Purchase Registered.';
    // we're good to give the happy signal back - HOWEVER if this is a repeat call
    //  - we want to let them know and log it...
    $alreadyProcessed = findByKey($data['receipt'], $file, "data.receipt");    
    if ( $alreadyProcessed ) {
        $givenMessage = "Valid Purchase Registered prior at [" . ($alreadyProcessed['registered_at'] ?? "nada") . "]";
        $thisToken = $alreadyProcessed['token'] ?? "token Missing";
        // can't put a duplicate validation in valid Purchases file - but we want to log anything like this..
        $file = __DIR__ . '/duplicateValidations.log';         
    }
    $rezult = [
        'verified' => true,
        'hash' => $hash, 
        'message' => $givenMessage,
        'token' => $thisToken,
        'redirect_url' => BASE_URL . "/?token=$thisToken",
        'registered_at' => $timestamp,
        'data' => $data
    ];
    file_put_contents($file, json_encode($rezult) . PHP_EOL, FILE_APPEND);
   
} else {
    $rezult =     [
        'verified' => true,
        'hash' => $hash, 
        'message' => 'INVALID information.'
    ];
    file_put_contents($file, json_encode($rezult), FILE_APPEND | LOCK_EX);
}

echo json_encode($rezult);
die();

function generate_uuidV4() {
    $data = random_bytes(16);

    // Set version to 0100 (4)
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function findByKey($givenSearchValue, $givenFileAndPath, $givenKeys) {
    $handle = fopen($givenFileAndPath, "r");
    if (!$handle) {
        return null; //a quiet fail because on day one this file won't exist..
        // note: it's possible to check if the file exists and throw an error - since that would not be good
        //throw new Exception("Unable to open file: " . $givenFileAndPath);
    }
    $keyPath = explode('.', $givenKeys);
    try {
        while (($line = fgets($handle)) !== false) {
            $item = json_decode($line, true);            
            // turn this on for debugging if the item is visible but not picked up by this code...
            //if ($item === null && json_last_error() !== JSON_ERROR_NONE) {
            //    // This line is corrupted! 
            //    // You could error_log("Bad JSON on line: " . $line);
            //}
            if (is_array($item)) {
                $keyFound = true;
                $currentItem = $item;
                foreach ($keyPath as $currentKey) {            
                    if (is_array($currentItem) && array_key_exists($currentKey, $currentItem)) {
                        $currentItem = $currentItem[$currentKey]; 
                    } else {
                        $keyFound = false; 
                        break; 
                    }
                }
                if ($keyFound && $currentItem === $givenSearchValue) {                    
                    return $item;
                }
            }
        }
    } finally {
        if (is_resource($handle)) {
            fclose($handle);
        }
    }
    return null;
}
