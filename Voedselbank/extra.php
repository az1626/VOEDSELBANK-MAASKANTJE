<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $beschikbare_allergieën = $_POST['beschikbare_allergieën'];
    $beschikbare_categorieën = $_POST['beschikbare_categorieën'];

    $sql = "INSERT INTO extra (beschikbare_allergieën, beschikbare_categorieën) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $beschikbare_allergieën, $beschikbare_categorieën);

    if ($stmt->execute()) {
        $success_message = "New extra information added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current records
$sql = "SELECT * FROM extra";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Extra Information</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            display: grid;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Extra Information</h1>
    <?php
    if (isset($success_message)) {
        echo "<div class='message success'>$success_message</div>";
    }
    if (isset($error_message)) {
        echo "<div class='message error'>$error_message</div>";
    }
    ?>

    <table>
        <tr>
            <th>Available Allergies</th>
            <th>Available Categories</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['beschikbare_allergieën']); ?></td>
                <td><?php echo htmlspecialchars($row['beschikbare_categorieën']); ?></td>
                <td>
                    <a href="edit_extra.php?id=<?php echo urlencode($row['id']); ?>">Edit</a>
                    <a href="delete_extra.php?id=<?php echo urlencode($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <form action="extra.php" method="post">
        <label for="beschikbare_allergieën">Available Allergies:</label>
        <input type="text" id="beschikbare_allergieën" name="beschikbare_allergieën" required>

        <label for="beschikbare_categorieën">Available Categories:</label>
        <input type="text" id="beschikbare_categorieën" name="beschikbare_categorieën" required>

        <input type="submit" value="Add Extra Info">
    </form>
</div>
</body>
</html>
