<?php

$server = "localhost";
$username = "id18040037_user";
$password = "GO[KGzLuCQ9]pCos";
$database = "id18040037_ooplogin";

$mySQL = new mysqli($server, $username, $password, $database);

// var_dump($mySQL);

if (!$mySQL) {
    die("Could not connect to the MySQL server: " . mysqli_connect_error());
}
