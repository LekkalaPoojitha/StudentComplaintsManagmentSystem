<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("You need to login first.");
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM complaints WHERE user_id = $user_id");

echo "<h2>Your Complaints</h2><table border='1'>";
echo "<tr><th>Category</th><th>Description</th><th>Status</th><th>Date</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['category']}</td><td>{$row['description']}</td><td>{$row['status']}</td><td>{$row['created_at']}</td></tr>";
}
echo "</table>";
?>
