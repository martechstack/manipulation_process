<?php

//createList();
createSubscribers(getDataAll());

function timeNow(){ return date('Y-m-d G:i:s'); }
function getConnect() {
    $servername = "localhost";
    $database = "mailwizz";
    $username = "root";
    $password = "Cvk9bpk1vV";

    $conn = mysqli_connect($servername, $username, $password, $database);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    echo "Connected successfully";
    
    return $conn;
}
function runQuery($sql) {
    $conn = getConnect();
    if ($result = mysqli_query($conn, $sql)) {
        echo 'success';
    } else {
        echo "\n<br/>Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    return $result;
}
function createList() {
    $list_uid = generateRandomString(13);
    $name = date('Fd_') . 'T' . date('Gi') . '_Many';
    $time = timeNow();

    $sql = "INSERT INTO mailwizz.mw_list
            (list_id, list_uid, customer_id, name, display_name, description, visibility, opt_in, opt_out, welcome_email, removable, subscriber_require_approval, subscriber_404_redirect, subscriber_exists_redirect, meta_data, status, date_added, last_updated) VALUES
            (NULL, '$list_uid', 1, '$name', '$name', '$name', 'public', 'single', 'single', 'no', 'yes', 'no', '', '', 0x613A323A7B733A33383A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F737562736372696265223B693A303B733A34303A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F756E737562736372696265223B693A303B7D, 'active', '$time', '$time');";

    echo '<pre>'; print_r([    runQuery($sql)    ]); echo die;
}
function generateRandomString($length = 13) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function createSubscribers($data){
    foreach ($data as $key => $datum) {
        createSubscriber($datum);
        //todo
        if($key <=3) {
            break;
        }
    }

    echo '';
    echo PHP_EOL . "Done successfully: $key subscribers";
}
function createSubscriber($datum) {
    $lastListId = getLastListId();
    $subscriber_uid = generateRandomString();
    $email = $datum->email;
    $time = timeNow();

    $sql = "INSERT INTO mailwizz.mw_list_subscriber
    (subscriber_id, subscriber_uid, list_id, email, ip_address, source, status, date_added, last_updated) VALUES
    (NULL, '$subscriber_uid', $lastListId, '$email', '', 'import', 'confirmed', '$time', '$time');";

    runQuery($sql);

    return $subscriber_uid;
}
function getLastListId() {
    $sql = "SELECT list_id FROM mailwizz.mw_list ORDER BY list_id DESC LIMIT 1";
    $result = runQuery($sql);
    if(!empty($result)){
        $resObj = $result->fetch_object();
        if(!empty($resObj)) {
            return $resObj->list_id;
        }
    }

    throw new Exception('Cannot get list id...');
}
function getDataAll() {
    $sql = "SELECT * FROM mailwizz.data_all";
    $result = runQuery($sql);
    while ($obj = $result->fetch_object()) {
        $ar[] = $obj;
    }

    return $ar;
}
?>