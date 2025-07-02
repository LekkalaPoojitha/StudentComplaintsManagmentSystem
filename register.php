<?php
// Connect to Database
include 'config.php';


// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Secure Hashing

    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Email already registered
        echo "<script>alert('A student has already registered with this email. Try another!'); window.location.href='register.html';</script>";
    } else {
        // Insert User into Database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registration Successful! Please Login.'); window.location.href='login.html';</script>";
        } else {
            echo "<script>alert('Error occurred during registration!'); window.location.href='register.html';</script>";
        }
        
        $stmt->close();
    }

    $checkStmt->close(); // Close statement
}

$conn->close(); // Close connection
?>
