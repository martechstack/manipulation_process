<?php

$strings = [
    'test',
    'A28',
    'A29',
    'VZ_100',
];

foreach ($strings as $uid) {
    if ($listIds = getListIdsByStrConsistName($uid)) {
        foreach ($listIds as $listId) {
            deleteListCompany($listId);
            deleteListDefault($listId);
            deleteListNotification($listId);
            deleteListField($listId);
            deleteList($listId);

            echo PHP_EOL . " Deleted: $uid " . PHP_EOL;
        }
    }

    echo PHP_EOL . ' Done..' . PHP_EOL;
}

function deleteListCompany($listId){
    $sql = "DELETE FROM mailwizz.mw_list_company WHERE list_id=$listId";
    if(!runQuery($sql)){
        throw new Exception('Cannot delete mw_list_company, sql: ' . $sql);
    }
}
function deleteListDefault($listId){
    $sql = "DELETE FROM mailwizz.mw_list_default WHERE list_id=$listId";
    if(!runQuery($sql)){
        throw new Exception('Cannot delete mw_list_default, sql: ' . $sql);
    }
}
function deleteListNotification($listId){
    $sql = "DELETE FROM mailwizz.mw_list_customer_notification WHERE list_id=$listId";
    if(!runQuery($sql)){
        throw new Exception('Cannot delete mw_list_customer_notification, sql: ' . $sql);
    }
}
function deleteListField($listId){
    $sql = "DELETE FROM mailwizz.mw_list_field WHERE list_id=$listId";
    if(!runQuery($sql)){
        throw new Exception('Cannot delete mw_list_field, sql: ' . $sql);
    }
}
function deleteList($listId){
    $sql = "DELETE FROM mailwizz.mw_list WHERE list_id=$listId";
    if(!runQuery($sql)){
        throw new Exception('Cannot delete mw_list, sql: ' . $sql);
    }
}
function getListIdsByStrConsistName($str): array {
    $ar = [];
    $sql = "SELECT list_id FROM mailwizz.mw_list WHERE mw_list.name LIKE '%$str%'";
    $result = runQuery($sql);
    while ($obj = $result->fetch_object()) {
        $ar[] = $obj->list_id;
    }
    //echo PHP_EOL . "Cannot get list, str: $str" . PHP_EOL;

    return $ar;
}
function getConnect() {
    $config = require 'config.php';

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