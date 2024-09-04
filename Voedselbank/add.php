<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Gezinnen</title>
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
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        form {
            display: grid;
            gap: 10px;
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
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
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
        <form action="add_action.php" method="post">
            <label for="naam">Naam:</label>
            <input type="text" id="naam" name="naam" required>
            
            <label for="volwassenen">Volwassenen:</label>
            <input type="number" id="volwassenen" name="volwassenen" required>
            
            <label for="kinderen">Kinderen:</label>
            <input type="number" id="kinderen" name="kinderen" required>
            
            <label for="postcode">Postcode:</label>
            <input type="text" id="postcode" name="postcode" required>
            
            <label for="mail">Email:</label>
            <input type="email" id="mail" name="mail" required>
            
            <label for="telefoonnummer">Telefoonnummer:</label>
            <input type="text" id="telefoonnummer" name="telefoonnummer" required>
            
            <label for="wensen">Wensen:</label>
            <input type="text" id="wensen" name="wensen" required>
            
            <label for="pakket">Pakket:</label>
            <input type="text" id="pakket" name="pakket" required>
            
            <input type="submit" value="Add Gezinnen">
        </form>
    </div>
</body>
</html>