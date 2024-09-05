<?php
global $conn;
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Debugging: print email and password
    // echo "Email: $email<br>";
    // echo "Password: $password<br>";

    // Prepare and execute statement
    $stmt = $conn->prepare("SELECT AccountID, Wachtwoord, role FROM user WHERE Email=?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $role);
        $stmt->fetch();

        // Debugging: print hashed password from database
        // echo "Hashed Password: $hashed_password<br>";

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['role'] = $role;
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email.";
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
