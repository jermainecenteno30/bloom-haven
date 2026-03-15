<?php
$host = "sql.freedb.tech";
$port = 3306;
$fp = @fsockopen($host, $port, $errno, $errstr, 5);
if ($fp) {
    echo "Port open!";
    fclose($fp);
} else {
    echo "Port blocked or host unreachable.";
}
?>