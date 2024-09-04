  <nav class="navbar">
      <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 1): ?>
              <li><a href="manage_users.php">Manage Users</a></li>
              <li><a href="manage_data.php">Manage Data</a></li>
          <?php endif; ?>
          <?php if (isset($_SESSION['user_id'])): ?>
              <li><a href="logout.php" class="logout-button">Logout</a></li>
          <?php else: ?>
              <li><a href="login.php">Login</a></li>
          <?php endif; ?>
      </ul>
  </nav>
