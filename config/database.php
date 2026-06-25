<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'prodx_erp');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDB() {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        die('Database connection failed: ' . $mysqli->connect_error);
    }
    return $mysqli;
}
?>