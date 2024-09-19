<?php
// Fetch session variables
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Niet ingelogd';
$user_email = isset($_SESSION['Email']) ? $_SESSION['Email'] : 'Niet beschikbaar';
$user_name = isset($_SESSION['Gebruikersnaam']) ? $_SESSION['Gebruikersnaam'] : 'Niet beschikbaar';
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Niet beschikbaar';
$profile_pic = isset($_SESSION['profile_pic']) ? $_SESSION['profile_pic'] : 'afbeeldingen/defaultacc.jpg';

// Determine role description and dashboard link
switch ($user_role) {
    case 0:
        $role_description = 'Klant';
        $dashboard_link = 'klantdashboard.php';  // Redirect klant to klantdashboard.php
        break;
    case 1:
        $role_description = 'Admin';
        $dashboard_link = 'dashboard.php';  // Redirect admin to dashboard.php
        break;
    case 2:
        $role_description = 'Medewerker';
        $dashboard_link = 'dashboard.php';  // Redirect medewerker to dashboard.php
        break;
    case 3:
        $role_description = 'Vrijwilliger';
        $dashboard_link = 'dashboard.php';  // Redirect vrijwilliger to dashboard.php
        break;
    default:
        $role_description = 'Unknown Role';
        $dashboard_link = 'dashboard.php';  // Default to dashboard.php
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar with Profile Modal</title>
    <link rel="stylesheet" href="CSS/navbar.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a class="navbar-logo">Voedselbank Maaskantje</a>
        <button class="navbar-toggle" id="navbar-toggle">☰</button>
        <ul class="navbar-menu" id="navbar-menu">
            <li><a href="<?php echo htmlspecialchars($dashboard_link); ?>">Dashboard</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] == 1): ?>
                    <li><a href="medewerkers.php">Medewerkers</a></li>
                <?php endif; ?>
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
        <p>Naam: <?php echo htmlspecialchars($user_name); ?></p>
        <p>ID: <?php echo htmlspecialchars($user_id); ?></p>
        <p>Rol: <?php echo htmlspecialchars($role_description); ?></p>
        <button class="logout-button" onclick="window.location.href='logout.php'">Logout</button>
        <button class="change-password-button" id="change-password-button">Verander Wachtwoord</button>

        <!-- Password change section -->
        <div class="password-change-section" id="password-change-section">
            <form id="change-password-form" method="post" action="change_password.php">
                <label for="old-password">Oude wachtwoord:</label>
                <input type="password" id="old-password" name="old-password" required>

                <label for="new-password">Nieuwe wachtwoord:</label>
                <input type="password" id="new-password" name="new-password" required>

                <label for="confirm-password">Bevestig Nieuwe wachtwoord:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>

                <button type="submit" class="save-button">Opslaan</button>
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
