<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("You need to login first.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO complaints (user_id, category, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $category, $description);

    if ($stmt->execute()) {
        echo "Complaint submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
