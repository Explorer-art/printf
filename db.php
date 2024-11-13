<?php
$servername = "localhost";
$username = "root";
$password = "27052008den";
$dbname = "registerUsers";

$connect = mysqli_connect($servername, $username, $password, $dbname);
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
/*
else{
    echo "Успех";
}
*/