<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ras';

$mysqli = new mysqli($host, $user, $password, $database);

if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");
?>
