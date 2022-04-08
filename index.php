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
$ar = [];
if ($result = mysqli_query($conn, $sql)) {
        while ($obj = $result -> fetch_object()) {
            $ar[] = $obj;
        }

        echo '<pre>'; print_r([    $ar    ]); echo die;
        
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
}
mysqli_close($conn);
?>