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
    $user_id = intval($_GET['id']); // Cast to integer for security

    // Fetch user data
    $sql = "SELECT idGebruikers, Gebruikersnaam, Wachtwoord, Rol, Email FROM gebruikers WHERE idGebruikers = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . htmlspecialchars($conn->error));
    }
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
    $role = intval($_POST['role']); // Ensure role is an integer
    $password = $_POST['password'];

    if (!empty($password)) {
        // If a new password is provided, hash it
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE gebruikers SET Email = ?, Gebruikersnaam = ?, Rol = ?, Wachtwoord = ? WHERE idGebruikers = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ssisi", $email, $naam, $role, $hashed_password, $user_id);
    } else {
        // If no new password is provided, do not update the password
        $sql = "UPDATE gebruikers SET Email = ?, Gebruikersnaam = ?, Rol = ? WHERE idGebruikers = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error preparing statement: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("ssii", $email, $naam, $role, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: medewerkers.php?updated=success");
        exit;
    } else {
        echo "Error updating record: " . htmlspecialchars($stmt->error);
    }

    $stmt->close();
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

        input[type="text"], input[type="email"], input[type="password"], select {
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
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Edit Medewerker</h1>

    <form method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>

        <label for="naam">Username:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($user['Gebruikersnaam']); ?>" required>

        <label for="password">New Password (leave blank to keep current password):</label>
        <input type="password" id="password" name="password">

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="0" <?php echo ($user['Rol'] == 0) ? 'selected' : ''; ?>>Klant</option>
            <option value="1" <?php echo ($user['Rol'] == 1) ? 'selected' : ''; ?>>Admin</option>
            <option value="2" <?php echo ($user['Rol'] == 2) ? 'selected' : ''; ?>>Medewerker</option>
            <option value="3" <?php echo ($user['Rol'] == 3) ? 'selected' : ''; ?>>Vrijwilliger</option>
        </select>

        <button type="submit">Save Changes</button>
    </form>

    <div class="back-link">
        <a href="medewerkers.php">Back to Manage Medewerkers</a>
    </div>
</div>
</body>
</html>
