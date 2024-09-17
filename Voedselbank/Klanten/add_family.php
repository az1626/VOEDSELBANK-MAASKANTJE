<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Fetch dietary preferences from the database
$sql = "SELECT idDieetwensen, naam FROM Dieetwensen";
$stmt = $conn->prepare($sql);
$stmt->execute();
$dieetwensenResult = $stmt->get_result();
$dieetwensen = [];
while ($row = $dieetwensenResult->fetch_assoc()) {
    $dieetwensen[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg Klanten</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 1.5rem;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="number"],
        input[type="email"] {
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        input[type="submit"] {
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        fieldset {
            border: 1px solid #ddd;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        legend {
            font-weight: bold;
            color: #333;
            padding: 0 0.5rem;
        }
        .error {
            color: #d9534f;
            background-color: #f2dede;
            padding: 1rem;
            border: 1px solid #d9534f;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .success {
            color: #5bc0de;
            background-color: #d9edf7;
            padding: 1rem;
            border: 1px solid #5bc0de;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.5rem;
        }
        .checkbox-group label {
            display: flex;
            align-items: center;
            font-weight: normal;
        }
        .checkbox-group input[type="checkbox"] {
            margin-right: 0.5rem;
        }
        #handmatig_input {
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Voeg Klanten</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo "<p class='success'>" . $_SESSION['success'] . "</p>";
            unset($_SESSION['success']);
        }
        ?>
        <form action="add_action.php" method="post">
            <label for="naam">Naam:</label>
            <input type="text" id="naam" name="naam" required>
            
            <label for="adres">Adres:</label>
            <input type="text" id="adres" name="adres" required>
            
            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="number" id="telefoonnummer" name="telefoonnummer" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="aantal_volwassenen">Aantal Volwassenen:</label>
            <input type="number" id="aantal_volwassenen" name="aantal_volwassenen" required min="0">
            
            <label for="aantal_kinderen">Aantal Kinderen:</label>
            <input type="number" id="aantal_kinderen" name="aantal_kinderen" required min="0">
            
            <label for="aantal_babys">Aantal Babys:</label>
            <input type="number" id="aantal_babys" name="aantal_babys" required min="0">
            
            <fieldset>
    <legend>Dieetwensen</legend>
    <?php foreach ($dieetwensen as $wens): ?>
        <label>
            <input type="checkbox" name="dieetwensen[]" value="<?php echo $wens['idDieetwensen']; ?>">
            <?php echo htmlspecialchars($wens['naam']); ?>
        </label><br>
    <?php endforeach; ?>
    
    <label for="handmatig_input">Handmatig dieetwens toevoegen:</label>
    <input type="text" id="handmatig_input" name="handmatig_input" placeholder="Voer dieetwens in">
</fieldset>


            <input type="submit" value="Voeg Klanten">
        </form>
    </div>
</body>
</html>