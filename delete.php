<?php

$listId = getListIdByUid('fy8stm0l4um8x');

function getListIdByUid($list_uid) {
    $sql = "SELECT list_id FROM mailwizz.mw_list WHERE list_uid='$list_uid'";
    $result = runQuery($sql);
    if(!empty($result)){
        $resObj = $result->fetch_object();
        if(!empty($resObj)) {
            return $resObj->list_id;
        }
    }

    throw new Exception('Cannot get list id...');
}
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