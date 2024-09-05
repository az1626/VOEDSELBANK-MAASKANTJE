<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Check if an ID is provided in the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user data
    $sql = "SELECT * FROM user WHERE AccountID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit;
    }
} else {
    echo "No user ID provided.";
    exit;
}

// Update user data if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $naam = $_POST['naam'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $role = $_POST['role'];

    // Update the user data in the database
    $sql = "UPDATE user SET Email = ?, Naam = ?, Telefoonnummer = ?, role = ? WHERE AccountID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $email, $naam, $telefoonnummer, $role, $user_id);

    if ($stmt->execute()) {
        header("Location: medewerkers.php");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Medewerker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="email"], select {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #45a049;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #4CAF50;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Medewerker</h1>

        <form method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

            <label for="naam">Name:</label>
            <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($user['Naam']); ?>" required>

            <label for="telefoonnummer">Phone Number:</label>
            <input type="text" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($user['Telefoonnummer']); ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="0" <?php echo ($user['role'] == 0) ? 'selected' : ''; ?>>User</option>
                <option value="1" <?php echo ($user['role'] == 1) ? 'selected' : ''; ?>>Admin</option>
            </select>

            <button type="submit">Save Changes</button>
        </form>

        <div class="back-link">
            <a href="medewerkers.php">Back to Manage Medewerkers</a>
        </div>
    </div>
</body>
</html>
