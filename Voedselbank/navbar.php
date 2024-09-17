<?php
// Fetch session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not logged in';
$user_email = isset($_SESSION['Email']) ? $_SESSION['Email'] : 'Not available';
$user_name = isset($_SESSION['Gebruikersnaam']) ? $_SESSION['Gebruikersnaam'] : 'Not available';
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Not available';
$profile_pic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'afbeeldingen/defaultacc.jpg';

// Determine role description
switch ($user_role) {
    case 0:
        $role_description = 'Klant';
        break;
    case 1:
        $role_description = 'Admin';
        break;
    case 2:
        $role_description = 'Medewerker';
        break;
    case 3:
        $role_description = 'Vrijwilliger';
        break;
    default:
        $role_description = 'Unknown Role';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar with Profile Modal</title>
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
            position: relative;
            z-index: 10;
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
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

        .profile-circle {
            position: relative;
            display: flex;
            align-items: center;
            margin-left: 1rem;
            cursor: pointer;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .modal-content p {
            margin-bottom: 20px;
            font-size: 18px;
            color: #333;
        }

        .modal-content .logout-button,
        .modal-content .change-password-button,
        .modal-content .save-button {
            background-color: green;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 10px;
        }

        .modal-content .logout-button:hover,
        .modal-content .change-password-button:hover,
        .modal-content .save-button:hover {
            background-color: #c9302c;
        }

        .modal-content .cancel-button {
            background-color: #d9534f;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-content .cancel-button:hover {
            background-color: #c9302c;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
        }

        .modal-content form label {
            margin: 10px 0 5px;
            font-weight: bold;
        }

        .modal-content form input[type="password"],
        .modal-content form input[type="email"],
        .modal-content form input[type="text"] {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        .password-change-section {
            display: none;
            margin-top: 20px;
        }

        .password-change-section.active {
            display: block;
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

        <div class="profile-circle" id="profile-circle">
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile" class="profile-img">
        </div>
    </div>
</nav>

<div class="modal" id="profile-modal">
    <div class="modal-content">
        <p>Name: <?php echo htmlspecialchars($user_name); ?></p>
        <p>ID: <?php echo htmlspecialchars($user_id); ?></p>
        <p>Role: <?php echo htmlspecialchars($role_description); ?></p>
        <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
        <button class="change-password-button" id="change-password-button">Change Password</button>

        <!-- Password change section -->
        <div class="password-change-section" id="password-change-section">
            <form id="change-password-form" method="post" action="change_password.php">
                <label for="old-password">Old Password:</label>
                <input type="password" id="old-password" name="old-password" required>

                <label for="new-password">New Password:</label>
                <input type="password" id="new-password" name="new-password" required>

                <label for="confirm-password">Confirm New Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>

                <button type="submit" class="save-button">Save Changes</button>
                <button type="button" class="cancel-button" id="cancel-button">Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMenu = document.getElementById('navbar-menu');
    const profileCircle = document.getElementById('profile-circle');
    const profileModal = document.getElementById('profile-modal');
    const cancelButton = document.getElementById('cancel-button');
    const changePasswordButton = document.getElementById('change-password-button');
    const passwordChangeSection = document.getElementById('password-change-section');

    navbarToggle.addEventListener('click', () => {
        navbarMenu.classList.toggle('active');
    });

    profileCircle.addEventListener('click', () => {
        profileModal.style.display = 'flex';
    });

    window.addEventListener('click', (event) => {
        if (event.target === profileModal) {
            profileModal.style.display = 'none';
        }
    });

    changePasswordButton.addEventListener('click', () => {
        passwordChangeSection.classList.toggle('active');
    });

    cancelButton.addEventListener('click', () => {
        passwordChangeSection.classList.remove('active');
    });
</script>
</body>
</html>
