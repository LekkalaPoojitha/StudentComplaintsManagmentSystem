<?php
session_start();
include 'config.php';

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login'])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email = ?");
    if (!$stmt) {
        die("❌ Database Error: " . $conn->error);
    }   
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($admin_id, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION["admin_id"] = $admin_id;
        } else {
            $error = "❌ Incorrect Password!";
        }
    } else {
        $error = "❌ Admin not found!";
    }

    $stmt->close();
}

// Update Complaint Status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status']) && isset($_SESSION['admin_id'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Student Complaints</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('https://www.necn.ac.in/sports/SPORTS-IMAGES/ballbadminton.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            position: relative;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 40px auto 80px auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.2);
        }

        h2, h3 {
            color: #333;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        input[type="email"],
        input[type="password"],
        select {
            width: 80%;
            max-width: 300px;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            background: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            padding: 12px 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .logout {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #ffffffcc;
            border: 1px solid #ccc;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0px 3px 8px rgba(0,0,0,0.15);
        }

        .logout a {
            text-decoration: none;
            color: #dc3545;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            input, select, button {
                width: 90%;
            }

            table {
                font-size: 14px;
            }

            .logout {
                bottom: 10px;
                right: 10px;
                padding: 10px 14px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <?php if (!isset($_SESSION['admin_id'])): ?>
        <h2>Admin Login</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="admin_login">Login</button>
        </form>
    <?php else: ?>
        <h2>Admin Dashboard</h2>
        <h3>Complaint Management</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            $result = $conn->query("SELECT * FROM complaints ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <form method="POST" style="display:flex; flex-direction: column; align-items:center;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <select name="status">
                                <option <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option <?= $row['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            </select>
                            <button type="submit" name="update_status">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<?php if (isset($_SESSION['admin_id'])): ?>
    <div class="logout">
        <a href="?logout=1">🚪 Logout</a>
    </div>
<?php endif; ?>

</body>
</html>
<?php
$conn->close();
?>
