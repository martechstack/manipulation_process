<?php

$uids = [
    '2umsdaf7q0x94',
    '025h6b6igs9oe',
    '5bmdp2b3mpiy4',
    'lz692qsh6nd0d',
    'rb2493yhce638',
    'cd927ajl4m27b',
    'wz432abwtobc5',
    'qw289jg1tmadf',
    'nl4361kmsx07c',
    'lg168rbg559d0',
    'oa1484my73aec',
    'gs076zjhar44f',
    'xw9427h4ewbda',
    'dd958m3556ba6',
    'ym409gy4h57df',
    'dz898qpvpzdaf',
    'mh822od35m24b',
    'kc553g277d103',
    'fy8stm0l4um8x',
    'x2v50gjiwqgiq',
    'px4kj411ffead',
    'urudjpm1xts0q',
    '62shy0qhua1w7',
    'u7zwoulqe5f8a',
    '1jtzlmkqbncbk',
    'jv7jgtndethxq',
    '6o1l356ds4pii',
    '20hrxkp8vrrqp',
    'p7w32720qgd6o',
    '165d9imh519hn',
    'inqcu0ri8f1mb',
    'wo398cqo7a520',
    'ra7793glyzf6d',
    'lb8896tom09ce',
    't7pojd9wv2hxf',
    'cz1061efs23d5',
    'oy791bom4rc16',
    'lq866hh94f1b6',
    'rw543kn5lta22',
    'fk1936hszxdeb',
    'pw479jjxrw9c6',
    'ha922se04d871',
    'jk247m929k375',
    'ng215b9gv99dd',
    'al208sgo5741f',
    'nt89721plm045',
    'yf676wfe071a7',
    'pw035hjrrc882',
    'bm898121d36bf',
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