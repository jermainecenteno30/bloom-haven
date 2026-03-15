<?php
include 'db.php';

$result = $conn->query("SELECT NOW() as current_time");
$row = $result->fetch_assoc();
echo "Connected! Current DB time: " . $row['current_time'];
?>