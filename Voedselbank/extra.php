<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit;
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naam = $_POST['naam'];

    // Prepare the SQL statement
    $sql = "INSERT INTO dieetwensen (naam) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $naam);

    // Execute the statement and handle success/error
    if ($stmt->execute()) {
        $success_message = "New dietary wish added successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current records
$sql = "SELECT * FROM dieetwensen";
$result = $conn->query($sql);

// Check if query was successful
if ($result === false) {
    $error_message = "Error fetching data: " . $conn->error;
    $result = [];  // Ensure $result is defined even if it's an empty array
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Dietary Wishes</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Reset default margin and padding for body and html */
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
        }
        /* Navbar styles */
        .navbar {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
        }
        .navbar a:hover {
            background-color: #575757;
        }
        /* Main container to push content below navbar */
        .main-content {
            padding-top: 60px; /* Adjust based on navbar height */
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
<?php include 'navbar.php'; ?>

<div class="main-content">
    <div class="container">
        <h1>Beheer dieetwensen</h1>
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
                <th>Naam</th>
                <th>Acties</th>
            </tr>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['naam']); ?></td>
                        <td>
                            <a href="edit_extra.php?id=<?php echo urlencode($row['idDieetwensen']); ?>">Edit</a>
                            <a href="delete_extra.php?id=<?php echo urlencode($row['idDieetwensen']); ?>" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Geen dieetwensen gevonden.</td>
                </tr>
            <?php endif; ?>
        </table>

        <form action="extra.php" method="post">
            <label for="naam">Naam:</label>
            <input type="text" id="naam" name="naam" required>

            <input type="submit" value="Voeg dieetwensen">
        </form>
    </div>
</div>
</body>
</html>
