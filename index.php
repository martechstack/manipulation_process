<?php

/*
 * APRIL:
100 TM  - APR12_APRIL_TM_100 tmobile     used
100 VZ - APR12_APRIL_VZ_100 verizon      used
100 ATT - APR12_APRIL_ATT_100            not need

1000 TM  - APR12_APRIL_TM_1000           used
1000 VZ - APR12_APRIL_VZ_1000
1000 ATT - APR12_APRIL_ATT_1000

AURORA:

100 TM  - APR12_AURORA_TM_100           used
100 VZ - APR12_AURORA_VZ_100
100 ATT - APR12_AURORA_ATT_100

1000 TM  - APR12_AURORA_TM_1000
1000 VZ - APR12_AURORA_VZ_1000
1000 ATT - APR12_AURORA_ATT_1000

BLACKBIRD:

100 TM  - APR12_BLACKBIRD_TM_100
100 VZ - APR12_BLACKBIRD_VZ_100
100 ATT - APR12_BLACKBIRD_ATT_100

1000 TM  - APR12_BLACKBIRD_TM_1000
1000 VZ - APR12_BLACKBIRD_VZ_1000
1000 ATT - APR12_BLACKBIRD_ATT_1000

ECLIPSE:

100 TM  - APR12_ECLIPSE_TM_100
100 VZ - APR12_ECLIPSE_VZ_100
100 ATT - APR12_ECLIPSE_ATT_100

1000 TM  - APR12_ECLIPSE_TM_1000
1000 VZ - APR12_ECLIPSE_VZ_1000
1000 ATT - APR12_ECLIPSE_ATT_1000
 */

$listName = 'APR12_AURORA_VZ_100';
$carrier = 'verizon';
//$carrier = 'tmobile';
$limit = '100';

$listUid = createList($listName);
if ($listId = getListIdByUid($listUid)) {
    createListCompany($listId);
    createListDefault($listId);
    createListNotification($listId);
    createListFields($listId);
    $fields = getFieldsByListId($listId);
    if (empty($fields)) {
        throw new Exception("Cannot find any fields with listId = $listId...");
    }

    $dataAll = getDataAll($carrier, $limit);
    if (empty($dataAll)) {
        throw new Exception('No data in table data_all...');
    }

    $createdSubscribersCount = createSubscribers($listId, $dataAll);
    $subscribers = getSubscribersByListId($listId);
    if (empty($subscribers)) {
        throw new Exception("Cannot find any subscriber with listId = $listId...");
    }

    createListFieldValue($dataAll, $subscribers, $fields);

    echo PHP_EOL . 'DONE!' . PHP_EOL;
}

function timeNow(){ return date('Y-m-d G:i:s'); }
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
function createList($name) {
    $list_uid = generateRandomString(13);
//    $name = date('Fd_') . 'T' . date('Gi') . '_Many';
    $time = timeNow();

    $sql = "INSERT INTO mailwizz.mw_list
            (list_id, list_uid, customer_id, name, display_name, description, visibility, opt_in, opt_out, welcome_email, removable, subscriber_require_approval, subscriber_404_redirect, subscriber_exists_redirect, meta_data, status, date_added, last_updated) VALUES
            (NULL, '$list_uid', 1, '$name', '$name', '$name', 'public', 'single', 'single', 'no', 'yes', 'no', '', '', 0x613A323A7B733A33383A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F737562736372696265223B693A303B733A34303A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F756E737562736372696265223B693A303B7D, 'active', '$time', '$time');";
      
    if(!runQuery($sql)){
        throw new Exception('Cannot create List: ' . $sql);
    }

    return $list_uid;
}
function createListCompany($listId) {
    $list_uid = generateRandomString(13);
    $name = date('Fd_') . 'T' . date('Gi') . '_Many';

    $sql = "INSERT INTO mailwizz.mw_list_company 
        (list_id, type_id, country_id, zone_id, name, website, address_1, address_2, zone_name, city, zip_code, phone, address_format) VALUES 
        ($listId, null, 3, 72, '$name', '', '23 dfsdf', '', '', 'sdfsdf', '3434', '', '[COMPANY_NAME]
        [COMPANY_ADDRESS_1] [COMPANY_ADDRESS_2]
        [COMPANY_CITY] [COMPANY_ZONE] [COMPANY_ZIP]
        [COMPANY_COUNTRY]
        [COMPANY_WEBSITE]');
    ";

    if(!runQuery($sql)){
        throw new Exception('Cannot create List Company: ' . $sql);
    }

    return $list_uid;
}
function createListDefault($listId) {
    $sql = "INSERT INTO mailwizz.mw_list_default (list_id, from_name, from_email, reply_to, subject) VALUES ($listId, 'Admin User', 'info@digitaldataandsolutions.com', 'info@digitaldataandsolutions.com', '');";

    if(!runQuery($sql)){
        throw new Exception('Cannot create List Default: ' . $sql);
    }
}
function createListNotification($listId) {
    $sql = "INSERT INTO mailwizz.mw_list_customer_notification (list_id, daily, subscribe, unsubscribe, daily_to, subscribe_to, unsubscribe_to) VALUES ($listId, 'no', 'no', 'no', null, '', '');";

    if(!runQuery($sql)){
        throw new Exception('Cannot create List Notification: ' . $sql);
    }
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
        if ( createSubscriber($listId, $datum)) {
            $created++;
            markUsed($datum);
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
function markUsed($datum) {
    $sql = "UPDATE mailwizz.data_all SET Used=1 WHERE id=$datum->id";

    if(!runQuery($sql)){
        throw new Exception('Cannot set Used = 1, sql: ' . $sql);
    }

    return true;
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
function getDataAll($carrier, $limit) {
    $sql = "SELECT *
            FROM mailwizz.data_all
            WHERE Carrier IN ('$carrier')
            AND Used IS NULL
            LIMIT $limit
            "; // LIMIT 1 OFFSET 0 AND Used != 1
    $result = runQuery($sql);
    while ($obj = $result->fetch_object()) {
        $ar[] = $obj;
    }

    return $ar;
}
function createListFields($listId){
    $time = timeNow();
    $sql = "
        INSERT INTO mailwizz.mw_list_field (field_id, type_id, list_id, label, tag, default_value, help_text, description, required, visibility, meta_data, sort_order, date_added, last_updated) VALUES 
        (NULL, 2, $listId, 'Email', 'EMAIL', null, null, null, 'yes', 'visible', 0x613A303A7B7D, 0, '$time', '$time'),
        (NULL, 2, $listId, 'From', 'FROM', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time'),
        (NULL, 2, $listId, 'Code', 'CODE', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time'),
        (NULL, 2, $listId, 'Carrier', 'CARRIER', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time'),
        (NULL, 2, $listId, 'Bucket', 'BUCKET', null, null, null, 'no', 'visible', 0x613A303A7B7D, 0, '$time', '$time'),
        (NULL, 2, $listId, 'First name', 'FNAME', null, null, null, 'no', 'visible', 0x613A303A7B7D, 1, '$time', '$time'),
        (NULL, 2, $listId, 'Last name', 'LNAME', null, null, null, 'no', 'visible', 0x613A303A7B7D, 2, '$time', '$time');
    ";

    if(!runQuery($sql)){
        throw new Exception('Cannot create list field, sql: ' . $sql);
    }
    
    return true;
}
function createListFieldValue($dataAll, $subscribers, $fields){
    $time = timeNow();
    $fildIdFirstName = getFieldId($fields, 'First name');
    $fildIdLastName = getFieldId($fields, 'Last name');
    $fildIdEmail = getFieldId($fields, 'Email');
    $fildIdFrom = getFieldId($fields, 'From');
    $fildIdCarrier = getFieldId($fields, 'Carrier');
    $fildIdCode = getFieldId($fields, 'Code');
    $fildIdBucket = getFieldId($fields, 'Bucket');

    foreach($subscribers as $subscriber) {
       $datum = findSubscriberInData($dataAll, $subscriber);
       $bucket = getRandomBucket();

       $firstName = addslashes($datum->FirstName);
       $lastName = addslashes($datum->LastName);
        $sql = "
                INSERT INTO mailwizz.mw_list_field_value (value_id, field_id, subscriber_id, value, date_added, last_updated) VALUES 
                (NULL, $fildIdFirstName, $subscriber->subscriber_id, '$firstName', '$time', '$time'),
                (NULL, $fildIdLastName, $subscriber->subscriber_id, '$lastName', '$time', '$time'),
                (NULL, $fildIdFrom, $subscriber->subscriber_id, '$datum->From', '$time', '$time'),
                (NULL, $fildIdEmail, $subscriber->subscriber_id, '$datum->Email', '$time', '$time'),
                (NULL, $fildIdCode, $subscriber->subscriber_id, '$datum->Code', '$time', '$time'),
                (NULL, $fildIdCarrier, $subscriber->subscriber_id, '$datum->Carrier', '$time', '$time'),
                (NULL, $fildIdBucket, $subscriber->subscriber_id, '$bucket', '$time', '$time');
            ";

        if(!runQuery($sql)){
            throw new Exception('Cannot create field values, sql: ' . $sql);
        }
    }

    return true;
}
function getRandomBucket() {
    $buckets = require 'buckets.php';
    if(empty($buckets[array_rand($buckets)])) {
        throw new Exception('Cannot get bucket');
    }

    $bucket = $buckets[array_rand($buckets)];

    return $bucket;
}
function getFieldId($fields, $label){
    foreach ($fields as $field) {
        if ($field->label == $label) {
            return $field->field_id;
        }
    }

    throw new Exception('Cannot find field label: ' . $label);
}
function findSubscriberInData($dataAll, $subscriber){
    foreach ($dataAll as $datum) {
        if ($datum->Email == $subscriber->email) {
            return $datum;
        }
    }

    throw new Exception('Cannot find subscriber in datum, subscriber Email: ' . $subscriber->email);
}
function getSubscribersByListId($listId) {
    $sql = "SELECT * FROM mailwizz.mw_list_subscriber WHERE list_id='$listId'";
    $result = runQuery($sql);
    while ($obj = $result->fetch_object()) {
        $ar[] = $obj;
    }

    return $ar;
}
function getFieldsByListId($listId) {
    $sql = "SELECT * FROM mailwizz.mw_list_field WHERE list_id='$listId'";
    $result = runQuery($sql);
    while ($obj = $result->fetch_object()) {
        $ar[] = $obj;
    }

    return $ar;
}
?>