<?php

echo '<pre>'; print_r([    $_GET    ]); echo die;

$sql = "";

function getConnect() {
    $config = require_once 'config.php';
    
    $conn = mysqli_connect($config['servername'], $config['username'], $config['password'], $config['database']);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn;
}

function runQuery($sql) {
    $conn = getConnect();
    if ($result = mysqli_query($conn, $sql)) {
    } else {
        echo "\n<br/>Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    return $result;
}