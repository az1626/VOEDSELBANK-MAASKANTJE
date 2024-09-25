<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="CSS/Login.css">
</head>
<body>
<div class="header">
    <h2>Voedselbank Maaskantje</h2>
</div>
<div class="container">
    <h2>Login</h2>
    <form action="login_action.php" method="POST">
        <label for="gebruikersnaam">Username:</label>
        <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>

        <label for="wachtwoord">Password:</label>
        <input type="password" id="wachtwoord" name="wachtwoord" required>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p class="error">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>

        <input type="submit" value="Login">
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
