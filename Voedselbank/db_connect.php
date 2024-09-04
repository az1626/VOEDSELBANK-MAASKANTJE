<?php
$servername = "localhost";
$username = "root";
$password = "Alfalion2004";
$database = "voedselbank_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
