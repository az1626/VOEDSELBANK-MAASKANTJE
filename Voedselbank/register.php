<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="CSS/Register.css">
</head>
<body>
<div class="header">
    <h2>Voedselbank Maaskantje</h2>
</div>
<div class="container">
    <h2>Register</h2>
    <form action="register_action.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="gebruikersnaam">Username:</label>
        <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>

        <label for="wachtwoord">Password:</label>
        <input type="password" id="wachtwoord" name="wachtwoord" required>

        <input type="submit" value="Register">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
