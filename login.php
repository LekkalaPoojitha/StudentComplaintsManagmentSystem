<?php
session_start();

// Connect to Database
include 'config.php'; // Assuming config.php contains database connection details
// InfinityFree uses 3306 by default
 // Ensure this matches your MySQL port


// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Use Prepared Statements to prevent SQL Injection
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify Password
        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $user_id;

            // Redirect to complaints page
            echo "<script>alert('Login Successful!'); window.location.href='complaints.php';</script>";
        } else {
            echo "<script>alert('Invalid Password!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('Email not found!'); window.location.href='login.html';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
