<?php

$ipaddress = '127.0.0.1';
$port = 10000;
$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
/* Bind the program to a socket, i.e., make the program listen to the specified port number */
socket_bind($sock, $ipaddress, $port);

// Start listening to the socke t:
socket_listen($sock, 5);

// Wait for and then read the message when it comes:
/* Wait for and then read the message when it comes */
echo "waiting for a message...\n";
$msgsock = socket_accept($sock);
$buf = socket_read($msgsock, 2048);
echo "Received message: $buf\n";


// Using the JSON Decode method to decode the JSON object
$buf = json_decode($buf);
print_r($buf);
// Accessing the encrypted JSON object
$encryptedData = $buf->object->encryptedData;
// Accessing the IV key
$iv = $buf->object->iv;
// Decrypting the encrypted data using the key
$array = openssl_decrypt(base64_decode($encryptedData), "AES-128-CBC", "passphrase", $options=OPENSSL_RAW_DATA, base64_decode($iv));


// Decoding the decrypted data that is JSON
$jsonArray = json_decode($array);
// Accessing the JSON object
$json = $jsonArray->object->json;
$key = $jsonArray->object->key;
$hashedJson = $jsonArray->object->hashedJson;

// Verifying if the message is authentic
$response;
if (hash_hmac('sha256', $json, $key) == $hashedJson) {
    $response = "The message is authentic.\n";
} else {
    $response = "The message is not authentic.\n";
}


/* Write and send a feedback message */
// $feedback = "Welcome to Program-2, You said '$buf'.\n";
socket_write($msgsock, $response, strlen($response));

// Close the socket:
socket_close($msgsock);
socket_close($sock);
