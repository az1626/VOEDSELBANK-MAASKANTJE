<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin, manager, or volunteer role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $_SESSION['role'] != 3)) {
    header("Location: login.php");
    exit;
}

// Handle form submission for adding and updating dietary wishes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'])) {
        // Update dietary wish
        $id = $_POST['id'];
        $naam = $_POST['naam'];

        $sql = "UPDATE dieetwensen SET naam=? WHERE idDieetwensen=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $naam, $id);

        if ($stmt->execute()) {
            $success_message = "Dietary wish updated successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Add new dietary wish
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
}

// Handle deletion of dietary wish
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql = "DELETE FROM dieetwensen WHERE idDieetwensen=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $success_message = "Dietary wish deleted successfully!";
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
    <style>
        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0, 0, 0); 
            background-color: rgba(0, 0, 0, 0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
            max-width: 500px;
            border-radius: 5px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
                        
                        <?php if ($_SESSION['role'] == 1): ?>
                      <button class="edit-btn" data-id="<?php echo htmlspecialchars($row['idDieetwensen']); ?>" data-name="<?php echo htmlspecialchars($row['naam']); ?>">Edit</button>
                        <a href="?delete_id=<?php echo urlencode($row['idDieetwensen']); ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                      <?php else: ?>
                      <span>Alleen bekijken</span>
                     <?php endif; ?>
</td>
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

<!-- Modal for Editing -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Dietary Wish</h2>
        <form id="editForm" action="extra.php" method="post">
            <label for="editNaam">Name:</label>
            <input type="text" id="editNaam" name="naam" required>
            <input type="hidden" id="editId" name="id">
            <input type="submit" value="Update Dietary Wish">
        </form>
    </div>
</div>
<script src="JS/extra.js"></script>
</body>
</html>
