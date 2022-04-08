<?php

$servername = "localhost";
$database = "mailwizz";
$username = "root";
$password = "Cvk9bpk1vV";

$conn = mysqli_connect($servername, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connected successfully";

$sql = "SELECT * from mailwizz.mw_list";
if ($result = mysqli_query($conn, $sql)) {
echo '<pre>'; print_r([    $result -> fetch_object()    ]); echo die;

//        while ($obj = $result -> fetch_object()) {
//            printf("%s (%s)\n", $obj->Lastname, $obj->Age);
//        }

    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?>