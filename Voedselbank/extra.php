<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin, manager, or volunteer role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit;
}

// Handle form submission (only for admins and managers, not volunteers)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2)) {
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
$sql = "SELECT idDieetwensen, naam FROM dieetwensen";
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
    <link rel="stylesheet" href="CSS/extra.css">
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
                            <?php if ($_SESSION['role'] == 1): ?>
                                <a href="edit_extra.php?id=<?php echo urlencode($row['idDieetwensen']); ?>">Edit</a>
                                <a href="delete_extra.php?id=<?php echo urlencode($row['idDieetwensen']); ?>" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                            <?php else: ?>
                                <span>Alleen bekijken</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">Geen dieetwensen gevonden.</td>
                </tr>
            <?php endif; ?>
        </table>

        <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2): ?>
            <form action="extra.php" method="post">
                <label for="naam">Naam:</label>
                <input type="text" id="naam" name="naam" required>

                <input type="submit" value="Voeg dieetwensen">
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
