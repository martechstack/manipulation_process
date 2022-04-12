<?php

$uids = [
    '2umsdaf7q0x94',
];

foreach ($uids as $uid) {
    $listId = getListIdByUid($uid);
    deleteListCompany($listId);
    deleteListDefault($listId);
    deleteListNotification($listId);
    deleteListField($listId);
    deleteList($listId);

    echo " Deleted: $uid ";
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