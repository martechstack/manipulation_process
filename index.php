<?php

try {
    $dbh = new PDO('localhost:mailwizz', 'root', 'Cvk9bpk1vV',
        array(PDO::ATTR_PERSISTENT => true));
    echo "Подключились\n";
} catch (Exception $e) {
    die("Не удалось подключиться: " . $e->getMessage());
}
die;
try {
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbh->beginTransaction();
    $dbh->exec("insert into staff (id, first, last) values (23, 'Joe', 'Bloggs')");
    $dbh->exec("insert into salarychange (id, amount, changedate)
      values (23, 50000, NOW())");
    $dbh->commit();

} catch (Exception $e) {
    $dbh->rollBack();
    echo "Ошибка: " . $e->getMessage();
}

die;





$listUid = createList();
if ($listId = getListIdByUid($listUid)) {
    $createdSubscribers = createSubscribers($listId, getDataAll());
    createListFields($listId);
}

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
function createList() {
    $list_uid = generateRandomString(13);
    $name = date('Fd_') . 'T' . date('Gi') . '_Many';
    $time = timeNow();

    $sql = "INSERT INTO mailwizz.mw_list
            (list_id, list_uid, customer_id, name, display_name, description, visibility, opt_in, opt_out, welcome_email, removable, subscriber_require_approval, subscriber_404_redirect, subscriber_exists_redirect, meta_data, status, date_added, last_updated) VALUES
            (NULL, '$list_uid', 1, '$name', '$name', '$name', 'public', 'single', 'single', 'no', 'yes', 'no', '', '', 0x613A323A7B733A33383A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F737562736372696265223B693A303B733A34303A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F756E737562736372696265223B693A303B7D, 'active', '$time', '$time');";

    if(!runQuery($sql)){
        throw new Exception('Cannot create List: ' . $sql);
    }

    return $list_uid;
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
function createSubscribers($listId, $data){
    $created = 0;
    foreach ($data as $datum) {
        if ( createSubscriber($listId,$datum)) {
            $created++;
        }
    }
    
    return $created;
}
function createSubscriber($listId, $datum) {
    $subscriber_uid = generateRandomString();
    $email = $datum->Email;
    $time = timeNow();

    $sql = "INSERT INTO mailwizz.mw_list_subscriber
    (subscriber_id, subscriber_uid, list_id, email, ip_address, source, status, date_added, last_updated) VALUES
    (NULL, '$subscriber_uid', $listId, '$email', '', 'import', 'confirmed', '$time', '$time');";

    if(!runQuery($sql)){
        throw new Exception('Cannot create subscriber, sql: ' . $sql);
    }

    return $subscriber_uid;
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
function getDataAll() {
    $sql = "SELECT * FROM mailwizz.data_all LIMIT 2";
    $result = runQuery($sql);
    while ($obj = $result->fetch_object()) {
        $ar[] = $obj;
    }

    return $ar;
}
function createListFields($listId){
    $time = timeNow();
    $sql = "
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES (NULL, 2, $listId, 'Email', 'EMAIL', null, null, null, 'yes', 'visible', 0x613A303A7B7D, 0, '$time', '$time');
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES (NULL, 2, $listId, 'From', 'FROM', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time');
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES (NULL, 2, $listId, 'Code', 'CODE', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time');
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES (NULL, 2, $listId, 'Carrier', 'CARRIER', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time');
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES (NULL, 2, $listId, 'Bucket', 'BUCKET', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time');
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES (NULL, 2, $listId, 'First name', 'FNAME', null, null, null, 'no', 'visible', 0x613A303A7B7D, 1, '$time', '$time');
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES (NULL, 2, $listId, 'Last name', 'LNAME', null, null, null, 'no', 'visible', 0x613A303A7B7D, 2, '$time', '$time');
    ";
}
?>