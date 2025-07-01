<?php
$host = "localhost";
$user = "root";
$pass = "Poojitha@1";
$db = "demodb";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
