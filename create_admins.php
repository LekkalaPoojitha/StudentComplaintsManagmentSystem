<?php
include 'config.php';

$admins = [
    ['necnprincipal@gmail.com', 'necnprincipal@1'],
    ['necndirector@gmail.com', 'necndirector@1']
];

foreach ($admins as $admin) {
    $email = $admin[0];
    $plain_password = $admin[1];

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $hashed_password);

        if ($stmt->execute()) {
            echo "✅ Admin <strong>$email</strong> added successfully.<br>";
        } else {
            echo "❌ Error adding <strong>$email</strong>: " . $stmt->error . "<br>";
        }

        $stmt->close();
    } else {
        echo "ℹ️ Admin <strong>$email</strong> already exists. Skipping...<br>";
    }

    $check->close();
}

$conn->close();
?>
