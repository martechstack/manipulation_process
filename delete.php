<?php

$config = require_once 'config.php';

getConnect($config);

function getConnect($config) {
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['database']);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}