<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }
        .header {
            position: absolute;
            top: 10px;
            left: 10px;
            display: flex;
            align-items: center;
        }
        .header img {
            height: 40px;
            margin-right: 10px;
        }
        .header h2 {
            margin: 0;
            color: forestgreen;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        h1 {
            color: forestgreen;
            margin-bottom: 1.5rem;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 0.5rem;
            color: #606770;
        }
        input[type="text"],
        input[type="password"] {
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #dddfe2;
            border-radius: 4px;
            font-size: 1rem;
        }
        input[type="submit"] {
            background-color: forestgreen;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: limegreen;
        }
        .register-link {
            margin-top: 1rem;
            color: blue;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="afbeeldingen/pngtree-fast-food-logo-png-image_5763171.png" alt="Logo">
    <h2>Voedselbank Maaskantje</h2>
</div>
<div class="container">
    <h2>Login</h2>
    <form action="login_action.php" method="POST">
        <label for="gebruikersnaam">Username:</label>
        <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>

        <label for="wachtwoord">Password:</label>
        <input type="password" id="wachtwoord" name="wachtwoord" required>

        <input type="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
