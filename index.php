<?php
$data = [
    [
        'id' => 299,
        'FirstName' => 'Rhonda',
        'LastName' => 'Moore',
        'From' => 'kelleytheroyal@gmail.com',
        'Email' => '4787657655@txt.att.net',
        'Code' => 'cq5mz66e4',
        'Carrier' => 'att',
        'Bucket' => '',
        'Date' => '0004-07-22 11:00:00',
        'Used' => 0,
    ],
    [
        'id' => 300,
        'FirstName' => 'Rhonda2',
        'LastName' => 'Moore',
        'From' => 'kelleytheroyal@gmail.com',
        'Email' => '4787657655@txt.att.net',
        'Code' => 'cq5mz66e4',
        'Carrier' => 'att',
        'Bucket' => '',
        'Date' => '0004-07-22 11:00:00',
        'Used' => 0,
    ],
];
createList($data);


$conn = getConnect();
$sql = "SELECT * from mailwizz.data_all";
$ar = [];
if ($result = mysqli_query($conn, $sql)) {
    while ($obj = $result->fetch_object()) {
        $ar[] = $obj;
    }

    echo '<pre>';
    print_r([$ar]);
    echo die;

} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);

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
        echo '<pre>'; print_r([   'success', $result    ]); echo die;
    } else {
        echo "\n<br/>Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

function createList($data) {
    $list_uid = generateRandomString(13);
    $name = date('Fd_') . 'T' . date('Gi') . '_Many';
    
    $sql = "INSERT INTO mailwizz.mw_list
            (list_id, list_uid, customer_id, name, display_name, description, visibility, opt_in, opt_out, welcome_email, removable, subscriber_require_approval, subscriber_404_redirect, subscriber_exists_redirect, meta_data, status) VALUES
            (NULL, '$list_uid', 1, '$name', '$name', '$name', 'public', 'single', 'single', 'no', 'yes', 'no', '', '', 0x613A323A7B733A33383A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F737562736372696265223B693A303B733A34303A2269735F73656C6563745F616C6C5F61745F616374696F6E5F7768656E5F756E737562736372696265223B693A303B7D, 'active');";

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
?>