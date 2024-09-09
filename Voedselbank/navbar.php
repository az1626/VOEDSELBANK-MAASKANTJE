<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Improved Navbar</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #333;
            padding: 1rem;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar-logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar-menu {
            display: flex;
            list-style-type: none;
        }

        .navbar-menu li {
            margin-left: 1rem;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .navbar-menu a:hover {
            background-color: #555;
        }

        .navbar-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .navbar-menu {
                display: none;
                flex-direction: column;
                width: 100%;
                position: absolute;
                top: 60px;
                left: 0;
                background-color: #333;
                padding: 1rem;
            }

            .navbar-menu.active {
                display: flex;
            }

            .navbar-menu li {
                margin: 0.5rem 0;
            }

            .navbar-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a class="navbar-logo">Voedselbank Maaskantje</a>
        <button class="navbar-toggle" id="navbar-toggle">☰</button>
        <ul class="navbar-menu" id="navbar-menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] == 1): ?>
                    <li><a href="medewerkers.php">Medewerkers</a></li>
                <?php endif; ?>
                <li><a href="extra.php">Extra</a></li>
                <li><a href="logout.php" class="logout-button">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>


    <script>
        const navbarToggle = document.getElementById('navbar-toggle');
        const navbarMenu = document.getElementById('navbar-menu');

        navbarToggle.addEventListener('click', () => {
            navbarMenu.classList.toggle('active');
        });
    </script>
</body>
</html>
