<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo "Status updated!";
    }
}

$result = $conn->query("SELECT * FROM complaints");

echo "<h2>All Complaints</h2><table border='1'>";
echo "<tr><th>ID</th><th>User ID</th><th>Category</th><th>Description</th><th>Status</th><th>Action</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['user_id']}</td>
        <td>{$row['category']}</td>
        <td>{$row['description']}</td>
        <td>{$row['status']}</td>
        <td>
            <form method='POST'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <select name='status'>
                    <option>Pending</option>
                    <option>Resolved</option>
                </select>
                <input type='submit' value='Update'>
            </form>
        </td>
    </tr>";
}
echo "</table>";
?>
