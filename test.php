<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "php -S localhost:8000 <br>PHP IS RUNNING WITHOUT INSTALLING XAMPP";

$servername = "localhost";
$username = "majeostk_farmer_user";
$password = "QsxXa-l3R6k6";
$dbname = "majeostk_farmer";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "<br>";

echo "Connected successfully<br>";



// Close connection
$conn->close();

?>
