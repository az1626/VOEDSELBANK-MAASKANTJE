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
$sql = "SELECT * FROM families WHERE FamilyID = ?";
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

// Update family data if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naam = $_POST['naam'];
    $volwassenen = intval($_POST['volwassenen']);
    $kinderen = intval($_POST['kinderen']);
    $postcode = $_POST['postcode'];
    $mail = $_POST['mail'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $wensen = $_POST['wensen'];
    $pakket = $_POST['pakket'];

    $sql = "UPDATE families SET Naam = ?, Volwassenen = ?, Kinderen = ?, Postcode = ?, Email = ?, Telefoonnummer = ?, Wensen = ?, Pakket = ? WHERE FamilyID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiissssi", $naam, $volwassenen, $kinderen, $postcode, $mail, $telefoonnummer, $wensen, $pakket, $id);

    if ($stmt->execute()) {
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
            max-width: 600px;
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
        form {
            display: grid;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="email"] {
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
    <h1>Edit Family Data</h1>
    <?php
    if (isset($error_message)) {
        echo "<div class='message error'>$error_message</div>";
    }
    ?>
    <form action="edit_family.php?id=<?php echo $id; ?>" method="post">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($data['Naam']); ?>" required>

        <label for="volwassenen">Volwassenen:</label>
        <input type="number" id="volwassenen" name="volwassenen" value="<?php echo $data['Volwassenen']; ?>" required>

        <label for="kinderen">Kinderen:</label>
        <input type="number" id="kinderen" name="kinderen" value="<?php echo $data['Kinderen']; ?>" required>

        <label for="postcode">Postcode:</label>
        <input type="text" id="postcode" name="postcode" value="<?php echo htmlspecialchars($data['Postcode']); ?>" required>

        <label for="mail">Email:</label>
        <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($data['Email']); ?>" required>

        <label for="telefoonnummer">Telefoonnummer:</label>
        <input type="text" id="telefoonnummer" name="telefoonnummer" value="<?php echo htmlspecialchars($data['Telefoonnummer']); ?>" required>

        <label for="wensen">Wensen:</label>
        <input type="text" id="wensen" name="wensen" value="<?php echo htmlspecialchars($data['Wensen']); ?>" required>

        <label for="pakket">Pakket:</label>
        <input type="text" id="pakket" name="pakket" value="<?php echo htmlspecialchars($data['Pakket']); ?>" required>

        <input type="submit" value="Update">
    </form>
</div>
</body>
</html>
