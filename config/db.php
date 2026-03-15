<?php
$host = getenv('DB_HOST');         // FreeDB server
$user = getenv('DB_USER');         // FreeDB username
$password = getenv('DB_PASSWORD'); // FreeDB password
$database = getenv('DB_NAME');     // FreeDB database
$port = getenv('DB_PORT');         // FreeDB port

$conn = new mysqli($host, $user, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>