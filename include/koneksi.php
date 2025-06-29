<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "mansa";

$koneksi = new mysqli($host, $username, $password, $dbname);

if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}
?>
