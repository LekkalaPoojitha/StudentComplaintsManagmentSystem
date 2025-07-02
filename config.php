<?php
$servername = "sql100.infinityfree.com";
$username = "if0_39368050";
$password = "poojitha63";
$dbname = "if0_39368050_students";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $dbname,$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
