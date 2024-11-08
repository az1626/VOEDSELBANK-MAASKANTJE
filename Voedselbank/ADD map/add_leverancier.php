<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 1 && $_SESSION['role'] != 2)) {
    header("Location: login.php");
    exit;
}

// Handle the form submission to add a new supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $naam = $_POST['naam'];
    $adres = $_POST['adres'];
    $contactpersoon = $_POST['contactpersoon'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $email = $_POST['email'];
    $eerstevolgende_levering = $_POST['eerstevolgende_levering'];

    // Prepare the SQL statement to insert the new supplier
    $sql = "INSERT INTO Leveranciers (naam, adres, contactpersoon, telefoonnummer, email, eerstevolgende_levering) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $naam, $adres, $contactpersoon, $telefoonnummer, $email, $eerstevolgende_levering);

    // Execute the statement and handle the result
    if ($stmt->execute()) {
        // Redirect to leveranciers.php with a success message
        header("Location: leveranciers.php?added=success");
        exit;
    } else {
        $error_message = "Error: " . htmlspecialchars($stmt->error);
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
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
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Voeg nieuw leverancier</h1>
    <?php
    if (isset($error_message)) {
        echo "<div class='error-message'>{$error_message}</div>";
    }
    ?>
    <form action="add_leverancier.php" method="POST">
        <label for="naam">Bedrijf:</label>
        <input type="text" id="naam" name="naam" required>

        <label for="adres">Adres:</label>
        <input type="text" id="adres" name="adres" required>

        <label for="contactpersoon">Contact Persoon:</label>
        <input type="text" id="contactpersoon" name="contactpersoon" required>

        <label for="telefoonnummer">Telefoon:</label>
        <input type="text" id="telefoonnummer" name="telefoonnummer" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="eerstevolgende_levering">Volgende Bezorging:</label>
        <input type="datetime-local" id="eerstevolgende_levering" name="eerstevolgende_levering" required>

        <button type="submit">Voeg Leverancier</button>
    </form>
</div>
</body>
</html>