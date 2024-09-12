<?php
$servername = "localhost";
$username = "root";
<<<<<<< HEAD
$password = "AymanZerDB";
=======
$password = "";
>>>>>>> 8c56bb66dfebecf2d4cea75aed2e5702f52c5554
$database = "mydb";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
