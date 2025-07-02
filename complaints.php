<?php
session_start();

// Connect to Database
include 'config.php'; // Assuming config.php contains database connection details
// InfinityFree uses 3306 by default
 // Ensure this matches your MySQL port

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Complaint Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_complaint"])) {
    if (!isset($_SESSION["user_id"])) {
        echo "<script>alert('You must be logged in to submit a complaint!'); window.location.href='login.html';</script>";
        exit;
    }

    $user_id = $_SESSION["user_id"];
    $category = $_POST["category"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("INSERT INTO complaints (user_id, category, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $category, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Complaint Submitted Successfully!'); window.location.href='complaints.php';</script>";
    } else {
        echo "<script>alert('Error submitting complaint!');</script>";
    }

    $stmt->close();
}

// Fetch Complaints
$result = $conn->query("SELECT c.id, c.category, c.description, c.status, c.created_at, u.name 
                        FROM complaints c 
                        JOIN users u ON c.user_id = u.id 
                        ORDER BY c.created_at DESC");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints</title>
    <style>
        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: url('clg.jpg') no-repeat center/cover;
            text-align: center;
            height: 100vh;
        }

        /* Form Styling */
        .form-container {
            background: white;
            padding: 20px;
            width: 50%;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin-bottom: 10px;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            text-align: left;
        }

        select, textarea, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: none;
        }

        button {
            background: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border: none;
            transition: 0.3s ease-in-out;
        }

        button:hover {
            background: #218838;
        }

        /* Table Styling */
        .table-container {
            width: 90%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>
<body>

    <!-- Complaint Submission Form -->
    <div class="form-container">
        <h2>Submit a Complaint</h2>
        <form action="complaints.php" method="POST">
            <label for="category">Category:</label>
            <select name="category" required>
                <option value="Academics">Academics</option>
                <option value="Facilities">Facilities</option>
                <option value="Administration">Administration</option>
                <option value="Other">Other</option>
            </select>

            <label for="description">Description:</label>
            <textarea name="description" rows="4" required></textarea>

            <button type="submit" name="submit_complaint">Submit</button>
        </form>
    </div>

    <!-- Complaints List Table -->
    <div class="table-container">
        <h2>Complaints List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["category"]) ?></td>
                    <td><?= htmlspecialchars($row["description"]) ?></td>
                    <td><?= $row["status"] ?></td>
                    <td><?= $row["created_at"] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
