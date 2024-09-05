<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
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
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        h1 {
            text-align: center;
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
        input[type="email"],
        input[type="password"],
        select {
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
        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23606770' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.7rem top 50%;
            background-size: 0.65rem auto;
        }
    </style>
</head>
<body>
<div class="header">
    <img src="afbeeldingen/pngtree-fast-food-logo-png-image_5763171.png" alt="Logo">
    <h2>Voedselbank Maaskantje</h2>
</div>
    <div class="container">
        <h1>Register</h1>
        <form action="register_action.php" method="post">
            <label for="naam">Name:</label>
            <input type="text" name="naam" id="naam" required>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
            <label for="telefoonnummer">Phone Number:</label>
            <input type="text" name="telefoonnummer" id="telefoonnummer" required>
            <label for="role">Role:</label>
            <select name="role" id="role">
                <option value="0">User</option>
                <option value="1">Admin</option>
            </select>
            <input type="submit" value="Register">
        </form>
        <a href="login.php" class="login-link">Already got an account? Login here.</a>
    </div>
</body>
</html>