<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Gezinnen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
        }

        .navbar {
            width: 100%;
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
        }

        .navbar a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 400px;
            width: 100%;
            margin-top: 80px; /* Adjust to add space below the fixed navbar */
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .error, .success {
            text-align: center;
            font-size: 14px;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"], input[type="number"], input[type="email"], input[type="tel"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container">
        <h1>Add New Gezinnen</h1>
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
            
            <label for="volwassenen">Volwassenen:</label>
            <input type="number" id="volwassenen" name="volwassenen" required min="0">
            
            <label for="kinderen">Kinderen:</label>
            <input type="number" id="kinderen" name="kinderen" required min="0">
            
            <label for="postcode">Postcode:</label>
            <input type="text" id="postcode" name="postcode" required>
            
            <label for="mail">Email:</label>
            <input type="email" id="mail" name="mail" required>
            
            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="tel" id="telefoonnummer" name="telefoonnummer" required>
            
            <label for="wensen">Wensen:</label>
            <input type="text" id="wensen" name="wensen">
            
            <label for="pakket">Pakket:</label>
            <input type="text" id="pakket" name="pakket">
            
            <input type="submit" value="Add Gezinnen">
        </form>
    </div>
</body>
</html>
