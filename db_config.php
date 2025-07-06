<?php
$servername = "localhost"; // Usually 'localhost'
$username = "root";        // Your MySQL username
$password = "";            // Your MySQL password (often empty for XAMPP/WAMP default)
$dbname = "login_db";      // The database name you created

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully"; // Uncomment for testing connection
?>