<?php

$dbserver = "127.0.0.1";
$dbuser = "phish";
$dbpassword = "password1!";
$dbname = "pond";

$conn = mysqli_connect($dbserver,$dbuser,$dbpassword,$dbname);

if(! $conn){
        die("Failed to connect to database.". mysqli_connect_error());
}

//echo "connected Successfully<br>";

?>
