<?php
session_start();
include 'db_connect.php';

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Fetch family data if ID is present
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch family data for the given ID
$sql = "SELECT * FROM Klanten WHERE idKlanten = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    echo "Family not found.";
    exit;
}

// Fetch existing dietary preferences for the client
$sql = "SELECT Dieetwensen_idDieetwensen FROM Klanten_has_Dieetwensen WHERE Klanten_idKlanten = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$dieetwensenResult = $stmt->get_result();
$selectedDieetwensen = [];
while ($row = $dieetwensenResult->fetch_assoc()) {
    $selectedDieetwensen[] = $row['Dieetwensen_idDieetwensen'];
}
$stmt->close();

// Fetch all dietary preferences
$sql = "SELECT idDieetwensen, naam FROM Dieetwensen";
$stmt = $conn->prepare($sql);
$stmt->execute();
$dieetwensenResult = $stmt->get_result();
$allDieetwensen = [];
while ($row = $dieetwensenResult->fetch_assoc()) {
    $allDieetwensen[] = $row;
}
$stmt->close();

// Update family and dietary preferences if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naam = $_POST['naam'];
    $adres = $_POST['adres'];
    $telefoonnummer = intval($_POST['telefoonnummer']);
    $email = $_POST['email'];
    $aantal_volwassenen = intval($_POST['aantal_volwassenen']);
    $aantal_kinderen = intval($_POST['aantal_kinderen']);
    $aantal_babys = intval($_POST['aantal_babys']);
    $selectedDieetwensen = isset($_POST['dieetwensen']) ? $_POST['dieetwensen'] : [];
    $handmatigDieetwens = $_POST['handmatig_input'];

    // Update family data
    $sql = "UPDATE Klanten SET naam = ?, adres = ?, telefoonnummer = ?, email = ?, aantal_volwassenen = ?, aantal_kinderen = ?, aantal_babys = ? WHERE idKlanten = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiisi", $naam, $adres, $telefoonnummer, $email, $aantal_volwassenen, $aantal_kinderen, $aantal_babys, $id);
    
    if ($stmt->execute()) {
        // Update dietary preferences
        $sql = "DELETE FROM Klanten_has_Dieetwensen WHERE Klanten_idKlanten = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        foreach ($selectedDieetwensen as $dieetwensId) {
            $sql = "INSERT INTO Klanten_has_Dieetwensen (Klanten_idKlanten, Dieetwensen_idDieetwensen) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id, $dieetwensId);
            $stmt->execute();
        }
        
        // Handle manual dietary preference
        if (!empty($handmatigDieetwens)) {
            $sql = "INSERT INTO Dieetwensen (naam) SELECT ? FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM Dieetwensen WHERE naam = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $handmatigDieetwens, $handmatigDieetwens);
            $stmt->execute();
            
            $newDieetwensId = $stmt->insert_id;
            
            $sql = "INSERT INTO Klanten_has_Dieetwensen (Klanten_idKlanten, Dieetwensen_idDieetwensen) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id, $newDieetwensId);
            $stmt->execute();
        }
        
        // Redirect to families.php with a success message
        header("Location: families.php?updated=success");
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
    <title>Edit Family Data</title>
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
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.2);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            display: grid;
            gap: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        fieldset {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
        }
        legend {
            font-weight: bold;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
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
    <h1>Edit Family Data</h1>
    <?php
    if (isset($error_message)) {
        echo "<div class='message error'>$error_message</div>";
    }
    ?>
    <form action="edit_family.php?id=<?php echo $id; ?>" method="post">
        <!-- Existing form fields -->
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($data['naam']); ?>" required>

        <label for="adres">Adres:</label>
        <input type="text" id="adres" name="adres" value="<?php echo htmlspecialchars($data['adres']); ?>" required>

        <label for="telefoonnummer">Telefoonnummer:</label>
        <input type="number" id="telefoonnummer" name="telefoonnummer" value="<?php echo $data['telefoonnummer']; ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>

        <label for="aantal_volwassenen">Aantal Volwassenen:</label>
        <input type="number" id="aantal_volwassenen" name="aantal_volwassenen" value="<?php echo $data['aantal_volwassenen']; ?>" required>

        <label for="aantal_kinderen">Aantal Kinderen:</label>
        <input type="number" id="aantal_kinderen" name="aantal_kinderen" value="<?php echo $data['aantal_kinderen']; ?>" required>

        <label for="aantal_babys">Aantal Babys:</label>
        <input type="number" id="aantal_babys" name="aantal_babys" value="<?php echo $data['aantal_babys']; ?>" required>

        <!-- Dietary preferences -->
        <fieldset>
            <legend>Dieetwensen</legend>
            <?php foreach ($allDieetwensen as $wens): ?>
                <label>
                    <input type="checkbox" name="dieetwensen[]" value="<?php echo $wens['idDieetwensen']; ?>"
                    <?php echo in_array($wens['idDieetwensen'], $selectedDieetwensen) ? 'checked' : ''; ?>>
                    <?php echo htmlspecialchars($wens['naam']); ?>
                </label><br>
            <?php endforeach; ?>
            
            <label for="handmatig_input">Handmatig dieetwens toevoegen:</label>
            <input type="text" id="handmatig_input" name="handmatig_input" placeholder="Voer dieetwens in">
        </fieldset>

        <input type="submit" value="Update">
    </form>
</div>
</body>
</html>
