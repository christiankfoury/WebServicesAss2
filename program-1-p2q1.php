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
/* The data, or message, to be sent to program-2 */
// $request = "GET /page.html HTTP/1.1\r\n";
// $request .= "Host: localhost\r\n";
// $request .= "Connection: Close\r\n\r\n";

// JSON string
$json = '{"name":"John", "age":30}';
// Key // * THE KEY SHOULD NOT BE SENT *
$key = 'secret';
// Hashing the JSON
$hashedJson = hash_hmac('sha256', $json, $key);
// Wrapping all three of the data in an array so it can be send once
$jsonArrayWrapped = array(
    'json' => $json,
    'key' => $key,
    'hashedJson' => $hashedJson
);

// Naming the array object
$array = array("object" => $jsonArrayWrapped);
// Encoding in JSON
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
