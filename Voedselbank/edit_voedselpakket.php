<?php
session_start();
include 'db_connect.php'; // Include your database connection script

// Check if the user is logged in and has the admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    header("Location: login.php");
    exit;
}

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Function to get a specific voedselpakket by ID
    function getVoedselpakketById($conn, $id) {
        $sql = "SELECT * FROM voedselpakket WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get the voedselpakket details
    $voedselpakket = getVoedselpakketById($conn, $id);

    // Check if the form was submitted for updating the voedselpakket
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_voedselpakket'])) {
        $naam = $_POST['naam'];
        $samenstellingsdatum = $_POST['samenstellingsdatum'];
        $ophaaldatum = $_POST['ophaaldatum'];

        // Function to update an existing voedselpakket
        function updateVoedselpakket($conn, $id, $naam, $samenstellingsdatum, $ophaaldatum) {
            $sql = "UPDATE voedselpakket SET naam = ?, samenstellingsdatum = ?, ophaaldatum = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $naam, $samenstellingsdatum, $ophaaldatum, $id);
            return $stmt->execute();
        }

        // Attempt to update the voedselpakket
        if (updateVoedselpakket($conn, $id, $naam, $samenstellingsdatum, $ophaaldatum)) {
            echo "<script>alert('Voedselpakket successfully updated.'); window.location.href='voedselpakket.php';</script>";
        } else {
            echo "<script>alert('Failed to update voedselpakket.'); window.location.href='edit_voedselpakket.php?id=$id';</script>";
        }
    }
} else {
    // Redirect back if no ID is provided
    header("Location: voedselpakket.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselpakket Bewerken</title>
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --background-color: #f4f4f4;
            --text-color: #333;
            --border-color: #ddd;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 16px;
        }

        button {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Voedselpakket Bewerken</h1>

    <form method="POST" action="">
        <label for="naam">Naam:</label>
        <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($voedselpakket['naam']); ?>" required>

        <label for="samenstellingsdatum">Samenstellingsdatum:</label>
        <input type="date" id="samenstellingsdatum" name="samenstellingsdatum" value="<?php echo htmlspecialchars($voedselpakket['samenstellingsdatum']); ?>" required>

        <label for="ophaaldatum">Ophaaldatum:</label>
        <input type="date" id="ophaaldatum" name="ophaaldatum" value="<?php echo htmlspecialchars($voedselpakket['ophaaldatum']); ?>" required>

        <button type="submit" name="update_voedselpakket">Update</button>
    </form>
</div>
</body>
</html>
