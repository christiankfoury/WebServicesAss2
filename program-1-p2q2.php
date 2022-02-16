<?php
echo "sending a message to a target program, program-2, through a socket.\n";
/* Target program IP address */
$ipaddress = '127.0.0.1';
/* Target program port number. Now it is the apache server. */
$port = 10000;

/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
echo "Socket created.\n";

//Connecting to the socket
echo "Attempting to connect to '$ipaddress' on port '$port'...";
$result = socket_connect($socket, $ipaddress, $port);
echo "Connected to socket.\n";

//Sending data over the socket connection

// JSON object
$json = '{"name":"John", "age":30}';
$key = 'secret';
// Hashing the JSON object
$hashedJson = hash_hmac('sha256', $json, $key);

// Wrapping the JSON object and the key in an object
$array = array("object" => array(
    'json' => $json,
    'key' => $key,
    'hashedJson' => $hashedJson
));
// Encoding in JSON to be able to encrypt
$array = json_encode($array);

// IV Key
$ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
$iv = openssl_random_pseudo_bytes($ivlen);

// Encrypting the JSON object
$encryptedData = openssl_encrypt($array, "AES-128-CBC", "passphrase", $options=OPENSSL_RAW_DATA, $iv);
echo "Encrypted data: $encryptedData\n";
// Encoding in base64 because of ? characters and wrapping in another object
$array = array(
    "object" => array(
        "encryptedData" => base64_encode($encryptedData),
        "iv" => base64_encode($iv)
    )
);
// Encoding in JSON to be able to sent
$array = json_encode($array);

echo "Sending the data to program-2 ...";
socket_write($socket, $array);
echo "Data sent.\n";

// Reading response from the socket when program-2 sends back a message
/* Reading response from the socket */
echo "Reading response:\n\n";
$feedback = socket_read($socket, 2048);
echo $feedback;

// Closing the socket
echo "Closing socket...";
socket_close($socket);
echo "Socket closed.\n\n";
