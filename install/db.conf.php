<?php
// ADATBÁZIS CSATLAKOZÁS
$mysql_host = "localhost";
$mysql_database = "db";
$mysql_root = "user";
$mysql_ = "password";

$connection = mysqli_connect($mysql_host, $mysql_root, $mysql_, $mysql_database);
mysqli_set_charset($connection,"utf8");
if(!$connection) {
    die('Unable to connect to database'.mysqli_connect_error());
}