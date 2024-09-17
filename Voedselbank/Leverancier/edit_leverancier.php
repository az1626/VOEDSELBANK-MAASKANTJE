<?php
session_start();
include '../db_connect.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Check if an ID is provided for editing
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: leveranciers.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch the supplier's current details
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM Leveranciers WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $supplier = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating the supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = $_POST['naam'];
    $adres = $_POST['adres'];
    $contactpersoon = $_POST['contactpersoon'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $email = $_POST['email'];
    $eerstevolgende_levering = $_POST['eerstevolgende_levering'];

    $sql = "UPDATE Leveranciers SET naam = ?, adres = ?, contactpersoon = ?, telefoonnummer = ?, email = ?, eerstevolgende_levering = ? WHERE idLeveranciers = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $naam, $adres, $contactpersoon, $telefoonnummer, $email, $eerstevolgende_levering, $id);

    if ($stmt->execute()) {
        header("Location: leveranciers.php?updated=success");
        exit;
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
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
    <title>Bewerk leverancier</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 2rem;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5rem;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="datetime-local"] {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #45a049;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <h1>Bewerk Leverancier</h1>
    <?php
    if (isset($error_message)) {
        echo "<div class='error-message'>{$error_message}</div>";
    }
    ?>
    <form action="edit_leverancier.php?id=<?php echo htmlspecialchars($id); ?>" method="POST">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($supplier['naam']); ?>" required>

        <label for="adres">Adres:</label>
        <input type="text" id="adres" name="adres" value="<?php echo htmlspecialchars($supplier['adres']); ?>" required>

        <label for="contactpersoon">Contact Persoon:</label>
        <input type="text" id="contactpersoon" name="contactpersoon" value="<?php echo htmlspecialchars($supplier['contactpersoon']); ?>" required>

        <label for="telefoonnummer">Telefoonnummer:</label>
        <input type="text" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($supplier['telefoonnummer']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email']); ?>" required>

        <label for="eerstevolgende_levering">Volgende bezorging:</label>
        <input type="datetime-local" id="eerstevolgende_levering" name="eerstevolgende_levering" value="<?php echo htmlspecialchars($supplier['eerstevolgende_levering']); ?>" required>

        <button type="submit">Update Leveranciers</button>
    </form>
</div>
</body>
</html>
